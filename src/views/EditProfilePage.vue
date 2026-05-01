<script setup lang="ts">
import { onBeforeUnmount, onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { Camera, CameraResultType, CameraSource } from "@capacitor/camera";
import { Capacitor } from "@capacitor/core";
import { parsePhoneNumberFromString, type CountryCode } from "libphonenumber-js";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/vue-tel-input.css";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, updateProfile, uploadProfilePicture, type AuthUser } from "@/services/auth";
import { useTheme } from "@/composables/useTheme";

const router = useRouter();
const pageContentId = "edit-profile-content";
const loading = ref(true);
const saving = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const phoneCountry = ref("PK");
const selectedPicture = ref<File | null>(null);
const user = ref<AuthUser | null>(null);
const previewUrl = ref("");
const cameraInput = ref<HTMLInputElement | null>(null);

const form = reactive({
  name: "",
  mobile_phone: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const { isDark, toggleTheme } = useTheme();

onMounted(async () => {
  try {
    user.value = await fetchCurrentUser();
    form.name = user.value.name || "";
    form.mobile_phone = user.value.mobile_number || "";
    form.email = user.value.email || "";
    previewUrl.value = user.value.profile_picture_url || "";
  } catch {
    await logout();
    await router.replace("/home");
  } finally {
    loading.value = false;
  }
});

onBeforeUnmount(() => {
  if (previewUrl.value.startsWith("blob:")) {
    URL.revokeObjectURL(previewUrl.value);
  }
});

function parsePhone(rawPhone: string, countryCode: string): string | null {
  const value = String(rawPhone || "").trim();
  const fallbackCountry = String(countryCode || "PK").toUpperCase();
  const parsed = value.startsWith("+")
    ? parsePhoneNumberFromString(value)
    : parsePhoneNumberFromString(value, { defaultCountry: fallbackCountry as CountryCode });

  if (!parsed?.countryCallingCode || !parsed?.nationalNumber) {
    return null;
  }

  return `+${parsed.countryCallingCode}${parsed.nationalNumber}`;
}

function handlePhoneInput(_phone: string, phoneObject: any): void {
  const nextCountry = phoneObject?.countryCode?.toUpperCase();

  if (nextCountry) {
    phoneCountry.value = nextCountry;
  }
}

function handlePictureChange(event: Event): void {
  const target = event.target;

  if (!(target instanceof HTMLInputElement)) {
    return;
  }

  if (previewUrl.value.startsWith("blob:")) {
    URL.revokeObjectURL(previewUrl.value);
  }

  selectedPicture.value = target.files?.[0] || null;
  previewUrl.value = selectedPicture.value ? URL.createObjectURL(selectedPicture.value) : (user.value?.profile_picture_url || "");
}

function openGalleryPicker(): void {
  const input = document.getElementById("edit-gallery-input");

  if (input instanceof HTMLInputElement) {
    input.click();
  }
}

async function captureFromCamera(): Promise<void> {
  const isNative = Capacitor.isNativePlatform();
  errorMessage.value = "";

  try {
    if (isNative) {
      const existingPermission = await Camera.checkPermissions();
      const needsRequest =
        existingPermission.camera !== "granted" ||
        existingPermission.photos === "prompt" ||
        existingPermission.photos === "prompt-with-rationale" ||
        existingPermission.photos === "denied";

      if (needsRequest) {
        const askedPermission = await Camera.requestPermissions({ permissions: ["camera", "photos"] });

        if (askedPermission.camera !== "granted") {
          errorMessage.value = "Camera permission is not granted. Please allow camera access and try again.";
          return;
        }
      }
    }

    const photo = await Camera.getPhoto({
      source: CameraSource.Camera,
      resultType: CameraResultType.Uri,
      quality: 85,
    });

    if (!photo.webPath) {
      return;
    }

    if (previewUrl.value.startsWith("blob:")) {
      URL.revokeObjectURL(previewUrl.value);
    }

    selectedPicture.value = await webPathToFile(photo.webPath, `profile_${Date.now()}.jpg`);
    previewUrl.value = URL.createObjectURL(selectedPicture.value);
  } catch {
    if (!isNative) {
      // Browser fallback where native camera plugin is unavailable.
      cameraInput.value?.click();
      return;
    }

    errorMessage.value = "Could not open native camera. Please allow camera permission and try again.";
  }
}

async function webPathToFile(webPath: string, filename: string): Promise<File> {
  const response = await fetch(webPath);
  const blob = await response.blob();
  return new File([blob], filename, { type: blob.type || "image/jpeg" });
}

async function submitProfile(): Promise<void> {
  saving.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const parsedMobile = parsePhone(form.mobile_phone, phoneCountry.value);

    if (!parsedMobile) {
      errorMessage.value = "Please enter a valid mobile number with country code.";
      return;
    }

    user.value = await updateProfile({
      name: form.name.trim(),
      mobile_number: parsedMobile,
      email: form.email.trim() || null,
      password: form.password || undefined,
      password_confirmation: form.password_confirmation || undefined,
    });

    if (selectedPicture.value) {
      user.value = await uploadProfilePicture(selectedPicture.value);
      selectedPicture.value = null;
      previewUrl.value = user.value.profile_picture_url || "";
    }

    form.name = user.value.name || "";
    form.mobile_phone = user.value.mobile_number || "";
    form.email = user.value.email || "";
    form.password = "";
    form.password_confirmation = "";

    successMessage.value = "Profile updated successfully.";
  } catch (error: any) {
    const fallback = "Could not update profile. Please try again.";
    const validationErrors = error?.response?.data?.errors as Record<string, string[]> | undefined;
    const firstValidationError = validationErrors
      ? Object.values(validationErrors)[0]?.[0]
      : null;

    errorMessage.value =
      (firstValidationError as string | null) || error?.response?.data?.message || fallback;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Edit Profile" :content-id="pageContentId" />

    <ion-content class="ion-padding profile-content">
      <div class="profile-card">
        <h2>Edit Profile</h2>
        <p v-if="successMessage" class="success-text">{{ successMessage }}</p>
        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <form v-if="!loading" class="form-grid" @submit.prevent="submitProfile">
          <ion-item>
            <ion-label position="stacked">Full Name</ion-label>
            <input v-model="form.name" class="native-input" required />
          </ion-item>

          <ion-item class="phone-item" lines="none">
            <ion-label position="stacked">Mobile Number</ion-label>
            <VueTelInput
              v-model="form.mobile_phone"
              mode="national"
              default-country="PK"
              :auto-default-country="false"
              :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
              :input-options="{ placeholder: 'Type mobile number', showDialCode: false }"
              @on-input="handlePhoneInput"
              valid-characters-only
            />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Email (Optional)</ion-label>
            <input v-model="form.email" class="native-input" type="email" />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Profile Picture (Optional)</ion-label>
            <div class="picture-actions">
              <button type="button" class="picture-action-btn" @click="captureFromCamera">
                Capture From Camera
              </button>
              <button type="button" class="picture-action-btn" @click="openGalleryPicker">
                Upload From Gallery
              </button>
            </div>
            <p v-if="selectedPicture" class="picture-name">Selected: {{ selectedPicture.name }}</p>
            <input
              id="edit-camera-input"
              ref="cameraInput"
              class="visually-hidden-file-input"
              type="file"
              accept="image/*"
              capture="environment"
              @change="handlePictureChange"
            />
            <input
              id="edit-gallery-input"
              class="visually-hidden-file-input"
              type="file"
              accept="image/*"
              @change="handlePictureChange"
            />
          </ion-item>

          <img v-if="previewUrl" :src="previewUrl" class="preview-image" alt="Profile preview" />

          <ion-item>
            <ion-label position="stacked">New Password (Optional)</ion-label>
            <input v-model="form.password" class="native-input" type="password" minlength="8" />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Confirm New Password</ion-label>
            <input
              v-model="form.password_confirmation"
              class="native-input"
              type="password"
              minlength="8"
            />
          </ion-item>

          <!-- Dark Mode Toggle -->
          <div class="theme-row">
            <div class="theme-row__label">
              <span class="theme-row__icon">{{ isDark ? '🌙' : '☀️' }}</span>
              <div>
                <p class="theme-row__title">Appearance</p>
                <p class="theme-row__sub">Currently: <strong>{{ isDark ? 'Dark Mode' : 'Light Mode' }}</strong></p>
              </div>
            </div>
            <button type="button" class="theme-toggle-btn" :class="{ 'theme-toggle-btn--active': isDark }" @click="toggleTheme">
              <span class="theme-toggle-knob" />
            </button>
          </div>

          <ion-button expand="block" type="submit" :disabled="saving">
            {{ saving ? "Saving..." : "Save Changes" }}
          </ion-button>
        </form>

        <p v-else>Loading profile...</p>
      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.profile-content {
  --padding-bottom: 84px;
}

.profile-card {
  max-width: 560px;
  margin: 12px auto;
  background: var(--app-surface-color);
  border-radius: var(--app-card-radius);
  padding: 16px;
  box-shadow: 0 8px 24px rgba(var(--ion-text-color-rgb), 0.1);
}

.form-grid {
  display: grid;
  gap: 10px;
  margin-top: 12px;
}

.picture-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  margin-top: 8px;
}

.picture-action-btn {
  border: 1px solid var(--app-muted-border-color);
  background: var(--app-surface-color);
  color: var(--ion-text-color);
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 13px;
  text-align: center;
  cursor: pointer;
}

.picture-name {
  margin: 8px 0 0;
  color: var(--app-muted-text-color);
  font-size: 12px;
}

.visually-hidden-file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

.native-input {
  width: 100%;
  border: 0;
  background: transparent;
  color: var(--ion-text-color);
  font: inherit;
  padding: 10px 0 8px;
}

.native-input:focus {
  outline: none;
}

.preview-image {
  width: 92px;
  height: 92px;
  object-fit: cover;
  border-radius: 999px;
  border: 2px solid var(--app-muted-border-color);
  margin: 4px auto 6px;
}

.error-text {
  color: var(--ion-color-danger);
}

.success-text {
  color: #0f766e;
}

.phone-item {
  position: relative;
  overflow: visible;
  z-index: 20;
  --border-color: transparent;
  --border-width: 0;
  --inner-border-color: transparent;
  --inner-border-width: 0;
  --inner-box-shadow: none;
  --highlight-height: 0;
}

.phone-item::part(native) {
  overflow: visible;
  border: 0 !important;
  box-shadow: none !important;
}

:deep(.vti) {
  width: 100%;
  border: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
  outline: none !important;
}

:deep(.vti__input) {
  flex: 1;
  min-width: 0;
  border: 0 !important;
  background: transparent !important;
  color: var(--ion-text-color) !important;
  font-size: 16px;
  padding: 10px 0 8px !important;
}

:deep(.vti__dropdown) {
  background: transparent !important;
  border: 0 !important;
}

:deep(.vti__selection) {
  border: 0 !important;
  box-shadow: none !important;
}

:deep(.vti__dropdown-list) {
  border: 1px solid var(--app-muted-border-color);
  border-radius: 10px;
  background: var(--app-surface-color);
  box-shadow: 0 12px 28px rgba(var(--ion-text-color-rgb), 0.18);
  z-index: 2147483647 !important;
}

:deep(.vti__search_box) {
  border: 1px solid var(--app-muted-border-color) !important;
  border-radius: 8px;
}

.theme-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 0;
  border-top: 1px solid var(--app-muted-border-color);
  margin-top: 8px;
}

.theme-row__label {
  display: flex;
  align-items: center;
  gap: 12px;
}

.theme-row__icon {
  font-size: 24px;
}

.theme-row__title {
  margin: 0;
  font-weight: 600;
  color: var(--ion-text-color);
  font-size: 15px;
}

.theme-row__sub {
  margin: 2px 0 0;
  font-size: 12px;
  color: var(--app-muted-text-color);
}

.theme-toggle-btn {
  width: 52px;
  height: 28px;
  border-radius: 999px;
  background: var(--app-muted-border-color);
  border: none;
  position: relative;
  cursor: pointer;
  transition: background 0.25s;
  flex-shrink: 0;
}

.theme-toggle-btn--active {
  background: var(--ion-color-primary);
}

.theme-toggle-knob {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: #fff;
  transition: transform 0.25s;
  display: block;
}

.theme-toggle-btn--active .theme-toggle-knob {
  transform: translateX(24px);
}
</style>




<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { Camera, CameraResultType, CameraSource } from "@capacitor/camera";
import { Capacitor } from "@capacitor/core";
import { login, register } from "@/services/auth";
import { parsePhoneNumberFromString } from "libphonenumber-js";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/vue-tel-input.css";

const router = useRouter();
const mode = ref("login");
const loginMethod = ref("mobile");
const loading = ref(false);
const errorMessage = ref("");
const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const showRegisterPasswordConfirmation = ref(false);

const loginForm = reactive({
  mobile_phone: "",
  email: "",
  password: "",
});

const registerForm = reactive({
  name: "",
  mobile_phone: "",
  email: "",
  password: "",
  password_confirmation: "",
  profile_picture: null,
});

const loginCountry = ref("PK");
const registerCountry = ref("PK");
const registerCameraInput = ref(null);

async function submitLogin(event) {
  loading.value = true;
  errorMessage.value = "";

  try {
    const form = event.currentTarget;
    const formData = form instanceof HTMLFormElement ? new FormData(form) : null;
    const password = String(formData?.get("password") || loginForm.password || "");
    const loginWith = loginMethod.value;

    loginForm.password = password;

    if (loginWith === "mobile") {
      const parsedPhone = parsePhoneFields(loginForm.mobile_phone, loginCountry.value);

      if (!parsedPhone) {
        errorMessage.value = "Please enter a valid mobile number with country code.";
        return;
      }

      await login({
        login_with: "mobile",
        country_code: parsedPhone.country_code,
        mobile_number: parsedPhone.mobile_number,
        password,
      });
    } else {
      const email = String(formData?.get("email") || loginForm.email || "").trim();
      loginForm.email = email;

      await login({
        login_with: "email",
        email,
        password,
      });
    }

    await redirectToDashboard();
  } catch (error) {
    errorMessage.value = extractError(error);
  } finally {
    loading.value = false;
  }
}

async function submitRegister(event) {
  loading.value = true;
  errorMessage.value = "";

  try {
    const form = event.currentTarget;
    const formData = form instanceof HTMLFormElement ? new FormData(form) : null;
    const name = String(formData?.get("name") || registerForm.name || "").trim();
    const email = String(formData?.get("email") || registerForm.email || "").trim();
    const password = String(formData?.get("password") || registerForm.password || "");
    const passwordConfirmation = String(
      formData?.get("password_confirmation") || registerForm.password_confirmation || ""
    );
    const parsedPhone = parsePhoneFields(registerForm.mobile_phone, registerCountry.value);

    if (!parsedPhone) {
      errorMessage.value = "Please enter a valid mobile number with country code.";
      return;
    }

    registerForm.name = name;
    registerForm.email = email;
    registerForm.password = password;
    registerForm.password_confirmation = passwordConfirmation;

    await register({
      name,
      country_code: parsedPhone.country_code,
      mobile_number: parsedPhone.mobile_number,
      email: email || undefined,
      password,
      password_confirmation: passwordConfirmation,
      profile_picture: registerForm.profile_picture || undefined,
    });

    await redirectToDashboard();
  } catch (error) {
    errorMessage.value = extractError(error);
  } finally {
    loading.value = false;
  }
}

async function redirectToDashboard() {
  await router.replace("/dashboard");

  if (router.currentRoute.value.path !== "/dashboard") {
    window.location.assign("/dashboard");
  }
}

function extractError(error) {
  const fallback = "Could not complete request. Please try again.";
  const firstValidationError = error?.response?.data?.errors
    ? Object.values(error.response.data.errors)[0]?.[0]
    : null;

  return firstValidationError || error?.response?.data?.message || fallback;
}

function parsePhoneFields(rawPhone, countryCode) {
  const value = String(rawPhone || "").trim();
  const fallbackCountry = String(countryCode || "PK").toUpperCase();
  const parsed = value.startsWith("+")
    ? parsePhoneNumberFromString(value)
    : parsePhoneNumberFromString(value, fallbackCountry);

  if (!parsed?.countryCallingCode || !parsed?.nationalNumber) {
    return null;
  }

  return {
    country_code: `+${parsed.countryCallingCode}`,
    mobile_number: parsed.nationalNumber,
  };
}

function handleLoginPhoneInput(_phone, phoneObject) {
  const nextCountry = phoneObject?.countryCode?.toUpperCase();

  if (nextCountry) {
    loginCountry.value = nextCountry;
  }
}

function handleRegisterPhoneInput(_phone, phoneObject) {
  const nextCountry = phoneObject?.countryCode?.toUpperCase();

  if (nextCountry) {
    registerCountry.value = nextCountry;
  }
}

function handleRegisterPictureChange(event) {
  const target = event.target;

  if (!(target instanceof HTMLInputElement)) {
    return;
  }

  registerForm.profile_picture = target.files?.[0] || null;
}

function openRegisterGalleryPicker() {
  const input = document.getElementById("register-gallery-input");

  if (input instanceof HTMLInputElement) {
    input.click();
  }
}

async function captureRegisterFromCamera() {
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

    const captured = await webPathToFile(photo.webPath, `register_${Date.now()}.jpg`);
    registerForm.profile_picture = captured;
  } catch {
    if (!isNative) {
      // Browser fallback where native camera plugin is unavailable.
      registerCameraInput.value?.click?.();
      return;
    }

    errorMessage.value = "Could not open native camera. Please allow camera permission and try again.";
  }
}

async function webPathToFile(webPath, filename) {
  const response = await fetch(webPath);
  const blob = await response.blob();
  return new File([blob], filename, { type: blob.type || "image/jpeg" });
}
</script>

<template>
  <ion-page>
    <ion-content class="ion-padding auth-content">
      <div class="auth-wrapper" :class="`auth-wrapper--${mode}`">
        <div class="auth-hero" />
        <h2>KRWWF</h2>
        <p>Please login or create an account.</p>

        <div class="mode-switch" role="tablist" aria-label="Auth mode">
          <button
            type="button"
            class="mode-switch__btn"
            :class="{ 'mode-switch__btn--active': mode === 'login' }"
            @click="mode = 'login'"
          >
            Login
          </button>
          <button
            type="button"
            class="mode-switch__btn"
            :class="{ 'mode-switch__btn--active': mode === 'register' }"
            @click="mode = 'register'"
          >
            Register
          </button>
        </div>

        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <form v-if="mode === 'login'" @submit.prevent="submitLogin" class="form-block">
          <div class="login-method">
            <label class="login-method__item">
              <input v-model="loginMethod" type="radio" value="mobile" />
              Mobile
            </label>
            <label class="login-method__item">
              <input v-model="loginMethod" type="radio" value="email" />
              Email
            </label>
          </div>

          <template v-if="loginMethod === 'mobile'">
            <ion-item class="phone-item" lines="none">
              <ion-label position="stacked">Mobile Number</ion-label>
              <VueTelInput
                v-model="loginForm.mobile_phone"
                mode="national"
                default-country="PK"
                :auto-default-country="false"
                :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
                :input-options="{ placeholder: 'Type mobile number', showDialCode: false }"
                @on-input="handleLoginPhoneInput"
                valid-characters-only
              />
            </ion-item>
          </template>

          <ion-item v-else>
            <ion-label position="stacked">Email</ion-label>
            <input v-model="loginForm.email" name="email" class="native-input" type="email" required />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Password</ion-label>
            <div class="password-field">
              <input
                v-model="loginForm.password"
                name="password"
                class="native-input"
                :type="showLoginPassword ? 'text' : 'password'"
                required
              />
              <button
                type="button"
                class="toggle-password-btn"
                :aria-label="showLoginPassword ? 'Hide password' : 'Show password'"
                @click="showLoginPassword = !showLoginPassword"
              >
                <ion-icon :name="showLoginPassword ? 'eye-off-outline' : 'eye-outline'" />
              </button>
            </div>
          </ion-item>

          <ion-button expand="block" type="submit" :disabled="loading">
            {{ loading ? "Please wait..." : "Login" }}
          </ion-button>
        </form>

        <form v-else @submit.prevent="submitRegister" class="form-block">
          <ion-item>
            <ion-label position="stacked">Full Name</ion-label>
            <input v-model="registerForm.name" name="name" class="native-input" required />
          </ion-item>

          <ion-item class="phone-item" lines="none">
            <ion-label position="stacked">Mobile Number</ion-label>
            <VueTelInput
              v-model="registerForm.mobile_phone"
              mode="national"
              default-country="PK"
              :auto-default-country="false"
              :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
              :input-options="{ placeholder: 'Type mobile number', showDialCode: false }"
              @on-input="handleRegisterPhoneInput"
              valid-characters-only
            />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Email (Optional)</ion-label>
            <input v-model="registerForm.email" name="email" class="native-input" type="email" />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Profile Picture (Optional)</ion-label>
            <div class="picture-actions">
              <button type="button" class="picture-action-btn" @click="captureRegisterFromCamera">
                Capture From Camera
              </button>
              <button type="button" class="picture-action-btn" @click="openRegisterGalleryPicker">
                Upload From Gallery
              </button>
            </div>
            <p v-if="registerForm.profile_picture" class="picture-name">
              Selected: {{ registerForm.profile_picture.name }}
            </p>
            <input
              id="register-camera-input"
              ref="registerCameraInput"
              name="profile_picture"
              class="visually-hidden-file-input"
              type="file"
              accept="image/*"
              capture="environment"
              @change="handleRegisterPictureChange"
            />
            <input
              id="register-gallery-input"
              name="profile_picture"
              class="visually-hidden-file-input"
              type="file"
              accept="image/*"
              @change="handleRegisterPictureChange"
            />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Password</ion-label>
            <div class="password-field">
              <input
                v-model="registerForm.password"
                name="password"
                class="native-input"
                :type="showRegisterPassword ? 'text' : 'password'"
                minlength="8"
                required
              />
              <button
                type="button"
                class="toggle-password-btn"
                :aria-label="showRegisterPassword ? 'Hide password' : 'Show password'"
                @click="showRegisterPassword = !showRegisterPassword"
              >
                <ion-icon :name="showRegisterPassword ? 'eye-off-outline' : 'eye-outline'" />
              </button>
            </div>
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Confirm Password</ion-label>
            <div class="password-field">
              <input
                v-model="registerForm.password_confirmation"
                name="password_confirmation"
                class="native-input"
                :type="showRegisterPasswordConfirmation ? 'text' : 'password'"
                minlength="8"
                required
              />
              <button
                type="button"
                class="toggle-password-btn"
                :aria-label="showRegisterPasswordConfirmation ? 'Hide password confirmation' : 'Show password confirmation'"
                @click="showRegisterPasswordConfirmation = !showRegisterPasswordConfirmation"
              >
                <ion-icon :name="showRegisterPasswordConfirmation ? 'eye-off-outline' : 'eye-outline'" />
              </button>
            </div>
          </ion-item>

          <ion-button expand="block" type="submit" :disabled="loading">
            {{ loading ? "Please wait..." : "Create Account" }}
          </ion-button>
        </form>
      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.auth-wrapper {
  max-width: 540px;
  margin: 24px auto;
  background: var(--app-surface-color);
  border-radius: var(--app-card-radius);
  box-shadow: 0 12px 30px rgba(var(--ion-text-color-rgb), 0.08);
  padding: 18px;
  position: relative;
  overflow: visible;
}

.auth-wrapper::before {
  content: "";
  position: absolute;
  inset: -20%;
  background: radial-gradient(circle at 20% 20%, rgba(var(--ion-color-primary-rgb), 0.22), transparent 42%),
    radial-gradient(circle at 80% 10%, rgba(var(--ion-color-secondary-rgb), 0.16), transparent 40%),
    radial-gradient(circle at 40% 80%, rgba(var(--ion-color-primary-rgb), 0.14), transparent 45%);
  animation: authGlow 12s ease-in-out infinite alternate;
  pointer-events: none;
}

.auth-wrapper > * {
  position: relative;
  z-index: 1;
}

.auth-wrapper--register::before {
  background: radial-gradient(circle at 15% 25%, rgba(var(--ion-color-secondary-rgb), 0.2), transparent 45%),
    radial-gradient(circle at 85% 15%, rgba(var(--ion-color-primary-rgb), 0.18), transparent 42%),
    radial-gradient(circle at 50% 85%, rgba(var(--ion-color-primary-rgb), 0.12), transparent 48%);
}

.auth-content {
  --background: var(--ion-background-color);
  background: linear-gradient(
    135deg,
    rgba(var(--ion-color-primary-rgb), 0.08) 0%,
    rgba(var(--ion-background-color-rgb), 1) 45%,
    rgba(var(--ion-color-secondary-rgb), 0.08) 100%
  );
  background-size: 200% 200%;
  animation: bgShift 18s ease infinite;
}

.auth-hero {
  width: 100%;
  height: 170px;
  border-radius: 14px;
  margin-bottom: 14px;
  background-image: url('/auth-bg.png');
  background-size: cover;
  background-position: center;
  animation: heroFloat 9s ease-in-out infinite;
}

.form-block {
  margin-top: 16px;
  display: grid;
  gap: 10px;
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

.mode-switch {
  margin-top: 10px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.mode-switch__btn {
  border: 1px solid var(--app-muted-border-color);
  background: transparent;
  color: var(--app-muted-text-color);
  border-radius: 10px;
  padding: 10px 12px;
  font-weight: 600;
  cursor: pointer;
}

.mode-switch__btn--active {
  border-color: var(--ion-color-primary);
  color: var(--ion-color-primary);
}

.error-text {
  color: var(--ion-color-danger);
  margin-top: 10px;
}

.native-input {
  width: 100%;
  background: var(--app-surface-color) !important;
  border: 0;
  color: var(--ion-text-color) !important;
  -webkit-text-fill-color: var(--ion-text-color);
  font: inherit;
  padding: 10px 0 8px;
}

.native-input:focus {
  outline: none;
}

.native-input:-webkit-autofill,
.native-input:-webkit-autofill:hover,
.native-input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px var(--app-surface-color) inset;
  -webkit-text-fill-color: var(--ion-text-color);
  transition: background-color 5000s ease-in-out 0s;
}

ion-item {
  --background: var(--app-surface-color);
  --border-color: var(--app-muted-border-color);
  --color: var(--ion-text-color);
  border-radius: 12px;
}

.password-field {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
}

.toggle-password-btn {
  margin-left: auto;
  border: 0;
  background: transparent;
  color: var(--app-muted-text-color);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  cursor: pointer;
}

.toggle-password-btn ion-icon {
  font-size: 18px;
}

.login-method {
  display: flex;
  gap: 14px;
  align-items: center;
}

.login-method__item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--app-muted-text-color);
  font-weight: 500;
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

:deep(.vti__input:focus) {
  outline: none !important;
  box-shadow: none !important;
}

:deep(.vti__dropdown) {
  background: transparent !important;
  border: 0 !important;
  cursor: pointer;
  pointer-events: auto;
}

:deep(.vti__selection) {
  border: 0 !important;
  box-shadow: none !important;
}

:deep(.vti__dropdown-list) {
  position: absolute !important;
  top: calc(100% + 6px) !important;
  left: 0 !important;
  min-width: 320px;
  max-height: 260px;
  overflow-y: auto;
  border: 1px solid var(--app-muted-border-color);
  border-radius: 10px;
  background: var(--app-surface-color);
  box-shadow: 0 12px 28px rgba(var(--ion-text-color-rgb), 0.18);
  z-index: 2147483647 !important;
  pointer-events: auto !important;
}

:deep(.vti__search_box) {
  display: block !important;
  width: 100%;
  border: 1px solid var(--app-muted-border-color) !important;
  border-radius: 8px;
  padding: 8px 10px !important;
  margin-bottom: 8px;
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

.phone-item:focus-within {
  z-index: 40;
}

.phone-item::part(native) {
  overflow: visible;
  border: 0 !important;
  box-shadow: none !important;
}

.phone-item::part(detail-icon) {
  display: none;
}

@keyframes authGlow {
  0% {
    transform: translate3d(0, 0, 0) scale(1);
  }
  100% {
    transform: translate3d(0, -2%, 0) scale(1.08);
  }
}

@keyframes bgShift {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

@keyframes heroFloat {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

@media (prefers-reduced-motion: reduce) {
  .auth-content,
  .auth-wrapper::before,
  .auth-hero {
    animation: none;
  }
}
</style>

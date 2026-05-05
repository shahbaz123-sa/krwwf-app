<script setup>
import { reactive, ref, onBeforeUnmount } from "vue";
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
const rememberMe = ref(true);

const logoSrc = "/favicon.png";

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

// Utility to detect mobile device
function isMobileDevice() {
  return /Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
}

// Webcam modal state
const showWebcamModal = ref(false);
const webcamStream = ref(null);
const webcamVideoRef = ref(null);
const webcamCanvasRef = ref(null);
const webcamError = ref("");

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

function openWebcamModal() {
  showWebcamModal.value = true;
  webcamError.value = "";
  // Start webcam
  navigator.mediaDevices.getUserMedia({ video: true })
    .then((stream) => {
      webcamStream.value = stream;
      if (webcamVideoRef.value) {
        webcamVideoRef.value.srcObject = stream;
        webcamVideoRef.value.play();
      }
    })
    .catch((err) => {
      webcamError.value = "Could not access webcam: " + err.message;
    });
}

function closeWebcamModal() {
  showWebcamModal.value = false;
  if (webcamStream.value) {
    webcamStream.value.getTracks().forEach((track) => track.stop());
    webcamStream.value = null;
  }
}

function captureWebcamPhoto() {
  if (!webcamVideoRef.value || !webcamCanvasRef.value) return;
  const video = webcamVideoRef.value;
  const canvas = webcamCanvasRef.value;
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext("2d");
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  canvas.toBlob((blob) => {
    if (blob) {
      const file = new File([blob], `register_${Date.now()}.jpg`, { type: blob.type });
      registerForm.profile_picture = file;
      closeWebcamModal();
    }
  }, "image/jpeg", 0.92);
}

onBeforeUnmount(() => {
  if (webcamStream.value) {
    webcamStream.value.getTracks().forEach((track) => track.stop());
  }
});

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
    } else if (!isMobileDevice()) {
      // Desktop/laptop: open webcam modal
      openWebcamModal();
      return;
    } else {
      // Browser fallback where native camera plugin is unavailable.
      registerCameraInput.value?.click?.();
      return;
    }
  } catch {
    if (!isNative) {
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

// Sentence case utility
function toSentenceCase(str) {
  return str.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
}

function handleNameInput(event) {
  const value = event.target.value;
  const sentenceCased = toSentenceCase(value);
  registerForm.name = sentenceCased;
}

function handleEmailInput(event, formType) {
  const value = event.target.value;
  if (formType === 'login') {
    loginForm.email = value.toLowerCase();
  } else if (formType === 'register') {
    registerForm.email = value.toLowerCase();
  }
}
</script>

<template>
  <ion-page>
    <ion-content class="ion-padding auth-content">
      <div class="auth-wrapper" :class="`auth-wrapper--${mode}`">
        <div class="auth-cover" aria-hidden="true">
          <div class="brand-row">
<!--            <img class="brand-logo" :src="logoSrc" alt="KRWWF" />-->
            <div class="brand-text">
<!--              <div class="brand-name">KRWWF</div>-->
<!--              <div class="brand-subtitle">Hi, Welcome Back</div>-->
            </div>
          </div>
        </div>

        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <form v-if="mode === 'login'" @submit.prevent="submitLogin" class="form-block">
          <div class="pill-switch pill-switch--compact" role="tablist" aria-label="Login method">
            <button
              type="button"
              class="pill-switch__btn"
              :class="{ 'pill-switch__btn--active': loginMethod === 'email' }"
              @click="loginMethod = 'email'"
            >
              Email
            </button>
            <button
              type="button"
              class="pill-switch__btn"
              :class="{ 'pill-switch__btn--active': loginMethod === 'mobile' }"
              @click="loginMethod = 'mobile'"
            >
              Phone Number
            </button>
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
                :input-options="{ placeholder: 'Type mobile number', showDialCode: false, type: 'tel' }"
                @on-input="handleLoginPhoneInput"
                valid-characters-only
              />
            </ion-item>
          </template>

          <ion-item v-else>
            <ion-label position="stacked">Email</ion-label>
            <input v-model="loginForm.email" name="email" class="native-input" type="email" required @input="handleEmailInput($event, 'login')" />
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

          <div class="login-meta">
            <label class="remember">
              <input v-model="rememberMe" type="checkbox" />
              <span>Remember me</span>
            </label>
            <button type="button" class="link-btn" @click.prevent>
              Forgot Password?
            </button>
          </div>

          <ion-button class="primary-cta" expand="block" type="submit" :disabled="loading">
            {{ loading ? "Please wait..." : "Login" }}
          </ion-button>

          <div class="auth-alt">
            <span class="auth-alt__text">Don’t have an account?</span>
            <button type="button" class="auth-alt__link" @click="mode = 'register'">Register</button>
          </div>
        </form>

        <template v-if="mode === 'register'">
          <div class="form-scroll-wrapper">
            <form @submit.prevent="submitRegister" class="form-block">
              <ion-item>
                <ion-label position="stacked">Full Name</ion-label>
                <input
                  v-model="registerForm.name"
                  name="name"
                  class="native-input"
                  required
                  @input="handleNameInput"
                />
              </ion-item>

          <ion-item class="phone-item" lines="none">
            <ion-label position="stacked">Mobile Number</ion-label>
            <VueTelInput
              v-model="registerForm.mobile_phone"
              mode="national"
              default-country="PK"
              :auto-default-country="false"
              :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
              :input-options="{ placeholder: 'Type mobile number', showDialCode: false, type: 'tel' }"
              @on-input="handleRegisterPhoneInput"
              valid-characters-only
            />
          </ion-item>

          <ion-item>
            <ion-label position="stacked">Email (Optional)</ion-label>
            <input v-model="registerForm.email" name="email" class="native-input" type="email" @input="handleEmailInput($event, 'register')" />
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

              <ion-button class="primary-cta" expand="block" type="submit" :disabled="loading">
                {{ loading ? "Please wait..." : "Create Account" }}
              </ion-button>

              <div class="auth-alt">
                <span class="auth-alt__text">Already have an account?</span>
                <button type="button" class="auth-alt__link" @click="mode = 'login'">Login</button>
              </div>
            </form>
          </div>
        </template>
      </div>
      <!-- Webcam Modal for Desktop/Laptop -->
      <div v-if="showWebcamModal" class="webcam-modal">
        <div class="webcam-modal__backdrop" @click="closeWebcamModal"></div>
        <div class="webcam-modal__content">
          <h3>Capture Photo from Webcam</h3>
          <video ref="webcamVideoRef" autoplay playsinline style="width: 100%; max-width: 320px; border-radius: 8px; background: #222;"></video>
          <canvas ref="webcamCanvasRef" style="display: none;"></canvas>
          <div v-if="webcamError" class="error-text">{{ webcamError }}</div>
          <div class="webcam-modal__actions">
            <button @click="captureWebcamPhoto" class="picture-action-btn">Take Photo</button>
            <button @click="closeWebcamModal" class="picture-action-btn">Cancel</button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>

/* Remove border from VueTelInput inside phone-item for seamless look */
.phone-item,
.phone-item :deep(.vti),
.phone-item :deep(.vti__input),
.phone-item :deep(.vti__input-wrapper),
.phone-item :deep(.vti__selection),
.phone-item :deep(.vti__dropdown),
.phone-item :deep(.vti__input:focus),
.phone-item > div,
.phone-item input {
  border: none !important;
  border-bottom: none !important;
  box-shadow: none !important;
  outline: none !important;
  background: transparent !important;
}

.phone-item {
  --border-color: transparent !important;
  --border-width: 0 !important;
  --inner-border-color: transparent !important;
  --inner-border-width: 0 !important;
  --inner-box-shadow: none !important;
  --highlight-height: 0 !important;
}

/* Remove border from ion-item in phone-item context */
.phone-item.ion-item,
.phone-item[lines="none"],
.phone-item[lines="full"],
.phone-item[lines="inset"] {
  border: none !important;
  box-shadow: none !important;
}

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

.brand-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}

.brand-logo {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  object-fit: contain;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.06);
  padding: 6px;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
}

.brand-name {
  font-weight: 900;
  letter-spacing: 0.4px;
  color: var(--ion-text-color);
  font-size: 18px;
}

.brand-subtitle {
  margin-top: 2px;
  color: var(--app-muted-text-color);
  font-size: 26px;
  font-weight: 900;
}

/* Cover/header area (replaces the flat green panel from the reference design)
   Uses the already-attached cover image (/auth-bg.png). */
.auth-cover {
  width: 100%;
  min-height: 150px;
  border-radius: 14px;
  padding: 14px;
  margin-bottom: 14px;
  background-image: url('/auth-bg.png');
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
}

.auth-cover::after {
  content: "";
  position: absolute;
  inset: 0;
  /* soft overlay to keep text readable regardless of image */
  background: linear-gradient(
    180deg,
    rgba(255, 255, 255, 0.72) 0%,
    rgba(255, 255, 255, 0.35) 55%,
    rgba(255, 255, 255, 0) 100%
  );
  pointer-events: none;
}

.auth-cover .brand-row {
  position: relative;
  z-index: 1;
  margin-bottom: 0;
}

/* Add scrollable wrapper for register form */
.form-scroll-wrapper {
  width: 100%;
  max-width: 100%;
  -webkit-overflow-scrolling: touch;
}

@media (max-width: 600px) {
  .auth-wrapper {
    max-height: 92vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .form-scroll-wrapper {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-right: 2px;
  }
  .form-block {
    min-width: 0;
    width: 100%;
    box-sizing: border-box;
    background: transparent;
    box-shadow: none;
    padding: 0;
    margin: 0;
    overflow: visible;
  }
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

/* legacy hero removed in favor of .auth-cover */

.form-block {
  margin-top: 16px;
  display: grid;
  gap: 10px;
}

/* Design-style pill switch (used for Login/Register and Email/Phone) */
.pill-switch {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: rgba(var(--ion-text-color-rgb), 0.06);
  border-radius: 999px;
  padding: 4px;
  gap: 4px;
}

.pill-switch--compact {
  margin-top: 2px;
}

.pill-switch__btn {
  border: 0;
  background: transparent;
  color: var(--ion-text-color);
  border-radius: 999px;
  padding: 10px 12px;
  font-weight: 800;
  cursor: pointer;
}

.pill-switch__btn--active {
  background: var(--ion-color-primary);
  color: var(--ion-color-primary-contrast);
  box-shadow: 0 10px 24px rgba(var(--ion-color-primary-rgb), 0.25);
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


.login-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 2px 2px 0;
}

.remember {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--app-muted-text-color);
  font-size: 12px;
  font-weight: 600;
}

.remember input {
  width: 14px;
  height: 14px;
}

.link-btn {
  border: 0;
  background: transparent;
  color: var(--ion-color-primary);
  font-weight: 800;
  font-size: 12px;
  cursor: pointer;
  padding: 0;
}

/* Bottom inline switch (Register/Login link) like the design */
.auth-alt {
  margin-top: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: var(--app-muted-text-color);
  font-size: 13px;
  font-weight: 600;
}

.auth-alt__link {
  border: 0;
  background: transparent;
  padding: 0;
  cursor: pointer;
  color: var(--ion-text-color);
  font-weight: 900;
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

.primary-cta {
  --border-radius: 999px;
  --box-shadow: 0 14px 28px rgba(var(--ion-color-primary-rgb), 0.2);
  font-weight: 900;
  text-transform: none;
  min-height: 44px;
}

.password-field {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Password eye icon button styling */
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

/* Ensure eye icon is visible in light mode */
.toggle-password-btn ion-icon {
  font-size: 18px;
  color: var(--ion-color-primary); /* Use primary color for visibility in light mode */
}

/* vue-tel-input: make it look like the other inputs and keep dropdown above */
:deep(.vti),
:deep(.vti__input-wrapper) {
  width: 100%;
  border: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
  outline: none !important;
}

:deep(.vti__input) {
  flex: 1;
  min-width: 0;
  width: 100%;
  border: 0 !important;
  background: transparent !important;
  color: var(--ion-text-color) !important;
  font: inherit;
  padding: 10px 0 8px !important;
  box-shadow: none !important;
}


/* Adjust stacked label for phone input to avoid overlap */
.phone-item ion-label[position="stacked"] {
  margin-bottom: 0px;
  padding-bottom: 0px;
  z-index: 2;
  background: transparent;
}

:deep(.vti__input:focus) {
  outline: none !important;
  box-shadow: none !important;
}

/* Ensure dropdown trigger is clickable and visible */
:deep(.vti__dropdown) {
  background: transparent !important;
  border: 0 !important;
  cursor: pointer;
  pointer-events: auto !important;
  z-index: 50 !important;
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
  z-index: 9999 !important;
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

/* Ensure phone-item does not clip dropdown */
.phone-item {
  position: relative;
  overflow: visible !important;
  z-index: 30;
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

.webcam-modal {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.webcam-modal__backdrop {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.45);
}
.webcam-modal__content {
  position: relative;
  background: var(--app-surface-color, #fff);
  border-radius: 14px;
  padding: 24px 18px 18px 18px;
  z-index: 2;
  min-width: 320px;
  max-width: 95vw;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  display: flex;
  flex-direction: column;
  align-items: center;
}
.webcam-modal__actions {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}
</style>

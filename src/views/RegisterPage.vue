<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { register } from "@/services/auth";
import { parsePhoneNumberFromString } from "libphonenumber-js";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/vue-tel-input.css";
import crestLogo from '@/assets/logo.png';
import castleBg from '@/assets/castle.jpg';
import forestFooter from '@/assets/forest-footer.png';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref("");
const showRegisterPassword = ref(false);
const showRegisterPasswordConfirmation = ref(false);
const registerForm = reactive({
  name: "",
  mobile_phone: "",
  email: "",
  password: "",
  password_confirmation: "",
  profile_picture: null,
});
const registerCountry = ref("PK");

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
function handleRegisterPhoneInput(_phone, phoneObject) {
  const nextCountry = phoneObject?.countryCode?.toUpperCase();
  if (nextCountry) {
    registerCountry.value = nextCountry;
  }

  // Keep only national number in input; dial code must come from country selector.
  const nationalNumber = String(phoneObject?.nationalNumber || "").trim();
  if (nationalNumber) {
    registerForm.mobile_phone = nationalNumber;
    return;
  }

  const raw = String(_phone || "").trim();
  const digitsOnly = raw.replace(/[^\d+]/g, "");
  const selectedDialCode = String(phoneObject?.countryCallingCode || "").trim();
  if (selectedDialCode && digitsOnly.startsWith(`+${selectedDialCode}`)) {
    registerForm.mobile_phone = digitsOnly.slice(selectedDialCode.length + 1);
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
      errorMessage.value = "Please enter a valid mobile number.";
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
    await router.replace("/dashboard");
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || "Could not complete request. Please try again.";
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" class="auth-content">
      <div class="cover-section" :style="{ backgroundImage: `url(${castleBg})` }">
        <div class="cover-overlay"></div>
        <div class="cover-center">
          <img :src="crestLogo" class="crest-logo" alt="KRWWF Crest" />
          <div class="cover-title">KHANZADA</div>
          <div class="cover-sub">Rajput Welfare & Waseela Foundation</div>
          <div class="cover-urdu">اتحاد، خدمت، ترقی</div>
        </div>
      </div>
      <div class="form-card">
        <div class="form-title">Create Account</div>
        <div class="form-sub">Register to join the community</div>
        <form @submit.prevent="submitRegister" class="register-form">
          <div class="input-group">
            <label>Name</label>
            <input v-model="registerForm.name" type="text" class="input-field" placeholder="Full Name" required />
          </div>
          <div class="input-group">
            <label>Mobile Number</label>
            <VueTelInput
              v-model="registerForm.mobile_phone"
              mode="international"
              default-country="PK"
              :preferred-countries="['PK', 'SA', 'AE', 'US', 'GB']"
              :auto-default-country="false"
              :dropdown-options="{ showDialCodeInSelection: true, showDialCodeInList: true, showFlags: true, showSearchBox: true }"
              :input-options="{ placeholder: '300 1234567', showDialCode: false, type: 'tel' }"
              @on-input="handleRegisterPhoneInput"
              valid-characters-only
              class="input-field phone-input"
            />
          </div>
          <div class="input-group">
            <label>Email</label>
            <input v-model="registerForm.email" type="email" class="input-field" placeholder="Email" required />
          </div>
          <div class="input-group">
            <label>Password</label>
            <input v-model="registerForm.password" :type="showRegisterPassword ? 'text' : 'password'" class="input-field" placeholder="Password" required />
          </div>
          <div class="input-group">
            <label>Confirm Password</label>
            <input v-model="registerForm.password_confirmation" :type="showRegisterPasswordConfirmation ? 'text' : 'password'" class="input-field" placeholder="Confirm Password" required />
          </div>
          <button class="main-login-btn" :disabled="loading">
            Register
          </button>
        </form>
        <div class="register-row">
          Already have an account? <router-link to="/login" class="register-link">Login</router-link>
        </div>
      </div>
      <div class="footer-curve">
        <img class="footer-bg" :src="forestFooter" alt="Footer background" />
        <div class="footer-content">
          <div class="footer-title">Connect. Collaborate. Empower.</div>
          <div class="footer-desc">Let's build a stronger Khanzada community together.</div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.auth-content {
  --background: #f4f6f9;
  --padding-bottom: calc(32px + env(safe-area-inset-bottom));
  min-height: 100vh;
  padding: 0;
  position: relative;
}
.cover-section {
  position: relative;
  width: 100%;
  min-height: 176px;
  background-size: cover;
  background-position: center;
  border-bottom-left-radius: 22px;
  border-bottom-right-radius: 22px;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.cover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg,rgba(30,68,52,0.7) 0%,rgba(30,68,52,0.5) 60%,rgba(30,68,52,0.0) 100%);
  z-index: 1;
}
.cover-center {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  width: 100%;
  padding-bottom: 12px;
}
.crest-logo {
  width: 62px;
  height: 62px;
  margin-bottom: 6px;
  object-fit: contain;
}
.cover-title {
  font-size: 22px;
  font-weight: 900;
  color: #FFD700;
  letter-spacing: 1px;
  margin-bottom: 2px;
}
.cover-sub {
  font-size: 11px;
  color: #fff;
  font-weight: 700;
  letter-spacing: 0.3px;
}
.cover-urdu {
  font-size: 14px;
  color: #fff;
  margin-top: 2px;
  font-family: "Noto Nastaliq Urdu", serif;
}
.form-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 3px 14px rgba(30,68,52,0.08);
  width: calc(100% - 14px);
  max-width: 360px;
  margin: -30px auto 0 auto;
  padding: 18px 14px 14px 14px;
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.form-title {
  font-size: 18px;
  font-weight: 800;
  color: #1e4434;
  text-align: center;
}
.form-sub {
  font-size: 12px;
  color: #6a7c7b;
  text-align: center;
  margin-bottom: 12px;
}
.input-group {
  margin-bottom: 12px;
}
.input-group label {
  font-size: 12px;
  font-weight: 700;
  color: #1e4434;
  margin-bottom: 4px;
  display: block;
}
.input-field {
  width: 100%;
  border: none;
  border-bottom: 2px solid #e0e0e0;
  background: transparent;
  font-size: 15px;
  color: #1e4434;
  padding: 8px 0 6px 0;
  outline: none;
  transition: border-color 0.2s;
}
.input-field:focus {
  border-bottom: 2px solid #1e4434;
}
.phone-input :deep(.vti__input) {
  border: none !important;
  border-bottom: 2px solid #e0e0e0 !important;
  background: transparent !important;
  font-size: 15px !important;
  color: #1e4434 !important;
  padding: 8px 0 6px 0 !important;
}
.phone-input :deep(.vti__input-container) {
  display: flex;
  align-items: center;
  gap: 8px;
}
.phone-input :deep(.vti__input:focus) {
  border-bottom: 2px solid #1e4434 !important;
}
.phone-input :deep(.vti__selection) {
  background: transparent !important;
  border: none !important;
  margin-right: 0 !important;
  padding-right: 2px;
}
.phone-input :deep(.vti__dropdown) {
  padding-right: 6px;
}
.phone-input :deep(.vti__flag) {
  margin-right: 6px;
}
.phone-input :deep(.vti__country-code) {
  margin-right: 6px;
}
.input-hint {
  margin-top: 6px;
  font-size: 11px;
  color: #6a7c7b;
}
.main-login-btn {
  width: 100%;
  background: #1e4434;
  color: #fff;
  font-weight: 800;
  font-size: 14px;
  border: none;
  border-radius: 7px;
  padding: 11px 0;
  margin-top: 6px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s;
}
.main-login-btn:disabled {
  background: #b2b2b2;
  cursor: not-allowed;
}
.register-row {
  text-align: center;
  margin-top: 12px;
  font-size: 12px;
  color: #6a7c7b;
}
.register-link {
  background: none;
  border: none;
  color: #F9B233;
  font-weight: 900;
  cursor: pointer;
  text-decoration: underline;
  margin-left: 4px;
  font-size: 13px;
  letter-spacing: 0.5px;
  transition: color 0.2s;
}
.register-link:hover, .register-link:focus {
  color: #ff9800;
}
.footer-curve {
  position: relative;
  z-index: 20;
  width: 100%;
  pointer-events: none;
  height: 250px;
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
}
.footer-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: bottom center;
  display: block;
  position: absolute;
  left: 0;
  bottom: 0;
  z-index: 1;
}
.footer-content {
  position: relative;
  z-index: 2;
  width: 100%;
  padding: 0 12px 26px;
  text-align: center;
  pointer-events: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.footer-title {
  font-size: 14px;
  font-weight: 800;
  color: #F9B233;
  margin-bottom: 2px;
  letter-spacing: 0.5px;
}
.footer-desc {
  font-size: 11px;
  color: #fff;
  font-weight: 500;
}
.back-btn {
  position: absolute;
  top: calc(12px + env(safe-area-inset-top));
  left: 18px;
  z-index: 3;
  background: rgba(255,255,255,0.85);
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(30,68,52,0.08);
  cursor: pointer;
  transition: background 0.2s;
}
.back-btn img {
  width: 22px;
  height: 22px;
}
.back-btn:hover {
  background: #f4f6f9;
}
@media (max-width: 340px) {
  .form-card {
    width: calc(100% - 10px);
    padding: 16px 10px 12px 10px;
  }
  .cover-title {
    font-size: 19px;
  }
  .cover-sub {
    font-size: 10px;
  }
  .input-field,
  .phone-input :deep(.vti__input) {
    font-size: 14px !important;
  }
}

@media (min-width: 601px) {
  .form-card {
    width: calc(100% - 24px);
    max-width: 370px;
    padding: 28px 22px 22px 22px;
  }
  .cover-section {
    min-height: 220px;
  }
  .footer-curve {
    height: 120px;
  }
  .footer-content {
    padding-bottom: 34px;
  }
}
</style>


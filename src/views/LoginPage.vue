<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { login } from "@/services/auth";
import { parsePhoneNumberFromString } from "libphonenumber-js";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/vue-tel-input.css";
import crestLogo from '@/assets/logo.png';
import googleIcon from '@/assets/google-icon.svg';
import castleBg from '@/assets/castle.jpg';
import forestFooter from '@/assets/forest-footer.png';
import arrowBack from '@/assets/arrow-back.svg';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';

const router = useRouter();
const loginMethod = ref("mobile");
const loading = ref(false);
const errorMessage = ref("");
const showLoginPassword = ref(false);
const loginForm = reactive({
  mobile_phone: "",
  email: "",
  password: "",
});
const loginCountry = ref("PK");

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
        <div class="form-title">Welcome Back!</div>
        <div class="form-sub">Login to continue your journey</div>
        <div class="tab-switch">
          <button :class="['tab-btn', loginMethod === 'mobile' && 'active']" @click="loginMethod = 'mobile'">
            <ion-icon name="call-outline"></ion-icon>
            Login with Mobile
          </button>
          <button :class="['tab-btn', loginMethod === 'email' && 'active']" @click="loginMethod = 'email'">
            <ion-icon name="mail-outline"></ion-icon>
            Login with Email
          </button>
        </div>
        <form @submit.prevent="submitLogin" class="login-form">
          <div v-if="loginMethod === 'mobile'">
            <div class="input-group">
              <label>Mobile Number</label>
              <VueTelInput
                v-model="loginForm.mobile_phone"
                mode="national"
                default-country="PK"
                :auto-default-country="false"
                :dropdown-options="{ showDialCodeInSelection: false, showDialCodeInList: false, showFlags: false, showSearchBox: true }"
                :input-options="{ placeholder: 'Mobile Number', showDialCode: false, type: 'tel' }"
                @on-input="handleLoginPhoneInput"
                valid-characters-only
                class="input-field phone-input"
              />
            </div>
          </div>
          <div v-else>
            <div class="input-group">
              <label>Email</label>
              <div class="input-icon-group">
                <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                <input v-model="loginForm.email" type="email" class="input-field" placeholder="Email" required />
              </div>
            </div>
          </div>
          <div class="input-group">
            <label>Password</label>
            <div class="input-icon-group">
              <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
              <input v-model="loginForm.password" :type="showLoginPassword ? 'text' : 'password'" class="input-field" placeholder="Password" required />
              <button type="button" class="eye-btn" @click="showLoginPassword = !showLoginPassword">
                <ion-icon :name="showLoginPassword ? 'eye-off-outline' : 'eye-outline'" />
              </button>
            </div>
          </div>
          <div class="forgot-row">
            <span></span>
            <button type="button" class="forgot-link">Forgot Password?</button>
          </div>
          <button class="main-login-btn" :disabled="loading">
            Login <ion-icon name="arrow-forward-outline"></ion-icon>
          </button>
          <div class="or-row"><span></span>OR<span></span></div>
          <button type="button" class="google-login-btn">
            <img :src="googleIcon" alt="Google" class="google-icon" />
            Login with Google
          </button>
        </form>
        <div class="register-row">
          Don’t have an account? <router-link to="/register" class="register-link">Register Now</router-link>
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
  --padding-bottom: calc(24px + env(safe-area-inset-bottom));
  min-height: 100vh;
  padding: 0;
  position: relative;
}
.cover-section {
  position: relative;
  width: 100%;
  min-height: 240px;
  background-size: cover;
  background-position: center;
  border-bottom-left-radius: 32px;
  border-bottom-right-radius: 32px;
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
  padding-bottom: 18px;
}
.crest-logo {
  width: 90px;
  height: 90px;
  margin-bottom: 8px;
  object-fit: contain;
}
.cover-title {
  font-size: 28px;
  font-weight: 900;
  color: #FFD700;
  letter-spacing: 2px;
  margin-bottom: 2px;
}
.cover-sub {
  font-size: 13px;
  color: #fff;
  font-weight: 700;
  letter-spacing: 1px;
}
.cover-urdu {
  font-size: 18px;
  color: #fff;
  margin-top: 2px;
  font-family: "Noto Nastaliq Urdu", serif;
}
.form-card {
  background: #fff;
  border-radius: 24px;
  box-shadow: 0 4px 24px rgba(30,68,52,0.07);
  width: calc(100% - 24px);
  height: 130%;
  max-width: 370px;
  margin: 0px auto 0 auto;
  padding: 32px 22px 24px 22px;
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.form-title {
  font-size: 22px;
  font-weight: 800;
  color: #1e4434;
  text-align: center;
}
.form-sub {
  font-size: 14px;
  color: #6a7c7b;
  text-align: center;
  margin-bottom: 18px;
}
.tab-switch {
  display: flex;
  gap: 0;
  background: #f4f6f9;
  border-radius: 12px;
  margin-bottom: 18px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}
.tab-btn {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  font-weight: 700;
  font-size: 12px;
  color: #6a7c7b;
  padding: 12px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  cursor: pointer;
  transition: color 0.2s;
}

.input-group {
  margin-bottom: 16px;
}
.input-group label {
  font-size: 13px;
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
  font-size: 16px;
  color: #1e4434;
  padding: 10px 0 7px 0;
  outline: none;
  transition: border-color 0.2s;
}
.input-field:focus {
  border-bottom: 2px solid #1e4434;
}
.input-icon-group {
  display: flex;
  align-items: center;
  position: relative;
}
.input-icon {
  font-size: 20px;
  color: #6a7c7b;
  margin-right: 8px;
}
.eye-btn {
  background: none;
  border: none;
  color: #6a7c7b;
  font-size: 20px;
  margin-left: auto;
  cursor: pointer;
  padding: 0 4px;
}
.phone-input :deep(.vti__input) {
  border: none !important;
  border-bottom: 2px solid #e0e0e0 !important;
  background: transparent !important;
  font-size: 16px !important;
  color: #1e4434 !important;
  padding: 10px 0 7px 0 !important;
}
.phone-input :deep(.vti__input:focus) {
  border-bottom: 2px solid #1e4434 !important;
}
.phone-input :deep(.vti__selection) {
  background: transparent !important;
  border: none !important;
}
.forgot-row {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: -10px;
}
.forgot-link {
  background: none;
  border: none;
  color: #1e4434;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  text-decoration: underline;
}
.main-login-btn {
  width: 100%;
  background: #1e4434;
  color: #fff;
  font-weight: 800;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  padding: 12px 0;
  margin-top: 10px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: background 0.2s;
}
.main-login-btn:disabled {
  background: #b2b2b2;
  cursor: not-allowed;
}
.or-row {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6a7c7b;
  font-size: 13px;
  margin: 12px 0 8px 0;
}
.or-row span {
  flex: 1;
  height: 1px;
  background: #e0e0e0;
  margin: 0 8px;
}
.google-login-btn {
  width: 100%;
  background: #fff;
  color: #1e4434;
  font-weight: 700;
  font-size: 15px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 10px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, border 0.2s;
}
.google-login-btn:hover {
  background: #f4f6f9;
  border: 1px solid #F9B233;
}
.google-icon {
  width: 22px;
  height: 22px;
  margin-right: 8px;
}
.register-row {
  text-align: center;
  margin-top: 18px;
  font-size: 14px;
  color: #6a7c7b;
}
.register-link {
  background: none;
  border: none;
  color: #1e4434;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
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
  height: 120px;
  margin-top: 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
}
.footer-bg {
  width: 100%;
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
  padding: 0 16px 36px;
  text-align: center;
  pointer-events: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.footer-title {
  font-size: 18px;
  font-weight: 800;
  color: #F9B233;
  margin-bottom: 2px;
  letter-spacing: 0.5px;
}
.footer-desc {
  font-size: 13px;
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
@media (max-width: 600px) {
  .form-card {
    width: calc(100% - 12px);
    max-width: 98vw;
    padding: 22px 6vw 18px 6vw;
  }
  .cover-section {
    min-height: 180px;
  }
  .footer-curve {
    height: 90px;
  }
  .footer-content {
    padding-bottom: 24px;
  }
}
</style>

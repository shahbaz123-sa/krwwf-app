<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { login, register } from "@/services/auth";

const router = useRouter();
const mode = ref("login");
const loading = ref(false);
const errorMessage = ref("");

const loginForm = ref({
  email: "",
  password: "",
});

const registerForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

async function submitLogin() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await login(loginForm.value);
    await router.replace("/dashboard");
  } catch (error) {
    errorMessage.value = extractError(error);
  } finally {
    loading.value = false;
  }
}

async function submitRegister() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await register(registerForm.value);
    await router.replace("/dashboard");
  } catch (error) {
    errorMessage.value = extractError(error);
  } finally {
    loading.value = false;
  }
}

function extractError(error) {
  const fallback = "Could not complete request. Please try again.";
  const firstValidationError = error?.response?.data?.errors
    ? Object.values(error.response.data.errors)[0]?.[0]
    : null;

  return firstValidationError || error?.response?.data?.message || fallback;
}
</script>

<template>
  <ion-page>
    <ion-content class="ion-padding">
      <div class="auth-wrapper">
        <h2>ERP App</h2>
        <p>Please login or create an account.</p>

        <ion-segment v-model="mode">
          <ion-segment-button value="login">
            <ion-label>Login</ion-label>
          </ion-segment-button>
          <ion-segment-button value="register">
            <ion-label>Register</ion-label>
          </ion-segment-button>
        </ion-segment>

        <p v-if="errorMessage" class="error-text">{{ errorMessage }}</p>

        <form v-if="mode === 'login'" @submit.prevent="submitLogin" class="form-block">
          <ion-item>
            <ion-input
              v-model="loginForm.email"
              label="Email"
              label-placement="stacked"
              type="email"
              required
            />
          </ion-item>

          <ion-item>
            <ion-input
              v-model="loginForm.password"
              label="Password"
              label-placement="stacked"
              type="password"
              required
            />
          </ion-item>

          <ion-button expand="block" type="submit" :disabled="loading">
            {{ loading ? "Please wait..." : "Login" }}
          </ion-button>
        </form>

        <form v-else @submit.prevent="submitRegister" class="form-block">
          <ion-item>
            <ion-input
              v-model="registerForm.name"
              label="Full Name"
              label-placement="stacked"
              required
            />
          </ion-item>

          <ion-item>
            <ion-input
              v-model="registerForm.email"
              label="Email"
              label-placement="stacked"
              type="email"
              required
            />
          </ion-item>

          <ion-item>
            <ion-input
              v-model="registerForm.password"
              label="Password"
              label-placement="stacked"
              type="password"
              minlength="8"
              required
            />
          </ion-item>

          <ion-item>
            <ion-input
              v-model="registerForm.password_confirmation"
              label="Confirm Password"
              label-placement="stacked"
              type="password"
              minlength="8"
              required
            />
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
  max-width: 420px;
  margin: 40px auto;
}

.form-block {
  margin-top: 16px;
  display: grid;
  gap: 10px;
}

.error-text {
  color: var(--ion-color-danger);
  margin-top: 12px;
}
</style>

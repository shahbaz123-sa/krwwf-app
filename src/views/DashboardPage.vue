<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, type AuthUser } from "@/services/auth";

const router = useRouter();
const pageContentId = "dashboard-content";
const user = ref<AuthUser | null>(null);
const loading = ref(true);

onMounted(async () => {
  try {
    user.value = await fetchCurrentUser();
  } catch {
    await logout();
    await router.replace("/home");
  } finally {
    loading.value = false;
  }
});

async function handleLogout() {
  await logout();
  await router.replace("/home");
}
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Your Dashboard" :content-id="pageContentId" />
    <ion-content class="ion-padding dashboard-content">
      <section class="dashboard-card">
        <p v-if="loading" class="dashboard-muted">Loading profile...</p>

        <template v-else-if="user">
          <h2 class="dashboard-title">Welcome, {{ user.name }}</h2>
          <p class="dashboard-muted">You are logged in successfully.</p>
          <p v-if="user.mobile_number" class="dashboard-detail">Mobile: {{ user.mobile_number }}</p>
          <p v-if="user.email" class="dashboard-detail">Email: {{ user.email }}</p>
        </template>

        <ion-button expand="block" class="dashboard-logout" @click="handleLogout">Logout</ion-button>
      </section>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.dashboard-content {
  --background: var(--ion-background-color);
  --padding-bottom: 84px;
  background: linear-gradient(
    135deg,
    rgba(var(--ion-color-primary-rgb), 0.06) 0%,
    rgba(var(--ion-background-color-rgb), 1) 50%,
    rgba(var(--ion-color-secondary-rgb), 0.08) 100%
  );
}

.dashboard-card {
  max-width: 560px;
  margin: 16px auto;
  padding: 18px;
  border-radius: var(--app-card-radius);
  background: var(--app-surface-color);
  box-shadow: 0 10px 28px rgba(var(--ion-text-color-rgb), 0.08);
}

.dashboard-title {
  margin: 0;
  color: var(--ion-text-color);
}

.dashboard-muted {
  margin: 8px 0;
  color: var(--app-muted-text-color);
}

.dashboard-detail {
  margin: 6px 0;
  color: var(--ion-text-color);
}

.dashboard-logout {
  margin-top: 14px;
  --background: var(--ion-color-primary);
  --color: var(--ion-color-primary-contrast);
}
</style>


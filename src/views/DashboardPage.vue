<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { fetchCurrentUser, logout } from "@/services/auth";

const router = useRouter();
const user = ref(null);
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
  <ion-page>
    <ion-content class="ion-padding">
      <h2>ERP Dashboard</h2>

      <p v-if="loading">Loading profile...</p>
      <p v-else-if="user">Welcome: {{ user.name }}</p>

      <ion-button expand="block" color="medium" @click="handleLogout">Logout</ion-button>
    </ion-content>
  </ion-page>
</template>


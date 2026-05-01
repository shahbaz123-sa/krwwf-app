<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, type AuthUser } from "@/services/auth";

const router = useRouter();
const pageContentId = "profile-content";
const loading = ref(true);
const user = ref<AuthUser | null>(null);

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

async function goToEditProfile(): Promise<void> {
  await router.push("/profile/edit");
}
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Profile" :content-id="pageContentId" />

    <ion-content class="ion-padding profile-content">
      <div class="profile-card">
        <h2 class="profile-title">Your Profile</h2>

        <template v-if="!loading && user">
          <div class="profile-picture-wrap">
            <img
              v-if="user.profile_picture_url"
              :src="user.profile_picture_url"
              alt="Profile picture"
              class="profile-picture"
            />
            <div v-else class="profile-picture profile-picture--placeholder">
              {{ user.name?.charAt(0)?.toUpperCase() || "U" }}
            </div>
          </div>

          <p class="profile-row"><strong>Name:</strong> {{ user.name }}</p>
          <p class="profile-row"><strong>Mobile:</strong> {{ user.mobile_number || "Not set" }}</p>
          <p class="profile-row"><strong>Email:</strong> {{ user.email || "Not set" }}</p>

          <ion-button expand="block" @click="goToEditProfile">Edit Profile</ion-button>
        </template>

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

.profile-title {
  margin-top: 0;
  color: var(--ion-text-color);
}

.profile-picture-wrap {
  display: flex;
  justify-content: center;
  margin: 12px 0 18px;
}

.profile-picture {
  width: 92px;
  height: 92px;
  border-radius: 999px;
  object-fit: cover;
  border: 2px solid var(--app-muted-border-color);
}

.profile-picture--placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(var(--ion-color-primary-rgb), 0.12);
  color: var(--ion-color-primary);
  font-weight: 700;
  font-size: 28px;
}

.profile-row {
  margin: 10px 0;
  color: var(--ion-text-color);
}
</style>



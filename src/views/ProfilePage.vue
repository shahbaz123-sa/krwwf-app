<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, type AuthUser } from "@/services/auth";
import { useTheme } from "@/composables/useTheme";

const router = useRouter();
const pageContentId = "profile-content";
const loading = ref(true);
const user = ref<AuthUser | null>(null);
const profileImageInput = ref<HTMLInputElement | null>(null);
const { isDark } = useTheme();

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

function handleProfileImageChange(event: Event): void {
  const target = event.target;
  if (!(target instanceof HTMLInputElement)) return;
  // You can add logic to upload the image here
  // For now, just log the file
  if (target.files && target.files[0]) {
    // Example: uploadProfileImage(target.files[0]);
    // For now, just log
    console.log('Selected file:', target.files[0]);
  }
}

function openProfileImagePicker(): void {
  profileImageInput.value?.click();
}

function captureProfileImage() {
  // You can add logic to capture from camera here
  // For now, just log
  console.log('Capture from camera clicked');
}
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Profile" :content-id="pageContentId" />

    <ion-content class="ion-padding profile-content">
      <div class="profile-card">
        <div class="profile-theme-badge">{{ isDark ? '🌙 Dark' : '☀️ Light' }}</div>
        <div class="profile-picture-wrap">
          <img
            v-if="user && user.profile_picture_url"
            :src="user.profile_picture_url"
            alt="Profile picture"
            class="profile-picture"
          />
          <div v-else class="profile-picture profile-picture--placeholder">
            {{ user?.name?.charAt(0)?.toUpperCase() || "U" }}
          </div>
        </div>
        <div class="profile-picture-actions">
          <ion-button size="small" @click="openProfileImagePicker">Upload</ion-button>
          <ion-button size="small" @click="captureProfileImage">Capture</ion-button>
          <input
            ref="profileImageInput"
            type="file"
            accept="image/*"
            style="display: none;"
            @change="handleProfileImageChange"
          />
        </div>
        <h2 class="profile-title">Your Profile</h2>

        <template v-if="!loading && user">
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
  position: relative;
  max-width: 560px;
  margin: 12px auto;
  background: var(--app-surface-color);
  border-radius: var(--app-card-radius);
  padding: 16px;
  box-shadow: 0 8px 24px rgba(var(--ion-text-color-rgb), 0.1);
}

.profile-theme-badge {
  position: absolute;
  top: 12px;
  right: 18px;
  font-size: 12px;
  color: var(--app-muted-text-color);
  background: var(--app-surface-color, #fff);
  border-radius: 8px;
  padding: 2px 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  pointer-events: none;
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

.profile-picture-actions {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 12px;
}

.profile-row {
  margin: 10px 0;
  color: var(--ion-text-color);
}
</style>

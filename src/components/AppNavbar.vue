<script setup lang="ts">
import { computed } from "vue";
import {
  IonButton,
  IonFooter,
  IonIcon,
  IonLabel,
  IonToolbar,
  useIonRouter,
} from "@ionic/vue";
import { personCircleOutline, gridOutline, logOutOutline } from "ionicons/icons";
import { useRouter } from "vue-router";
import { logout } from "@/services/auth";
import { useTheme } from "@/composables/useTheme";

type Props = {
  title: string;
  contentId: string;
};

const props = defineProps<Props>();

const router = useRouter();
const ionRouter = useIonRouter();
const { isDark } = useTheme();
const activePath = computed(() => router.currentRoute.value.path);

async function navigateTo(name: "Dashboard" | "Profile", path: "/dashboard" | "/profile"): Promise<void> {
  if (router.currentRoute.value.path === path) {
    return;
  }

  ionRouter.navigate(path, "forward", "replace");

  if (router.currentRoute.value.name !== name) {
    await router.replace({ name });
  }

  if (router.currentRoute.value.name !== name) {
    window.location.assign(path);
  }
}

async function handleLogout(): Promise<void> {
  await logout();
  ionRouter.navigate("/home", "root", "replace");

  if (router.currentRoute.value.name !== "Home") {
    await router.replace({ name: "Home" });
  }

  if (router.currentRoute.value.name !== "Home") {
    window.location.assign("/home");
  }
}
</script>

<template>
  <ion-footer class="bottom-nav">
    <ion-toolbar>
      <div class="bottom-nav__actions">
        <ion-button fill="clear" class="bottom-nav__btn" :class="{ 'bottom-nav__btn--active': activePath === '/dashboard' }" @click="navigateTo('Dashboard', '/dashboard')">
          <span class="bottom-nav__btn-inner">
            <ion-icon :icon="gridOutline" />
            <ion-label>Dashboard</ion-label>
          </span>
        </ion-button>
        <ion-button fill="clear" class="bottom-nav__btn" :class="{ 'bottom-nav__btn--active': activePath === '/profile' || activePath === '/profile/edit' }" @click="navigateTo('Profile', '/profile')">
          <span class="bottom-nav__btn-inner">
            <ion-icon :icon="personCircleOutline" />
            <ion-label>Profile</ion-label>
          </span>
        </ion-button>
        <ion-button fill="clear" class="bottom-nav__btn bottom-nav__btn--logout" @click="handleLogout">
          <span class="bottom-nav__btn-inner">
            <ion-icon :icon="logOutOutline" />
            <ion-label>Logout</ion-label>
          </span>
        </ion-button>
      </div>
    </ion-toolbar>
  </ion-footer>
</template>

<style scoped>
.bottom-nav {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
  border-top: 1px solid var(--app-muted-border-color);
}

.bottom-nav ion-toolbar {
  --background: var(--app-surface-color);
  --min-height: 62px;
}

.bottom-nav__actions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 4px;
  align-items: center;
  width: 100%;
}

.bottom-nav__btn {
  margin: 0;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  min-height: 56px;
  color: var(--ion-text-color);
}

.bottom-nav__btn-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.bottom-nav__btn ion-icon {
  font-size: 20px;
}

.bottom-nav__btn ion-label {
  font-size: 12px;
}

.bottom-nav__btn--logout {
  color: var(--ion-color-danger);
}

.bottom-nav__btn--active {
  color: var(--ion-color-primary);
}

.bottom-nav__btn--active ion-icon,
.bottom-nav__btn--active ion-label {
  color: var(--ion-color-primary);
}
</style>

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
import { useRouter } from "vue-router";
// Import Ionicons as objects for Capacitor/Android compatibility
import {
  home,
  homeOutline,
  calendar,
  calendarOutline,
  informationCircle,
  informationCircleOutline,
  chatbubbleEllipses,
  chatbubbleEllipsesOutline,
  call,
  callOutline,
} from 'ionicons/icons';

// Navigation configuration for each button
const navItems = [
  {
    name: "Dashboard",
    path: "/dashboard",
    icon: {
      active: home,
      inactive: homeOutline,
    },
    color: "primary",
    label: "Dashboard",
  },
  {
    name: "Events",
    path: "/events",
    icon: {
      active: calendar,
      inactive: calendarOutline,
    },
    color: "success",
    label: "Events",
  },
  {
    name: "About",
    path: "/about",
    icon: {
      active: informationCircle,
      inactive: informationCircleOutline,
    },
    color: "warning",
    label: "About Us",
  },
  {
    name: "AI",
    path: "/ai",
    icon: {
      active: chatbubbleEllipses,
      inactive: chatbubbleEllipsesOutline,
    },
    color: "tertiary",
    label: "AI Assistant",
  },
  {
    name: "Contact",
    path: "/contact",
    icon: {
      active: call,
      inactive: callOutline,
    },
    color: "danger",
    label: "Contact",
  },
];

const router = useRouter();
const ionRouter = useIonRouter();
const activePath = computed(() => router.currentRoute.value.path);

function isActiveTab(item: any) {
  // For About tab, match /about and any subroute
  if (item.path === '/about') {
    return activePath.value === '/about' || activePath.value.startsWith('/about/');
  }
  return activePath.value === item.path;
}

function navigateTo(path: string) {
  if (router.currentRoute.value.path !== path) {
    ionRouter.navigate(path, "forward", "replace");
  }
}
</script>

<template>
  <ion-footer class="bottom-nav">
    <ion-toolbar>
      <div class="bottom-nav__actions">
        <ion-button
          v-for="item in navItems"
          :key="item.path"
          fill="clear"
          class="bottom-nav__btn"
          :class="[ 'bottom-nav__btn--' + item.color, { 'bottom-nav__btn--active': isActiveTab(item) } ]"
          @click="navigateTo(item.path)"
        >
          <span class="bottom-nav__btn-inner">
            <ion-icon :icon="isActiveTab(item) ? item.icon.active : item.icon.inactive" />
            <ion-label>{{ item.label }}</ion-label>
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
  width: 100vw;
  max-width: 100vw;
  min-width: 0;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  overflow-x: hidden;
}

.bottom-nav ion-toolbar {
  --background: var(--app-surface-color);
  --padding-bottom: env(safe-area-inset-bottom);
  --min-height: calc(48px + env(safe-area-inset-bottom));
  min-height: 48px;
  padding: 0;
  width: 100vw;
  max-width: 100vw;
  min-width: 0;
  margin: 0;
  box-sizing: border-box;
  overflow-x: hidden;
}

.bottom-nav__actions {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  width: 100vw;
  max-width: 100vw;
  min-width: 0;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  overflow-x: hidden;
}

.bottom-nav__btn {
  flex: 1 1 0;
  min-width: 64px;
  margin: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 30px;
  color: var(--ion-text-color);
  padding: 0 2px;
  box-sizing: border-box;
}

.bottom-nav__btn-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.bottom-nav__btn ion-icon {
  font-size: 18px;
  margin-bottom: 0px;
}

.bottom-nav__btn ion-label {
  font-size: 8px;
  line-height: 1;
  margin-top: 6px;
  color: gray;
  white-space: nowrap;
}

.bottom-nav__btn--primary {
  color: var(--ion-color-primary);
}
.bottom-nav__btn--success {
  color: var(--ion-color-success);
}
.bottom-nav__btn--warning {
  color: var(--ion-color-warning);
}
.bottom-nav__btn--tertiary {
  color: var(--ion-color-tertiary);
}
.bottom-nav__btn--danger {
  color: var(--ion-color-danger);
}

.bottom-nav__btn--active {
  font-weight: bold;
  color: var(--ion-color-primary);
  position: relative;
}

.bottom-nav__btn--active::after {
  content: "";
  display: block;
  position: absolute;
  left: 50%;
  bottom: 2px;
  transform: translateX(-50%);
  width: 100%;
  height: 3px;
  background: var(--ion-color-primary);
  border-radius: 8px;
  font-weight: bold;
  z-index: 2;
}

.bottom-nav__btn--active ion-icon,
.bottom-nav__btn--active ion-label {
  color: var(--ion-color-primary);
}

@media (max-width: 600px) {
  .bottom-nav,
  .bottom-nav ion-toolbar,
  .bottom-nav__actions {
    width: 100vw;
    max-width: 100vw;
    min-width: 0;
    margin: 0;
    padding-left: 0;
    padding-right: 0;
    box-sizing: border-box;
    overflow-x: hidden;
  }
}
</style>

<template>
  <ion-app>
    <ion-router-outlet :key="route.fullPath" />
    <router-view v-slot="{ Component }">
      <component :is="Component" />
      <AppNavbar />
    </router-view>
  </ion-app>
</template>

<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { useRoute } from 'vue-router';
import { useTheme } from '@/composables/useTheme';
import AppNavbar from "@/components/AppNavbar.vue";

const route = useRoute();
useTheme(); // initialise theme from localStorage on mount
</script>

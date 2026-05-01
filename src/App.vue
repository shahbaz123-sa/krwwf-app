<template>
  <ion-app>
    <ion-router-outlet :key="route.fullPath" />
  </ion-app>
</template>

<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { useRoute } from 'vue-router';
import { useTheme } from '@/composables/useTheme';

const route = useRoute();
useTheme(); // initialise theme from localStorage on mount
</script>

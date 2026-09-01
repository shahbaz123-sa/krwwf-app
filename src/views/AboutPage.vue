<template>
  <ion-page>
    <ion-content class="about-page-content">
      <div class="about-page-main">
        <AboutSection />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { IonPage, IonContent } from '@ionic/vue';
import AboutSection from '@/features/dashboard/AboutSection.vue';
</script>

<style scoped>
.about-page-content {
  --background: #f8fafb;
  --padding-bottom: calc(84px + env(safe-area-inset-bottom));
}

.about-page-main {
  max-width: 1100px;
  margin: 0 auto;
  padding: clamp(12px, 2.5vw, 20px) clamp(10px, 2vw, 16px) clamp(24px, 4vw, 36px);
}
</style>

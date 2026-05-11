<template>
  <div class="about-card">
    <div class="about-text">
      <h3>About Khanzada</h3>
      <p>The Khanzada tribe has a rich history of courage, unity and leadership. Our ancestors played a vital role in the region's heritage and we continue to uphold the values of honor, integrity and brotherhood.</p>
      <ion-button class="explore-button" fill="clear">
        Explore History
        <ion-icon slot="end" name="arrow-forward"></ion-icon>
      </ion-button>
    </div>
    <img src="/src/assets/castle.jpg" alt="Khanzada castle" class="about-image" />
  </div>
</template>

<script setup lang="ts">
import {IonIcon, IonButton} from '@ionic/vue';

</script>

<style scoped>
.about-card {
  background: var(--ion-card-background, #ffffff);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.about-text {
  flex: 1;
}

.about-text h3 {
  margin: 0 0 8px 0;
  font-size: 18px;
  font-weight: bold;
  color: var(--ion-text-color);
}

.about-text p {
  margin: 0 0 16px 0;
  font-size: 14px;
  color: var(--ion-color-step-600, #666666);
}

.explore-button {
  --color: var(--ion-color-primary);
  --padding-start: 0;
  --padding-end: 0;
  font-weight: bold;
}

.about-image {
  width: 120px;
  height: auto;
  object-fit: contain;
}
</style>

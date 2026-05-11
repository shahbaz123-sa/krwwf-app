<template>
  <div class="event-card">
    <div class="event-header">
      <div class="event-title">Upcoming Event</div>
      <a href="/events" class="event-view-all">View All</a>
    </div>
    <img :src="event.image" alt="Event Image" class="event-image" />
    <div class="event-details">
      <div class="event-name">{{ event.title }}</div>
      <div class="event-info">
        <ion-icon name="calendar-outline" class="event-icon" aria-hidden="true"></ion-icon>
        <span class="event-date">{{ event.date }}</span>
      </div>
      <div class="event-info">
        <ion-icon name="location-outline" class="event-icon" aria-hidden="true"></ion-icon>
        <span class="event-location">{{ event.location }}</span>
      </div>
    </div>
    <button class="event-btn" @click="goToLogin">
      <ion-icon name="lock-closed-outline" class="lock-icon" aria-hidden="true"></ion-icon>
      Log in to register event
    </button>
  </div>
</template>

<script setup lang="ts">
import { DashboardEvent } from "@/types/dashboard";
import { IonIcon } from '@ionic/vue';
import { useRouter } from "vue-router";

const router = useRouter();

defineProps<{ event: DashboardEvent }>();

function goToLogin() {
  router.push('/login');
}
</script>

<style scoped>
.event-card {
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 16px;
  max-width: 320px;
  text-align: left;
  font-family: Arial, sans-serif;
}

.event-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.event-title {
  font-size: 16px;
  font-weight: bold;
  color: #003d1f;
}

.event-view-all {
  font-size: 14px;
  color: #007b83;
  text-decoration: none;
  font-weight: bold;
}

.event-view-all:hover {
  text-decoration: underline;
}

.event-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 10px 0;
}

.event-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 15px;
}

.event-name {
  font-size: 16px;
  font-weight: bold;
  color: #003d1f;
}

.event-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.event-icon {
  font-size: 16px;
  color: #666;
}

.event-date {
  font-size: 14px;
  color: #666;
}

.event-location {
  font-size: 14px;
  color: #999;
}

.event-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e8f5e9;
  color: #003d1f;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
}

.event-btn:hover {
  background-color: #c8e6c9;
}

.lock-icon {
  font-size: 16px;
  color: #003d1f;
  margin-right: 8px;
}
</style>

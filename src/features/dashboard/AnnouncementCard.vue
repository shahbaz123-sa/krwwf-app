<template>
  <div class="announcement-card">
    <div class="announcement-header">
      <div class="announcement-title">Recent Announcements</div>
      <a href="/announcements" class="announcement-view-all">View All</a>
    </div>
    <div class="announcement-list">
      <div v-for="announcement in announcements" :key="announcement.title" class="announcement-item">
        <div class="announcement-icon">
          <ion-icon
              :icon="announcement.icon"
              :style="{ color: announcement.color }"
              aria-hidden="true"
          />
        </div>
        <div class="announcement-content">
          <div class="announcement-name">{{ announcement.title }}</div>
          <div class="announcement-desc">{{ announcement.desc }}</div>
          <div class="announcement-date">{{ announcement.date }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { DashboardAnnouncement } from "@/types/dashboard";
import { IonIcon } from '@ionic/vue';

defineProps<{ announcements: (DashboardAnnouncement & { color: string })[] }>();
</script>

<style scoped>
.announcement-card {
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 16px;
  max-width: 400px;
  font-family: Arial, sans-serif;
}

.announcement-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.announcement-title {
  font-size: 16px;
  font-weight: bold;
  color: #003d1f;
}

.announcement-view-all {
  font-size: 14px;
  color: #007b83;
  text-decoration: none;
  font-weight: bold;
}

.announcement-view-all:hover {
  text-decoration: underline;
}

.announcement-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.announcement-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.announcement-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.announcement-icon ion-icon {
  font-size: 24px;
}

.announcement-content {
  display: flex;
  flex-direction: column;
}

.announcement-name {
  font-size: 16px;
  font-weight: bold;
  color: #003d1f;
}

.announcement-desc {
  font-size: 14px;
  color: #666;
}

.announcement-date {
  font-size: 12px;
  color: #999;
}
</style>

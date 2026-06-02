/* Styles to be added */
// Props and logic to be added
<template>
  <div class="event-card">
    <img :src="getImageSrc" class="event-card-img" alt="Event image" />
    <div class="event-card-details">
      <div class="event-card-title-row">
        <span class="event-card-title">{{ event.title }}</span>
        <span class="event-card-status" :style="{ background: statusColor(event.status) }">
          {{ event.status }}
        </span>
      </div>
      <div class="event-card-info">
        <ion-icon :icon="calendarOutline" />
        <span>{{ event.date }}</span>
        <span class="event-card-dot">•</span>
        <span>{{ event.time }}</span>
      </div>
      <div class="event-card-info event-location-row" @click="openInGoogleMaps(event.lat, event.lng, event.location)">
        <ion-icon :icon="locationOutline" />
        <span>{{ event.location }}</span>
      </div>
      <div class="event-card-actions">
        <ion-button fill="outline" size="small" class="event-card-action-btn">
          <span class="event-card-btn-content">
            <ion-icon :icon="eyeOutline" />
            <span>View</span>
          </span>
        </ion-button>
        <ion-button fill="outline" size="small" color="warning" class="event-card-action-btn">
          <span class="event-card-btn-content">
            <ion-icon :icon="createOutline" />
            <span>Edit</span>
          </span>
        </ion-button>
        <ion-button fill="outline" size="small" color="danger" class="event-card-action-btn">
          <span class="event-card-btn-content">
            <ion-icon :icon="trashOutline" />
            <span>Delete</span>
          </span>
        </ion-button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { defineProps, computed } from 'vue';
import { IonIcon, IonButton } from '@ionic/vue';
import { calendarOutline, locationOutline, eyeOutline, createOutline, trashOutline } from 'ionicons/icons';

const props = defineProps<{
  event: {
    id: number;
    title: string;
    status: string;
    date: string;
    time: string;
    location: string;
    image: string;
    lat?: number;
    lng?: number;
  }
}>();

// Compute the correct image src for Android and web
const getImageSrc = computed(() => {
  const img = props.event.image;
  // If image is an absolute URL, use as is
  if (/^https?:\/\//.test(img)) return img;
  // If image is a relative path, prepend public/ for Android
  if (img.startsWith('/')) return img; // already absolute from public/
  // Otherwise, assume it's in public/event-images or similar
  return `/${img}`;
});

function statusColor(status: string) {
  switch (status) {
    case 'Upcoming': return 'var(--ion-color-success)';
    case 'Completed': return 'var(--ion-color-primary)';
    case 'Draft': return '#bdbdbd';
    default: return '#e0e0e0';
  }
}

function openInGoogleMaps(lat?: number, lng?: number, location?: string) {
  if (lat && lng) {
    window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
  } else if (location) {
    window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`, '_blank');
  }
}
</script>
<style scoped>
.event-card {
  display: flex;
  flex-direction: row;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(32,97,58,0.10);
  padding: 14px 12px;
  gap: 12px;
  align-items: flex-start;
  max-width: 480px;
  min-width: 0;
  width: 100%;
}
.event-card-img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 10px;
  background: #f3f7f3;
  flex-shrink: 0;
}
.event-card-details {
  flex: 1 1 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}
.event-card-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}
.event-card-title {
  font-size: 0.7rem;
  font-weight: 700;
  color: #1a4d2e;
}
.event-card-status {
  font-size: 0.55rem;
  color: #fff;
  font-weight: 600;
  border-radius: 12px;
  padding: 2px 10px;
  margin-left: 4px;
  background: var(--ion-color-success);
  white-space: nowrap;
}
.event-card-info {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.45rem;
  color: #3a3a3a;
}
.event-card-info ion-icon {
  font-size: 0.6rem;
  color: #1a4d2e;
}
.event-card-dot {
  color: #bdbdbd;
  font-size: 1em;
  margin: 0 2px;
}
.event-card-actions {
  display: flex;
  flex-direction: row !important;
  gap: 4px;
  margin-top: 6px;
  flex-wrap: nowrap;
}
.event-card-action-btn {
  --border-radius: 7px;
  --padding-start: 6px;
  --padding-end: 6px;
  font-weight: 600;
  font-size: 0.4rem;
  min-width: 0;
  flex: 1 1 0;
  white-space: nowrap;
  --border-width: 1px;
}
.event-location-row {
  cursor: pointer;
  transition: background 0.15s;
  border-radius: 6px;
}
.event-location-row:hover {
  background: #f3f7f3;
}
/* Flex align icon and label in event card action buttons */
.event-card-btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  width: 100%;
}
/* Remove the media query that stacks image/details vertically on mobile. Always keep row layout. */
</style>

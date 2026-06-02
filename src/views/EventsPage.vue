<template>
  <ion-page class="events-page">
    <ion-header class="events-header">
      <div class="events-header-bg">
        <h1 class="events-title">Events</h1>
      </div>
    </ion-header>
    <ion-content :fullscreen="true" class="events-content">
      <div class="events-banner-card">
        <div class="events-banner-text">
          <h3>View upcoming and past events</h3>
          <p>Organize, engage and make a lasting impact with KRWWF.</p>
        </div>
        <img src="/src/assets/KhanzadaDay-01.jpg" class="events-banner-img" alt="Events Banner" />
      </div>
      <div class="events-controls-row">
        <ion-searchbar disabled class="events-search" placeholder="Search events..." />
      </div>
      <div class="events-list">
        <EventCard v-for="event in events" :key="event.id" :event="event" />
      </div>
      <ion-button disabled class="create-event-btn" expand="block" fill="solid" color="primary">
        <ion-icon slot="start" :icon="addOutline" />
        Create Event
      </ion-button>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { IonPage, IonHeader, IonContent, IonButton, IonIcon, IonSearchbar, IonSegment, IonSegmentButton } from '@ionic/vue';
import { addOutline, funnelOutline } from 'ionicons/icons';
import EventCard from '@/components/EventCard.vue';

// Example event data (replace with API data as needed)
const events = ref([
  {
    id: 1,
    title: 'Annual Gathering 2024',
    status: 'Upcoming',
    date: 'Sun, 15 Dec 2024',
    time: '05:00 PM – 08:30 PM',
    location: 'Pearl Continental Hotel, Lahore',
    image: '../src/assets/KhanzadaDay-01.jpg',
    lat: 31.5204,
    lng: 74.3560,
  },
  {
    id: 2,
    title: 'Blood Donation Camp',
    status: 'Upcoming',
    date: 'Sat, 02 Nov 2024',
    time: '10:00 AM – 03:00 PM',
    location: 'KRWWF Community Center, Islamabad',
    image: '../src/assets/KhanzadaDay-02.jpg',
    lat: 33.6844,
    lng: 73.0479,
  },
  {
    id: 3,
    title: 'Education Support Seminar',
    status: 'Completed',
    date: 'Fri, 20 Sep 2024',
    time: '02:00 PM – 04:30 PM',
    location: 'Islamabad Model College, Islamabad',
    image: '../src/assets/KhanzadaDay-03.jpg',
    lat: 33.6938,
    lng: 73.0652,
  },
  {
    id: 4,
    title: 'Community Welfare Drive',
    status: 'Draft',
    date: 'Sun, 08 Sep 2024',
    time: '09:00 AM – 12:00 PM',
    location: 'F-9 Park, Islamabad',
    image: '../src/assets/KhanzadaDay-03.jpg',
    lat: 33.6931,
    lng: 72.9789,
  },
]);
</script>

<style scoped>
.events-header {
  background: linear-gradient(180deg, #1a4d2e 80%, #fff 100%);
  border-bottom-left-radius: 24px;
  border-bottom-right-radius: 24px;
  padding-bottom: 12px;
  position: relative;
}
.events-header-bg {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 18px 0 18px;
}
.events-title {
  color: #fff;
  font-size: 2.1rem;
  font-weight: 700;
  margin: 0;
}

.create-event-btn {
  margin: 10px 0 50px 0 !important;
  font-weight: 600;
  border-radius: 20px;
  --background: #1a4d2e;
  --color: #fff;
  --box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.events-banner-card {
  display: flex;
  flex-direction: row;
  align-items: center;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 24px rgba(32,97,58,0.10);
  padding: 18px 18px 18px 0;
  margin: 18px 18px 12px 18px;
  gap: 18px;
}
.events-banner-img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 16px;
  background: #f3f7f3;
}
.events-banner-text h3 {
  font-size: 0.92rem;
  font-weight: 600;
  color: #1a4d2e;
  margin: 0 0 6px 0;
}
.events-banner-text p {
  font-size: 0.75rem;
  color: #3a3a3a;
  margin: 0;
}
.events-controls-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0 18px 10px 18px;
}
.events-search {
  flex: 1 1 0;
}
.events-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin: 0 18px;
}
@media (max-width: 700px) {
  .events-header-bg {
    flex-direction: column;
    align-items: flex-start;
    padding: 18px 10px 0 10px;
  }
  .events-title {
    font-size: 1.5rem;
  }
  .events-banner-card {
    flex-direction: row;
    align-items: center;
    padding: 10px 6px 10px 6px;
    margin: 10px 6px 6px 6px;
    gap: 10px;
  }
  .events-banner-img {
    min-width: 45px;
    max-height: 70px;
    border-radius: 10px;
  }
  .events-banner-text {
    flex: 1 1 0;
  }
  .events-controls-row {
    flex-direction: column;
    gap: 8px;
    margin: 0 10px 8px 10px;
  }
  .events-segment {
    margin: 0 10px 10px 10px;
  }
  .events-list {
    margin: 0 10px;
    gap: 10px;
  }
}
</style>

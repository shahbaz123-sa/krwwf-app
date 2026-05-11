<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { IonPage, IonContent } from '@ionic/vue';
import AppNavbar from "@/components/AppNavbar.vue";
import DashboardHeader from "@/components/DashboardHeader.vue";
import { fetchCurrentUser, logout, type AuthUser } from "@/services/auth";
import { useDashboardData } from "@/features/dashboard/dashboardService";
import StatCards from "@/features/dashboard/StatCards.vue";
import AboutSection from "@/features/dashboard/AboutSection.vue";
import EventCard from "@/features/dashboard/EventCard.vue";
import AnnouncementCard from "@/features/dashboard/AnnouncementCard.vue";

const router = useRouter();
const pageContentId = "dashboard-content";
const user = ref<AuthUser | null>(null);
const loading = ref(true);

const { stats, event, announcements } = useDashboardData();

onMounted(async () => {
  try {
    user.value = await fetchCurrentUser();
  } catch {
    try { await logout(); } catch { /* ignore logout errors */ }
  } finally {
    loading.value = false;
  }
});

function goToLogin() {
  void router.push('/login');
}

function goToCreateAccount() {
  void router.push('/register');
}

</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Dashboard" :content-id="pageContentId" />
    <ion-content class="dashboard-content">
      <DashboardHeader :user="user" :loading="loading" @login="goToLogin" @create-account="goToCreateAccount" />
      <div class="dashboard-main">
        <StatCards :stats="stats" />
        <AboutSection />
        <div class="event-announcement-row-below">
          <div class="event-announcement-row styled">
            <div class="event-card-outer">
              <EventCard :event="event" />
            </div>
            <div class="announcement-card-outer">
              <AnnouncementCard :announcements="announcements" />
            </div>
          </div>
        </div>
      </div>
      <div class="section">
        <div class="section-content">
          <h2>Connect. Collaborate. Empower.</h2>
          <p>Let's build a stronger Khanzada community together.</p>
          <a @click.prevent="goToCreateAccount" href="#" class="btn">Join Us Today →</a>
        </div>
        <div class="section-logo">
          <img src="/src/assets/logo.png" alt="Logo">
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.dashboard-content {
  --background: var(--ion-background-color);
  --padding-bottom: calc(84px + env(safe-area-inset-bottom));
  background: linear-gradient(
    135deg,
    rgba(var(--ion-color-primary-rgb), 0.06) 0%,
    rgba(var(--ion-background-color-rgb), 1) 50%,
    rgba(var(--ion-color-secondary-rgb), 0.08) 100%
  );
  min-height: 100vh;
  padding: 0;
}

.dashboard-main {
  max-width: 1100px;
  margin: -26px auto 0;
  padding: 0 10px 30px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  position: relative;
  z-index: 10;
}

.event-announcement-row {
  display: flex;
  gap: 14px;
}

.event-announcement-row-below {
  margin-top: 10px;
}

.event-announcement-row.styled {
  display: flex;
  gap: 18px;
  width: 100%;
  justify-content: space-between;
  margin: 0 auto;
  max-width: 900px;
}

.event-card-outer, .announcement-card-outer {
  background: #fff;
  border-radius: 22px;
  box-shadow: 0 4px 32px 0 rgba(60,60,60,0.10);
  border: 1.5px solid #f2f2f2;
  padding: 22px 18px 22px 18px;
  flex: 1 1 0;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.event-card-outer {
  margin-right: 0;
  margin-left: 0;
  margin-bottom: 0;
  margin-top: 0;
  max-width: 420px;
}

.announcement-card-outer {
  margin-left: 0;
  margin-right: 0;
  margin-bottom: 0;
  margin-top: 0;
  max-width: 420px;
}

@media (max-width: 900px) {
  .event-announcement-row.styled {
    flex-direction: column;
    gap: 14px;
    max-width: 98vw;
  }
  .event-card-outer, .announcement-card-outer {
    max-width: 100%;
    padding: 14px 8px 14px 8px;
    border-radius: 16px;
  }
}

@media (max-width: 600px) {
  .dashboard-main {
    margin-top: -18px;
    padding: 0 8px 20px;
    gap: 10px;
  }

  .section {
    padding: 15px 20px;
  }

  .section-content {
    max-width: 100%;
  }

  .section-content h2 {
    font-size: 6px;
  }

  .section-content p {
    font-size: 7px;
  }

  .section-content .btn {
    padding: 8px 16px;
    font-size: 9px;
  }

  .section-logo img {
    max-width: 80px;
  }
}

body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f4f4f4;
}

.section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #003d1f;
  color: #fff;
  border-radius: 10px;
  padding: 10px 10px;
  margin-left: 10px;
  margin-right: 10px;
  max-width: 900px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.section-content {
  max-width: 60%;
}

.section-content h2 {
  font-size: 12px;
  font-weight: bold;
  margin: 0;
  color: #f9a825;
}

.section-content p {
  margin: 10px 0;
  font-size: 9px;
}

.section-content .btn {
  display: inline-block;
  background-color: #f9a825;
  color: #003d1f;
  padding: 10px 20px;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
  margin-top: 5px;
}

.section-content .btn:hover {
  background-color: #d4a017;
}

.section-logo img {
  max-width: 100px;
  height: auto;
}
</style>

<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import AppNavbar from "@/components/AppNavbar.vue";
import { fetchCurrentUser, logout, type AuthUser } from "@/services/auth";
import { useTheme } from "@/composables/useTheme";

const router = useRouter();
const pageContentId = "profile-content";
const loading = ref(true);
const user = ref<AuthUser | null>(null);
const { isDark } = useTheme();

const defaultUser = {
  name: "Shahbaz Ahmad",
  title: "Software Engineer",
  mobile_number: "+92 300 1234567",
  email: "shahbazahmad@email.com",
  location: "Lahore, Pakistan",
  member_id: "KRWWF-2024-1587",
  profile_picture_url: null,
  profession: "Software Engineer",
  company: "TechSoft Solutions (Pvt.) Ltd.",
  experience: "3+ Years",
  skills: "Laravel, Vue.js, JavaScript, PHP, MySQL",
  role_in_community: "Volunteer",
  blood_group: "O+",
  interests: "Welfare, Education, IT Support",
  recent_activity: [
    { title: "Participated in Blood Donation Camp", date: "May 18, 2024" },
    { title: "Joined KRWWF Professionals Group", date: "April 22, 2024" },
    { title: "Contributed to Education Support Program", date: "March 10, 2024" }
  ]
};

const displayUser = computed(() => ({ ...defaultUser, ...(user.value || {}) }));

onMounted(async () => {
  try {
    user.value = await fetchCurrentUser();
  } catch {
    await logout();
    await router.replace("/home");
  } finally {
    loading.value = false;
  }
});

const goToEditProfile = async (): Promise<void> => {
  await router.push("/profile/edit");
};

function onMessage() {
  // simple stub for UI action
  void window.alert(`Message ${displayUser.value.name}`);
}

function onCall() {
  void window.alert(`Call ${displayUser.value.mobile_number}`);
}

function onConnect() {
  void window.alert(`Connect request sent to ${displayUser.value.name}`);
}
</script>

<template>
  <ion-page :id="pageContentId">
    <AppNavbar title="Profile" :content-id="pageContentId" />

    <ion-content class="ion-padding profile-content">
      <div class="profile-hero">
        <div class="hero-decor"></div>
      </div>

      <div class="profile-card">
        <div class="profile-theme-badge">{{ isDark ? '🌙 Dark' : '☀️ Light' }}</div>

        <div class="profile-top">
          <div class="avatar-wrap">
            <img
              v-if="displayUser.profile_picture_url"
              :src="displayUser.profile_picture_url"
              alt="Profile picture"
              class="profile-picture"
            />
            <div v-else class="profile-picture profile-picture--placeholder">
              {{ displayUser.name.charAt(0).toUpperCase() }}
            </div>
            <div class="avatar-badge">✔️</div>
          </div>

          <div class="profile-meta">
            <h2 class="profile-name">{{ displayUser.name }}</h2>
            <div class="profile-sub">{{ displayUser.title }}</div>
            <div class="profile-status">Active Member</div>
            <div class="profile-badges">
              <span class="badge">Proud Khanzada</span>
            </div>
          </div>
        </div>

        <div class="info-grid">
          <div class="info-card">
            <div class="info-row"><strong>Phone</strong><div class="info-val">{{ displayUser.mobile_number }}</div></div>
            <div class="info-row"><strong>Email</strong><div class="info-val">{{ displayUser.email }}</div></div>
          </div>
          <div class="info-card">
            <div class="info-row"><strong>Location</strong><div class="info-val">{{ displayUser.location }}</div></div>
            <div class="info-row"><strong>Member ID</strong><div class="info-val">{{ displayUser.member_id }}</div></div>
          </div>
        </div>

        <div class="action-row">
          <button class="action-btn" @click="onMessage">Message</button>
          <button class="action-btn" @click="onCall">Call</button>
          <button class="action-btn action-btn--primary" @click="onConnect">Connect</button>
          <button class="action-btn" @click="goToEditProfile">Edit</button>
        </div>

        <section class="section">
          <div class="section-head">Professional Details <a class="view-more">View More</a></div>
          <div class="detail-grid">
            <div class="detail-row"><span class="label">Profession</span><span class="val">{{ displayUser.profession }}</span></div>
            <div class="detail-row"><span class="label">Company</span><span class="val">{{ displayUser.company }}</span></div>
            <div class="detail-row"><span class="label">Experience</span><span class="val">{{ displayUser.experience }}</span></div>
            <div class="detail-row"><span class="label">Skills</span><span class="val">{{ displayUser.skills }}</span></div>
          </div>
        </section>

        <section class="section">
          <div class="section-head">Community & Interests <a class="view-more">View More</a></div>
          <div class="three-grid">
            <div class="pill"><strong>Role in Community</strong><div class="pill-sub">{{ displayUser.role_in_community }}</div></div>
            <div class="pill"><strong>Blood Group</strong><div class="pill-sub">{{ displayUser.blood_group }}</div></div>
            <div class="pill"><strong>Interests</strong><div class="pill-sub">{{ displayUser.interests }}</div></div>
          </div>
        </section>

        <section class="section">
          <div class="section-head">Recent Activity <a class="view-more">View All</a></div>
          <ul class="recent-list">
            <li v-for="(act, idx) in displayUser.recent_activity" :key="idx" class="recent-item">
              <div class="recent-title">{{ act.title }}</div>
              <div class="recent-date">{{ act.date }}</div>
            </li>
          </ul>
        </section>

      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.profile-content {
  --padding-bottom: calc(84px + env(safe-area-inset-bottom));
}

.profile-theme-badge {
  position: absolute;
  top: 12px;
  right: 18px;
  font-size: 12px;
  color: var(--app-muted-text-color);
  background: var(--app-surface-color, #fff);
  border-radius: 8px;
  padding: 2px 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  pointer-events: none;
}

.profile-title {
  margin-top: 0;
  color: var(--ion-text-color);
}

.profile-picture-wrap {
  display: flex;
  justify-content: center;
  margin: 12px 0 18px;
}

.profile-picture {
  width: 92px;
  height: 92px;
  border-radius: 999px;
  object-fit: cover;
  border: 2px solid var(--app-muted-border-color);
}

.profile-picture--placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(var(--ion-color-primary-rgb), 0.12);
  color: var(--ion-color-primary);
  font-weight: 700;
  font-size: 28px;
}

.profile-hero {
  height: 130px;
  background: linear-gradient(180deg, #0b6b3a 0%, #0f7a47 100%);
  position: relative;
}

.hero-decor {
  position: absolute;
  right: 16px;
  bottom: -12px;
  width: 120px;
  height: 120px;
  background: rgba(255,255,255,0.04);
  border-radius: 8px;
}

.profile-card {
  position: relative;
  max-width: 720px;
  margin: -42px auto 24px;
  background: var(--app-surface-color);
  border-radius: 12px;
  padding: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.profile-top {
  display: flex;
  flex-direction: column;
  gap: 14px;
  align-items: flex-start;
}

.avatar-wrap { position: relative; }

.avatar-badge {
  position: absolute;
  right: -6px;
  bottom: -6px;
  background: #fff;
  border-radius: 999px;
  padding: 4px 6px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  font-size: 12px;
}

.profile-meta { width: 100%; }

.profile-name { margin: 0; font-size: 20px; }
.profile-sub { color: var(--app-muted-text-color); margin-top: 4px; }
.profile-status { margin-top: 6px; font-size: 13px; color: #2f9a66; }
.profile-badges { margin-top: 8px; }
.badge { background: #f3f7f4; color:#155d36; padding: 6px 10px; border-radius: 18px; font-size: 12px; }

.info-grid { display:flex; flex-direction: column; gap:10px; margin-top: 14px; }
.info-card { flex:1; min-width: 0; background:var(--app-surface-2, #fff); padding:12px; border-radius:8px; }
.info-row { display:flex; flex-direction: column; gap: 4px; padding:6px 0; border-bottom:1px solid rgba(0,0,0,0.04); }
.info-val { color:var(--ion-text-color); }

.action-row { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:10px; margin:16px 0; }
.action-btn { min-width: 0; padding:10px 12px; border-radius:10px; border:1px solid rgba(0,0,0,0.06); background:transparent; }
.action-btn--primary { background:#0b6b3a; color:#fff; border-color:transparent; }

.section { margin-top: 12px; }
.section-head { font-weight:600; display:flex; justify-content:space-between; align-items:center; }
.view-more { font-size:12px; color:#888; text-decoration:underline; }
.detail-grid { margin-top:8px; border-radius:8px; background:var(--app-surface-2, #fff); padding:10px; }
.detail-row { display:flex; flex-direction: column; gap: 4px; padding:8px 0; border-bottom:1px solid rgba(0,0,0,0.04); }
.label { color:#666; }
.val { font-weight:600; }

.three-grid { display:flex; flex-direction: column; gap:10px; margin-top:10px; }
.pill { flex:1; background:var(--app-surface-2, #fff); padding:10px; border-radius:8px; }
.pill-sub { margin-top:6px; color:#555 }

.recent-list { list-style:none; padding:0; margin:8px 0 0 0; }
.recent-item { padding:10px 0; border-bottom:1px solid rgba(0,0,0,0.04); display:flex; flex-direction: column; gap: 4px; }
.recent-title { color:var(--ion-text-color); }
.recent-date { color:#999; font-size:12px; }

.profile-row {
  margin: 10px 0;
  color: var(--ion-text-color);
}

@media (min-width: 768px) {
  .profile-hero {
    height: 160px;
  }

  .profile-card {
    margin-top: -56px;
    padding: 18px;
  }

  .profile-top {
    flex-direction: row;
    align-items: center;
    gap: 18px;
  }

  .profile-meta {
    width: auto;
    flex: 1;
  }

  .info-grid {
    flex-direction: row;
    gap: 12px;
  }

  .info-row,
  .detail-row {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }

  .action-row {
    display: flex;
  }

  .action-btn {
    flex: 1;
  }

  .three-grid {
    flex-direction: row;
  }

  .recent-item {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

@media (min-width: 1024px) {
  .profile-card {
    max-width: 920px;
  }
}
</style>

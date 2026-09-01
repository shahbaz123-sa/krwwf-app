<template>
  <div class="dashboard-header">
    <div class="dashboard-header-bg" aria-hidden="true"></div>
    <div class="dashboard-header-content">
      <div class="dashboard-header-row">
        <button class="icon-btn" @click="$emit('menu')" aria-label="Open menu">
<!--          <IonIcon :icon="menuOutline" />-->
        </button>
        <button class="icon-btn" @click="$emit('notifications')" aria-label="Open notifications">
          <IonIcon :icon="notificationsOutline" />
        </button>
      </div>

<!--      <div class="dashboard-header-main">-->
        <div class="dashboard-header-main-left">
          <div class="welcome-text">Welcome to</div>
          <div class="khanzada-title">KHANZADA</div>
          <div class="khanzada-sub">Rajput Welfare & Waseela Foundation</div>
          <div class="khanzada-desc">
            Together, let's build a stronger Khanzada community.
          </div>
          <div class="dashboard-header-buttons">
            <button class="header-btn outline" @click="$emit('login')">Log In</button>
            <button class="header-btn solid" @click="$emit('create-account')">Create Account</button>
          </div>
        </div>
<!--      </div>-->
    </div>
  </div>
</template>

<script setup lang="ts">
import { IonIcon } from '@ionic/vue';
import { menuOutline, notificationsOutline } from 'ionicons/icons';
import headerLogo from '@/assets/logo.png';

defineEmits(['menu', 'notifications', 'login', 'create-account']);
</script>

<style scoped>
.dashboard-header {
  position: relative;
  min-height: 350px;
  border-bottom-left-radius: 34px;
  border-bottom-right-radius: 34px;
  overflow: hidden;
  color: #fff;
}

.dashboard-header-bg {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(115deg, rgba(7, 38, 26, 0.82) 0%, rgba(7, 38, 26, 0.64) 48%, rgba(7, 38, 26, 0.78) 100%),
    url('@/assets/castle.jpg') center center / cover no-repeat;
  z-index: 1;
}

.dashboard-header-content {
  position: relative;
  z-index: 2;
  min-height: inherit;
  padding: calc(14px + env(safe-area-inset-top)) 22px 30px;
  display: flex;
  flex-direction: column;
}

.dashboard-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.icon-btn {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.95);
  font-size: 30px;
  padding: 4px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.dashboard-header-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-top: 34px;
  gap: 16px;
}

.dashboard-header-main-left {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.dashboard-header-logos-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 42%;
}

.header-logo {
  width: 100%;
  max-width: 220px;
  height: auto;
  object-fit: contain;
}

.welcome-text {
  font-size: 16px;
  margin-bottom: 6px;
  color: rgba(255, 255, 255, 0.95);
}

.khanzada-title {
  font-size: clamp(20px, 10.4vw, 34px);
  font-weight: 800;
  letter-spacing: 0.8px;
  margin-bottom: 6px;
  line-height: 0.98;
}

.khanzada-sub {
  font-size: clamp(9px, 5.2vw, 16px);
  font-weight: 500;
  line-height: 1.18;
  color: rgba(255, 255, 255, 0.95);
}

.khanzada-desc {
  margin-top: 18px;
  margin-bottom: 28px;
  font-size: clamp(12px, 4.7vw, 12px);
  line-height: 1.25;
  color: rgba(255, 255, 255, 0.92);
  font-weight: 400;
}

.dashboard-header-buttons {
  display: flex;
  width: 80%;
  gap: 10px;
  margin-top: 16px;
}

.header-btn {
  min-width: 0;
  flex: 1;
  padding: 12px 16px;
  font-size: 15px;
  font-weight: 700;
  border-radius: 16px;
  border: 2px solid transparent;
  background: transparent;
  color: #fff;
  cursor: pointer;
  transition: 0.2s ease;
}

.header-btn.solid {
  background: #fff;
  color: #174637;
  border-color: #fff;
}

.header-btn.outline {
  background: rgba(8, 42, 31, 0.42);
  border-color: rgba(183, 219, 206, 0.45);
  color: #fff;
}

.header-btn.outline:hover,
.header-btn.solid:hover {
  transform: translateY(-1px);
}

@media (max-width: 600px) {
  .dashboard-header {
    min-height: 348px;
    border-bottom-left-radius: 26px;
    border-bottom-right-radius: 26px;
  }

  .dashboard-header-content {
    padding: calc(10px + env(safe-area-inset-top)) 16px 24px;
  }

  .dashboard-header-main {
    margin-top: 14px;
    gap: 10px;
  }

  .dashboard-header-logos-row {
    width: 36%;
  }

  .welcome-text {
    font-size: 11px;
    margin-bottom: 3px;
  }


  .header-btn {
    padding: 10px 10px;
    font-size: 12px;
    border-radius: 14px;
  }
}
</style>

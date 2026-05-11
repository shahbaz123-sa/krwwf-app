<template>
  <div class="stat-card" :class="`stat-card--${variantIndex}`">
    <div class="icon-wrapper">
      <IonIcon :icon="icon" class="stat-icon" />
    </div>
    <div class="stat-value">{{ value }}</div>
    <div class="stat-title">{{ title }}</div>
    <div v-if="trend" class="stat-trend">{{ trend }}</div>
  </div>
</template>

<script setup lang="ts">
import { IonIcon } from '@ionic/vue';
defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: true,
  },
  trend: {
    type: String,
    required: false,
  },
  icon: {
    type: String,
    required: true,
  },
  variantIndex: {
    type: Number,
    default: 0,
  },
});
</script>

<style scoped>
.stat-card {
  background: transparent;
  border-radius: 12px;
  padding: 12px 4px 10px;
  text-align: center;
  box-shadow: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  gap: 2px;
}

.icon-wrapper {
  width: 54px;
  height: 34px;
  border-radius: 999px;
  background: #dcebe3;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}

.stat-icon {
  font-size: 22px;
  color: #1d8558;
}

.stat-value {
  font-size: clamp(26px, 4.7vw, 44px);
  line-height: 1.1;
  font-weight: 700;
  color: #11362a;
}

.stat-title {
  margin-top: 6px;
  font-size: clamp(13px, 2.8vw, 29px);
  line-height: 1.15;
  color: #2e4339;
}

.stat-trend {
  margin-top: 8px;
  font-size: clamp(12px, 2.5vw, 24px);
  line-height: 1.1;
  color: #188354;
  font-weight: 500;
}

.stat-card--1 .icon-wrapper {
  background: #deebe5;
}

.stat-card--2 .icon-wrapper {
  background: #e8dcfb;
}

.stat-card--2 .stat-icon {
  color: #8c66de;
}

.stat-card--3 .icon-wrapper {
  background: #f7edd9;
}

.stat-card--3 .stat-icon {
  color: #c58a14;
}

@media (max-width: 600px) {
  .stat-card {
    padding: 10px 2px 8px;
  }

  .icon-wrapper {
    width: 46px;
    height: 30px;
    margin-bottom: 6px;
  }

  .stat-icon {
    font-size: 18px;
  }

  .stat-value {
    font-size: clamp(20px, 5.6vw, 30px);
  }

  .stat-title {
    font-size: clamp(10px, 2.9vw, 14px);
    margin-top: 4px;
  }

  .stat-trend {
    font-size: clamp(9px, 2.6vw, 12px);
    margin-top: 6px;
  }
}
</style>

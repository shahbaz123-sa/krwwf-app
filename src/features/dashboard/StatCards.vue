<template>
  <div class="stat-cards-row">
    <template v-for="(stat, index) in stats" :key="stat.title">
      <StatCard
        :title="stat.title"
        :value="stat.value"
        :trend="stat.trend"
        :icon="stat.icon"
        :variant-index="index"
      />
      <div v-if="index < stats.length - 1" class="stat-divider" aria-hidden="true"></div>
    </template>
  </div>
</template>

<script setup lang="ts">
import StatCard from "@/components/StatCard.vue";
import { DashboardStat } from "@/types/dashboard";

defineProps<{ stats: DashboardStat[] }>();
</script>

<style scoped>
.stat-cards-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  align-items: stretch;
  gap: 0;
  margin-bottom: 10px;
  background: #fff;
  border-radius: 22px;
  box-shadow: 0 8px 28px rgba(6, 29, 21, 0.14);
  padding: 10px 6px;
  position: relative;
  z-index: 2;
}

.stat-cards-row > *:not(.stat-divider) {
  min-width: 0;
}

.stat-divider {
  display: none;
}

@media (min-width: 601px) {
  .stat-cards-row {
    padding: 14px 10px;
  }

  .stat-divider {
    display: block;
  width: 1px;
    background: #e3e9e5;
  align-self: center;
    height: 120px;
    grid-row: 1;
  }

  .stat-cards-row > :nth-child(2) { grid-column: 2; }
  .stat-cards-row > :nth-child(4) { grid-column: 4; }
  .stat-cards-row > :nth-child(6) { grid-column: 6; }
  .stat-cards-row > :nth-child(8) { grid-column: 8; }

  .stat-cards-row {
    grid-template-columns: 1fr 1px 1fr 1px 1fr 1px 1fr;
  }
}

@media (max-width: 900px) and (min-width: 601px) {
  .stat-cards-row {
    border-radius: 20px;
    padding: 10px 6px;
  }
}

@media (max-width: 600px) {
  .stat-cards-row {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    border-radius: 18px;
    padding: 6px 2px;
    box-shadow: 0 6px 18px rgba(6, 29, 21, 0.12);
  }
}
</style>

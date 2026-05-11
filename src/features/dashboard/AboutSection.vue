<template>
  <div class="about-section">
    <div class="about-card compact">
      <div class="about-content compact">
        <div class="about-title compact">About Khanzada</div>
        <div class="about-desc small compact">
          The Khanzada tribe has a rich history of courage, unity and leadership. Our ancestors played a vital role in the region's heritage and we continue to uphold the values of honor, integrity and brotherhood.
        </div>
        <button class="about-btn compact" @click="goToAbout">
          Learn More
          <span class="about-btn-icon compact">&#x2192;</span>
        </button>
      </div>
      <div class="about-img-wrapper compact">
        <img src="/src/assets/castle.png" alt="About Khanzada" class="about-img prominent compact" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No props needed for now
import { useRouter } from "vue-router";
const router = useRouter();
function goToAbout() {
  router.push('/about');
}
</script>

<style scoped>

.about-section {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  background: transparent;
}

.about-card {
  display: flex;
  flex-direction: row;
  align-items: stretch;
  background: #fff;
  box-shadow: 0 4px 32px 0 rgba(60,60,60,0.10);
  border-radius: 22px;
  padding: 36px 36px 36px 36px;
  gap: 36px;
  width: 100%;
  max-width: 700px;
  min-height: 240px;
  border: 1.5px solid #f2f2f2;
}

.about-card.compact {
  gap: 18px;
  padding: 18px 12px 18px 12px;
  max-width: 420px;
  min-height: unset;
  align-items: center; /* Ensure vertical centering of children */
}

.about-content {
  flex: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.about-content.compact {
  flex: 2;
}

.about-title {
  color: #20613a;
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 16px;
  letter-spacing: -0.5px;
}

.about-title.compact {
  font-size: 1.15rem;
  margin-bottom: 8px;
}

.about-desc {
  color: #222;
  font-size: 1.13rem;
  margin-bottom: 28px;
  line-height: 1.6;
}
.about-desc.small {
  font-size: 0.98rem;
}

.about-desc.small.compact {
  font-size: 0.82rem;
  margin-bottom: 14px;
}

.about-btn {
  display: inline-flex;
  align-items: center;
  background: #20613a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 18px 38px;
  font-size: 1.25rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.10);
  margin-top: 8px;
}
.about-btn:hover {
  background: #174c2c;
}
.about-btn.compact {
  padding: 8px 18px;
  font-size: 0.95rem;
  border-radius: 8px;
  margin-top: 4px;
}
.about-btn-icon {
  font-size: 1.4em;
  margin-left: 14px;
  font-weight: bold;
}
.about-btn-icon.compact {
  font-size: 1.1em;
  margin-left: 8px;
}

.about-img-wrapper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex: 1;
}

.about-img-wrapper.compact {
  flex: 1;
  display: flex;
  align-items: center; /* Center vertically */
  justify-content: flex-end;
}

.about-img {
  max-width: 180px;
  max-height: 140px;
  object-fit: cover;
  align-self: center;
}

@media (max-width: 600px) {
  .about-card.compact {
    flex-direction: row;
    align-items: center; /* Center vertically on mobile too */
    gap: 10px;
    padding: 10px 4px 10px 4px;
    max-width: 98vw;
  }
  .about-img-wrapper.compact {
    margin-top: 0;
    justify-content: flex-end;
    align-items: center; /* Center vertically on mobile too */
  }
  .about-img.compact {
    max-width: 120px;
    max-height: 120px;
  }
  .about-title.compact {
    font-size: 1rem;
  }
  .about-desc.small.compact {
    font-size: 0.72rem;
  }
  .about-btn.compact {
    font-size: 0.82rem;
    padding: 6px 12px;
  }
}
</style>

<template>
  <section id="krwwf-about" class="krwwf-about">
    <div class="about-header-card">
      <div class="about-header-left">
        <img :src="crestLogo" alt="KRWWF Logo" class="about-header-logo" />
        <div class="about-header-texts">
          <h2 class="about-header-title">
            Khanzada Rajput Welfare &amp; Waseela Foundation
          </h2>
          <p class="about-header-subtitle">A Waseela of Hope, Support, and Welfare</p>
        </div>
      </div>
      <img :src="castleImage" alt="KRWWF cover" class="about-header-cover" />
    </div>

    <div class="about-intro-card">
      <div class="about-intro-icon">
        <IonIcon :icon="handLeftOutline" />
      </div>
      <div class="about-intro-content">
        <h3>About KRWWF</h3>
        <p>
          KRWWF is a community-focused welfare foundation dedicated to serving humanity,
          supporting deserving families, and creating meaningful opportunities for a better future.
          We work with the values of compassion, unity, and social responsibility to help those
          facing hardship.
        </p>
      </div>
    </div>

    <div class="about-mission-vision-row">
      <article class="about-mv-card" v-for="card in missionVision" :key="card.title">
        <IonIcon :icon="card.icon" />
        <h4>{{ card.title }}</h4>
        <p>{{ card.description }}</p>
      </article>
    </div>

    <div class="about-block">
      <div class="about-block-header">
        <div class="about-block-title">
          <IonIcon :icon="handLeftOutline" />
          <span>What We Do</span>
        </div>
      </div>
      <div class="about-services-grid">
        <div class="about-service-item" v-for="item in whatWeDo" :key="item.label">
          <IonIcon :icon="item.icon" />
          <span>{{ item.label }}</span>
        </div>
      </div>
    </div>

    <div class="about-block">
      <div class="about-block-header">
        <div class="about-block-title">
          <IonIcon :icon="shieldCheckmarkOutline" />
          <span>Our Values</span>
        </div>
      </div>
      <div class="about-values-grid">
        <div class="about-value-pill" v-for="value in values" :key="value.label">
          <IonIcon :icon="value.icon" />
          <span>{{ value.label }}</span>
        </div>
      </div>
    </div>

    <div class="about-join-card">
      <img :src="forestFooter" alt="Join our cause" class="about-join-image" />
      <div class="about-join-content">
        <h4>Join Our Cause</h4>
        <p>
          Your support can become a source of hope for someone in need. Whether through donations,
          volunteering, or spreading awareness, you can help us make a difference.
        </p>
        <div class="about-join-actions">
          <button type="button" class="about-join-btn donate">
            <IonIcon :icon="cardOutline" />
            Donate Now
          </button>
          <button type="button" class="about-join-btn volunteer">
            <IonIcon :icon="peopleOutline" />
            Become a Volunteer
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { IonIcon } from '@ionic/vue';
import {
  handLeftOutline,
  locateOutline,
  eyeOutline,
  schoolOutline,
  medkitOutline,
  peopleOutline,
  peopleCircleOutline,
  heartOutline,
  shieldCheckmarkOutline,
  ribbonOutline,
  constructOutline,
  cardOutline,
} from 'ionicons/icons';
import crestLogo from '@/assets/logo.png';
import castleImage from '@/assets/castle.jpg';
import forestFooter from '@/assets/forest-footer.png';

const missionVision = [
  {
    icon: locateOutline,
    title: 'Our Mission',
    description:
      'To support deserving individuals and families through welfare services, education assistance, healthcare aid, and emergency relief.',
  },
  {
    icon: eyeOutline,
    title: 'Our Vision',
    description:
      'To build a caring, empowered, and united society where no one is left behind due to poverty or lack of resources.',
  },
];

const whatWeDo = [
  { icon: handLeftOutline, label: 'Family Support' },
  { icon: schoolOutline, label: 'Education Assistance' },
  { icon: medkitOutline, label: 'Medical Help' },
  { icon: medkitOutline, label: 'Emergency Relief' },
  { icon: peopleOutline, label: 'Community Welfare' },
  { icon: peopleCircleOutline, label: 'Volunteer Support' },
];

const values = [
  { icon: heartOutline, label: 'Compassion' },
  { icon: shieldCheckmarkOutline, label: 'Integrity' },
  { icon: peopleOutline, label: 'Unity' },
  { icon: ribbonOutline, label: 'Dignity' },
  { icon: constructOutline, label: 'Service' },
  { icon: eyeOutline, label: 'Transparency' },
];
</script>

<style scoped>
.krwwf-about {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: clamp(12px, 2vw, 18px);
}

.about-header-card {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  gap: 12px;
  background: #155c36;
  border-radius: clamp(14px, 2vw, 20px);
  padding: clamp(14px, 2.5vw, 22px);
  box-shadow: 0 2px 12px rgba(32, 97, 58, 0.12);
  overflow: hidden;
  position: relative;
  min-height: clamp(110px, 18vw, 150px);
}

.about-header-left {
  display: flex;
  align-items: center;
  gap: clamp(10px, 2vw, 16px);
  z-index: 2;
  min-width: 0;
}

.about-header-logo {
  width: clamp(48px, 10vw, 72px);
  height: clamp(48px, 10vw, 72px);
  border-radius: 12px;
  background: #fff;
  object-fit: contain;
  border: 2px solid #e6f2ed;
  flex-shrink: 0;
}

.about-header-texts {
  min-width: 0;
}

.about-header-title {
  color: #fff;
  font-size: clamp(0.95rem, 2.4vw, 1.35rem);
  font-weight: 700;
  margin: 0 0 6px;
  line-height: 1.25;
}

.about-header-subtitle {
  color: #f9a825;
  font-size: clamp(0.72rem, 1.8vw, 0.95rem);
  font-weight: 500;
  margin: 0;
  line-height: 1.3;
}

.about-header-cover {
  width: clamp(88px, 22vw, 150px);
  height: 100%;
  min-height: 88px;
  object-fit: cover;
  border-radius: 12px;
  z-index: 1;
}

.about-intro-card,
.about-mv-card,
.about-block,
.about-join-card {
  background: #fff;
  border-radius: clamp(12px, 2vw, 16px);
  box-shadow: 0 2px 8px rgba(32, 97, 58, 0.06);
  border: 1px solid #eef2ef;
}

.about-intro-card {
  display: flex;
  gap: clamp(10px, 2vw, 14px);
  padding: clamp(14px, 2.5vw, 20px);
  align-items: flex-start;
}

.about-intro-icon {
  background: #155c36;
  color: #fff;
  border-radius: 50%;
  width: clamp(34px, 8vw, 42px);
  height: clamp(34px, 8vw, 42px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.about-intro-content h3 {
  margin: 0 0 6px;
  color: #155c36;
  font-size: clamp(1rem, 2.2vw, 1.2rem);
}

.about-intro-content p {
  margin: 0;
  color: #333;
  font-size: clamp(0.82rem, 1.8vw, 0.95rem);
  line-height: 1.55;
}

.about-mission-vision-row {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: clamp(10px, 2vw, 14px);
}

.about-mv-card {
  padding: clamp(12px, 2.2vw, 18px);
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.about-mv-card ion-icon {
  color: #155c36;
  font-size: 1.35rem;
}

.about-mv-card h4 {
  margin: 0;
  color: #155c36;
  font-size: clamp(0.92rem, 2vw, 1.05rem);
}

.about-mv-card p {
  margin: 0;
  color: #333;
  font-size: clamp(0.78rem, 1.7vw, 0.9rem);
  line-height: 1.45;
}

.about-block {
  padding: clamp(12px, 2.2vw, 18px);
}

.about-block-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.about-block-title {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #155c36;
  font-size: clamp(0.95rem, 2vw, 1.1rem);
  font-weight: 700;
}

.about-services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
  gap: 10px;
}

.about-service-item {
  background: #f5f8f6;
  border-radius: 12px;
  padding: 12px 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
  color: #155c36;
  font-size: clamp(0.72rem, 1.6vw, 0.85rem);
  font-weight: 600;
}

.about-service-item ion-icon {
  font-size: 1.35rem;
}

.about-values-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.about-value-pill {
  background: #f5f8f6;
  border-radius: 999px;
  padding: 8px 14px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #155c36;
  font-size: clamp(0.72rem, 1.6vw, 0.85rem);
  font-weight: 600;
}

.about-value-pill ion-icon {
  font-size: 1rem;
}

.about-join-card {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: clamp(10px, 2vw, 16px);
  padding: clamp(12px, 2.2vw, 18px);
  background: #fff7e6;
  align-items: center;
}

.about-join-image {
  width: clamp(72px, 16vw, 110px);
  height: clamp(72px, 16vw, 110px);
  object-fit: contain;
  border-radius: 12px;
  background: #fff;
}

.about-join-content h4 {
  margin: 0 0 6px;
  color: #155c36;
  font-size: clamp(0.95rem, 2vw, 1.1rem);
}

.about-join-content p {
  margin: 0 0 10px;
  color: #333;
  font-size: clamp(0.78rem, 1.7vw, 0.9rem);
  line-height: 1.45;
}

.about-join-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.about-join-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: none;
  border-radius: 8px;
  font-size: clamp(0.75rem, 1.6vw, 0.88rem);
  font-weight: 600;
  padding: 8px 14px;
  cursor: pointer;
}

.about-join-btn.donate {
  background: #155c36;
  color: #fff;
}

.about-join-btn.donate ion-icon {
  color: #f9a825;
}

.about-join-btn.volunteer {
  background: #fff;
  color: #155c36;
  border: 1.5px solid #155c36;
}

@media (max-width: 768px) {
  .about-mission-vision-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 560px) {
  .about-header-card {
    grid-template-columns: 1fr;
  }

  .about-header-cover {
    width: 100%;
    max-height: 120px;
  }

  .about-intro-card {
    flex-direction: column;
  }

  .about-join-card {
    grid-template-columns: 1fr;
  }

  .about-join-image {
    justify-self: start;
  }

  .about-join-actions {
    flex-direction: column;
  }

  .about-join-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>

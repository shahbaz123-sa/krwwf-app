<template>
  <ion-page class="about-page">
    <ion-content :fullscreen="true" class="about-content">
      <!-- Header Card -->
      <div class="about-header-card">
        <div class="about-header-left">
          <img src="/src/assets/krwwf-crest.png" alt="Logo" class="about-header-logo" />
          <div class="about-header-texts">
            <h1 class="about-header-title">Khanzada Rajput Welfare &<br />Waseela Foundation</h1>
            <div class="about-header-subtitle">A Waseela of Hope, Support, and Welfare</div>
          </div>
        </div>
        <img src="/src/assets/castle.png" alt="Cover" class="about-header-cover" />
      </div>

      <!-- About Section -->
      <div class="about-section">
        <div class="about-section-icon">
          <ion-icon name="hand-heart-outline"></ion-icon>
        </div>
        <div class="about-section-content">
          <h2>About KRWWF</h2>
          <p>
            KRWWF is a community-focused welfare foundation dedicated to serving humanity, supporting deserving families, and creating meaningful opportunities for a better future.<br /><br />
            We work with the values of compassion, unity, and social responsibility to help those facing hardship.
          </p>
        </div>
      </div>

      <!-- Mission & Vision Cards -->
      <div class="about-mission-vision-row">
        <div class="about-mission-vision-card">
          <ion-icon name="target-outline"></ion-icon>
          <div class="about-mv-title">Our Mission</div>
          <div class="about-mv-desc">
            To support deserving individuals and families through welfare services, education assistance, healthcare aid, and emergency relief.
          </div>
        </div>
        <div class="about-mission-vision-card">
          <ion-icon name="eye-outline"></ion-icon>
          <div class="about-mv-title">Our Vision</div>
          <div class="about-mv-desc">
            To build a caring, empowered, and united society where no one is left behind due to poverty or lack of resources.
          </div>
        </div>
      </div>

      <!-- What We Do Section -->
      <div class="about-what-we-do-row">
        <div class="about-what-title-row">
          <div class="about-what-title">
            <ion-icon name="hand-heart-outline"></ion-icon>
            <span>What We Do</span>
          </div>
          <a class="about-what-viewall" href="#">View All <ion-icon name="chevron-forward-outline"></ion-icon></a>
        </div>
        <div class="about-what-icons-row">
          <div class="about-what-icon-col" v-for="item in whatWeDo" :key="item.label">
            <ion-icon :name="item.icon"></ion-icon>
            <span>{{ item.label }}</span>
          </div>
        </div>
      </div>

      <!-- Our Values Section -->
      <div class="about-values-row">
        <div class="about-values-title-row">
          <ion-icon name="shield-checkmark-outline"></ion-icon>
          <span>Our Values</span>
        </div>
        <div class="about-values-pills-row">
          <div class="about-value-pill" v-for="value in values" :key="value.label">
            <ion-icon :name="value.icon"></ion-icon>
            <span>{{ value.label }}</span>
          </div>
        </div>
      </div>

      <!-- Join Our Cause Card -->
      <div class="about-join-cause-card">
        <img src="/src/assets/forest-footer.png" alt="Join Cause" class="about-join-cause-img" />
        <div class="about-join-cause-content">
          <div class="about-join-cause-title">Join Our Cause</div>
          <div class="about-join-cause-desc">
            Your support can become a source of hope for someone in need. Whether through donations, volunteering, or spreading awareness, you can help us make a difference.
          </div>
          <div class="about-join-cause-btns">
            <button class="about-join-btn donate"><ion-icon name="card-outline"></ion-icon> Donate Now</button>
            <button class="about-join-btn volunteer"><ion-icon name="people-outline"></ion-icon> Become a Volunteer</button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { IonPage, IonContent } from '@ionic/vue';
const whatWeDo = [
  { icon: 'hand-heart-outline', label: 'Family Support' },
  { icon: 'school-outline', label: 'Education Assistance' },
  { icon: 'medkit-outline', label: 'Medical Help' },
  { icon: 'medkit-outline', label: 'Emergency Relief' },
  { icon: 'people-outline', label: 'Community Welfare' },
  { icon: 'people-circle-outline', label: 'Volunteer Support' },
];
const values = [
  { icon: 'heart-outline', label: 'Compassion' },
  { icon: 'shield-checkmark-outline', label: 'Integrity' },
  { icon: 'people-outline', label: 'Unity' },
  { icon: 'ribbon-outline', label: 'Dignity' },
  { icon: 'construct-outline', label: 'Service' },
  { icon: 'eye-outline', label: 'Transparency' },
];
</script>

<style scoped>
.about-page {
  background: #f8fafb;
  min-height: 100vh;
}
.about-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0;
  background: #f8fafb;
}
.about-header-card {
  display: flex;
  align-items: center;
  background: #155c36;
  border-radius: 18px;
  margin: 18px 12px 18px 12px;
  padding: 18px 16px 18px 16px;
  box-shadow: 0 2px 12px 0 rgba(32,97,58,0.10);
  position: relative;
  min-height: 120px;
  overflow: hidden;
}
.about-header-left {
  display: flex;
  align-items: center;
  z-index: 2;
}
.about-header-logo {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: #fff;
  object-fit: contain;
  margin-right: 14px;
  border: 2px solid #e6f2ed;
}
.about-header-texts {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.about-header-title {
  color: #fff;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 4px;
  line-height: 1.2;
}
.about-header-subtitle {
  color: #f9a825;
  font-size: 0.85rem;
  font-weight: 500;
}
.about-header-cover {
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
  width: 120px;
  object-fit: cover;
  border-top-right-radius: 18px;
  border-bottom-right-radius: 18px;
  z-index: 1;
}

.about-section {
  display: flex;
  align-items: flex-start;
  margin: 0 14px 18px 14px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.06);
  padding: 16px 14px;
}
.about-section-icon {
  background: #155c36;
  color: #fff;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  margin-right: 12px;
}
.about-section-content h2 {
  font-size: 1rem;
  font-weight: 700;
  margin: 0 0 4px 0;
  color: #155c36;
}
.about-section-content p {
  font-size: 0.82rem;
  color: #222;
  margin: 0;
  line-height: 1.5;
}

.about-mission-vision-row {
  display: flex;
  gap: 12px;
  margin: 0 14px 18px 14px;
}
.about-mission-vision-card {
  flex: 1 1 0;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.06);
  padding: 14px 12px 12px 12px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-width: 0;
}
.about-mission-vision-card ion-icon {
  color: #155c36;
  font-size: 1.3rem;
  margin-bottom: 6px;
}
.about-mv-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #155c36;
  margin-bottom: 2px;
}
.about-mv-desc {
  font-size: 0.78rem;
  color: #222;
  line-height: 1.4;
}

.about-what-we-do-row {
  margin: 0 14px 18px 14px;
}
.about-what-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.about-what-title {
  display: flex;
  align-items: center;
  color: #155c36;
  font-size: 0.98rem;
  font-weight: 700;
  gap: 6px;
}
.about-what-viewall {
  color: #155c36;
  font-size: 0.85rem;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 2px;
  font-weight: 500;
}
.about-what-icons-row {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 2px;
}
.about-what-icon-col {
  background: #f5f8f6;
  border-radius: 12px;
  padding: 10px 8px 6px 8px;
  min-width: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #155c36;
  font-size: 0.8rem;
  font-weight: 600;
  box-shadow: 0 1px 4px 0 rgba(32,97,58,0.04);
}
.about-what-icon-col ion-icon {
  font-size: 1.3rem;
  margin-bottom: 4px;
}

.about-values-row {
  margin: 0 14px 18px 14px;
}
.about-values-title-row {
  display: flex;
  align-items: center;
  color: #155c36;
  font-size: 0.98rem;
  font-weight: 700;
  gap: 6px;
  margin-bottom: 8px;
}
.about-values-pills-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.about-value-pill {
  background: #f5f8f6;
  border-radius: 18px;
  padding: 6px 14px 6px 10px;
  display: flex;
  align-items: center;
  color: #155c36;
  font-size: 0.8rem;
  font-weight: 600;
  box-shadow: 0 1px 4px 0 rgba(32,97,58,0.04);
  gap: 6px;
}
.about-value-pill ion-icon {
  font-size: 1.1rem;
}

.about-join-cause-card {
  display: flex;
  align-items: center;
  background: #fff7e6;
  border-radius: 16px;
  margin: 0 14px 18px 14px;
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.06);
  padding: 12px 10px 12px 10px;
  gap: 12px;
}
.about-join-cause-img {
  width: 90px;
  height: 90px;
  object-fit: contain;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 4px 0 rgba(32,97,58,0.04);
}
.about-join-cause-content {
  flex: 1 1 0;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-width: 0;
}
.about-join-cause-title {
  font-size: 1rem;
  font-weight: 700;
  color: #155c36;
  margin-bottom: 2px;
}
.about-join-cause-desc {
  font-size: 0.78rem;
  color: #222;
  margin-bottom: 8px;
  line-height: 1.4;
}
.about-join-cause-btns {
  display: flex;
  gap: 8px;
}
.about-join-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  padding: 7px 14px;
  cursor: pointer;
  transition: background 0.2s;
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
.about-join-btn.volunteer ion-icon {
  color: #155c36;
}

@media (max-width: 600px) {
  .about-header-card {
    flex-direction: column;
    align-items: flex-start;
    min-height: 100px;
    padding: 14px 8px 14px 8px;
  }
  .about-header-logo {
    width: 44px;
    height: 44px;
    margin-right: 8px;
  }
  .about-header-title {
    font-size: 0.98rem;
  }
  .about-header-subtitle {
    font-size: 0.7rem;
  }
  .about-header-cover {
    width: 90px;
    min-width: 70px;
    height: 100%;
    border-top-right-radius: 14px;
    border-bottom-right-radius: 14px;
  }
  .about-section {
    padding: 12px 8px;
  }
  .about-section-icon {
    width: 30px;
    height: 30px;
    font-size: 1.1rem;
    margin-right: 8px;
  }
  .about-section-content h2 {
    font-size: 0.92rem;
  }
  .about-section-content p {
    font-size: 0.7rem;
  }
  .about-mission-vision-row {
    flex-direction: column;
    gap: 8px;
  }
  .about-mission-vision-card {
    padding: 10px 7px 10px 7px;
  }
  .about-mv-title {
    font-size: 0.85rem;
  }
  .about-mv-desc {
    font-size: 0.68rem;
  }
  .about-what-title {
    font-size: 0.9rem;
  }
  .about-what-viewall {
    font-size: 0.7rem;
  }
  .about-what-icon-col {
    min-width: 64px;
    font-size: 0.7rem;
    padding: 7px 4px 4px 4px;
  }
  .about-what-icon-col ion-icon {
    font-size: 1.1rem;
  }
  .about-values-title-row {
    font-size: 0.9rem;
  }
  .about-value-pill {
    font-size: 0.7rem;
    padding: 4px 10px 4px 7px;
  }
  .about-value-pill ion-icon {
    font-size: 0.95rem;
  }
  .about-join-cause-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 10px 6px 10px 6px;
    gap: 8px;
  }
  .about-join-cause-img {
    width: 70px;
    height: 70px;
    margin-bottom: 4px;
  }
  .about-join-cause-title {
    font-size: 0.9rem;
  }
  .about-join-cause-desc {
    font-size: 0.68rem;
  }
  .about-join-btn {
    font-size: 0.7rem;
    padding: 6px 10px;
  }
}
</style>

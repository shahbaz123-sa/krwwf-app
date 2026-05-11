<template>
  <ion-page>
    <ion-content :fullscreen="true" class="auth-content">
      <div class="cover-section">
        <div class="cover-overlay"></div>
        <div class="cover-center">
<!--          <img :src="crestLogo" class="crest-logo" alt="KRWWF Crest" />-->
          <div class="cover-title">KHANZADA</div>
          <div class="cover-sub">WELFARE & WORKING FORUM</div>
          <div class="cover-urdu">اتحاد، خدمت، ترقی</div>
        </div>
      </div>
      <div class="footer-curve">
<!--        <img class="footer-bg" :src="forestFooter" alt="Footer background" />-->
        <div class="footer-content">
          <div class="footer-title">Connect. Collaborate. Empower.</div>
          <div class="footer-desc">Let&apos;s build a stronger Khanzada community together.</div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script>
import { IonPage, IonContent } from '@ionic/vue';
export default {
  components: { IonPage, IonContent }
}
</script>

<style scoped>
.auth-content {
  --background: #f4f6f9;
  --padding-bottom: calc(24px + env(safe-area-inset-bottom));
  min-height: auto; /* Adjusted to allow scrolling */
  padding: 0;
  position: relative;
  overflow-y: auto; /* Ensure vertical scrolling */
}
.cover-section {
  position: relative;
  width: 100%;
  min-height: 240px;
  background-size: cover;
  background-position: center;
  border-bottom-left-radius: 32px;
  border-bottom-right-radius: 32px;
  overflow: visible; /* Adjusted to allow scrolling */
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.cover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg,rgba(30,68,52,0.7) 0%,rgba(30,68,52,0.5) 60%,rgba(30,68,52,0.0) 100%);
  z-index: 1;
}
.cover-center {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  width: 100%;
  padding-bottom: 18px;
}
.crest-logo {
  width: 90px;
  height: 90px;
  margin-bottom: 8px;
  object-fit: contain;
}
.cover-title {
  font-size: 28px;
  font-weight: 900;
  color: #FFD700;
  letter-spacing: 2px;
  margin-bottom: 2px;
}
.cover-sub {
  font-size: 13px;
  color: #fff;
  font-weight: 700;
  letter-spacing: 1px;
}
.cover-urdu {
  font-size: 18px;
  color: #fff;
  margin-top: 2px;
  font-family: "Noto Nastaliq Urdu", serif;
}
.footer-curve {
  position: relative;
  z-index: 20;
  width: 100%;
  pointer-events: none;
  height: 120px;
  margin-top: 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
}
.footer-bg {
  width: 100%;
  display: block;
  position: absolute;
  left: 0;
  bottom: 0;
  z-index: 1;
}
.footer-content {
  position: relative;
  z-index: 2;
  width: 100%;
  padding: 0 16px 36px;
  text-align: center;
  pointer-events: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.footer-title {
  font-size: 18px;
  font-weight: 800;
  color: #F9B233;
  margin-bottom: 2px;
  letter-spacing: 0.5px;
}
.footer-desc {
  font-size: 13px;
  color: #fff;
  font-weight: 500;
}
@media (max-width: 600px) {
  .cover-section {
    min-height: 180px;
  }
  .footer-curve {
    height: 90px;
  }
  .footer-content {
    padding-bottom: 24px;
  }
}
</style>

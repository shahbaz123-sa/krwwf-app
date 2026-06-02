<template>
  <ion-page class="contact-page">
    <!-- Header -->
    <div class="contact-header">
      <div class="contact-header-title">Contact Us</div>
    </div>
    <ion-content :fullscreen="true" class="contact-content">
      <!-- Hero Section -->
      <div class="contact-hero-row">
        <div class="contact-hero-text">
          <div class="contact-hero-title">We’re Here For You</div>
          <div class="contact-hero-desc">
            Reach out to any of our offices or contact us on WhatsApp.
          </div>
        </div>
        <div class="contact-hero-img">
          <img src="/src/assets/contact-hero.png" alt="Contact Illustration" />
        </div>
      </div>
      <!-- Quick Contact -->
      <div class="contact-section">
        <div class="contact-section-title">Quick Contact</div>
        <div class="quick-contact-row">
          <a v-for="item in quickContacts" :key="item.label" :href="item.href" class="quick-contact-card" target="_blank" rel="noopener">
            <div class="quick-contact-icon" :style="{ background: '#f3f7f3', color: item.color }">
              <ion-icon :icon="item.icon" />
            </div>
            <div class="quick-contact-labels">
              <div class="quick-contact-label">{{ item.label }}</div>
              <div class="quick-contact-sublabel">{{ item.sublabel }}</div>
            </div>
          </a>
        </div>
      </div>
      <!-- Our Offices -->
      <div class="contact-section">
        <div class="contact-section-title">Our Officials</div>
        <div class="office-list">
          <div v-for="office in offices" :key="office.name" class="office-card">
            <div class="office-card-title-row">
              <span class="office-card-icon"><ion-icon :icon="office.icon" /></span>
              <span class="office-card-title">{{ office.name }}</span>
            </div>
            <div class="office-card-info">
              <div class="office-card-address">
                <ion-icon :icon="locationOutline" />
                {{ office.address }}
              </div>
              <div class="office-card-contact-row">
                <div class="office-card-contact"><ion-icon :icon="callOutline" />{{ office.phone }}</div>
                <div class="office-card-contact"><ion-icon :icon="logoWhatsapp" />{{ office.whatsapp }}</div>
                <div class="office-card-contact"><ion-icon :icon="mailOutline" />{{ office.email }}</div>
              </div>
            </div>
            <div class="office-card-actions">
              <a :href="office.maps" target="_blank" class="office-card-btn primary">
                <ion-icon :icon="locationOutline" /> Open in Google Maps
              </a>
              <a :href="office.whatsappLink" target="_blank" class="office-card-btn outline">
                <ion-icon :icon="logoWhatsapp" /> WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- Send a Message -->
      <div class="contact-section">
        <div class="contact-section-title">Send a Message</div>
        <form class="contact-form" @submit.prevent="sendMessage">
          <div class="contact-form-field">
            <ion-icon :icon="personOutline" />
            <input disabled type="text" placeholder="Your Name" v-model="form.name" required />
          </div>
          <div class="contact-form-field">
            <ion-icon :icon="mailOutline" />
            <input disabled type="email" placeholder="Your Email" v-model="form.email" required />
          </div>
          <div class="contact-form-field">
            <ion-icon :icon="chatbubbleEllipsesOutline" />
            <textarea disabled placeholder="Your Message" v-model="form.message" required rows="3"></textarea>
          </div>
          <button disabled class="contact-form-btn" type="submit">
            <ion-icon :icon="sendOutline" />
            Send Message
          </button>
        </form>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  callOutline,
  mailOutline,
  locationOutline,
  personOutline,
  chatbubbleEllipsesOutline,
  sendOutline,
  logoWhatsapp,
  homeOutline,
  mapOutline
} from 'ionicons/icons';

const quickContacts = [
  {
    icon: callOutline,
    label: 'Call Us',
    sublabel: '+923333737671',
    href: 'tel:+923333737671',
    color: '#155c36',
  },
  {
    icon: logoWhatsapp,
    label: 'WhatsApp',
    sublabel: 'Chat on WhatsApp',
    href: 'https://wa.me/923333737671',
    color: '#155c36',
  },
  {
    icon: mailOutline,
    label: 'Email Us',
    sublabel: 'Send us an email',
    href: 'mailto:nomandt@hotmail.com',
    color: '#155c36',
  },
  {
    icon: mapOutline,
    label: 'Google Maps',
    sublabel: 'Find us on map',
    href: 'https://maps.app.goo.gl/xfFuiqKrSgB5Nf3G7',
    color: '#155c36',
  },
];

const offices = [
  {
    icon: homeOutline,
    name: 'Bilawal Khanzada – Karachi',
    address: 'House 862 Sector 33/E Korangi 21/2, 74900, Karachi, Pakistan',
    phone: '+923311335907',
    whatsapp: '+923311335907',
    email: 'bilawalkz23@gmail.com',
    maps: 'https://maps.app.goo.gl/DMe3ByRrV4z28VZ69',
    whatsappLink: 'https://wa.me/923311335907',
  },
  {
    icon: homeOutline,
    name: 'Furqan khanzada – Karachi',
    address: '473, 3 Shah Faisal Colony Number 3, Block 3, Shah Faisal Town, 75230, Karachi, Pakistan',
    phone: '+923222015764',
    whatsapp: '+923222015764',
    email: 'furqankz26@yahoo.com',
    maps: 'https://maps.app.goo.gl/BCbU3JSkXVmuM1z88',
    whatsappLink: 'https://wa.me/923222015764',
  },
  {
    icon: homeOutline,
    name: 'Imran Raheem Khanzada - Punjab',
    address: 'Jamber Kalan, District Kasur, Tehsil Pattoki, Punjab, 55250, Pakistan',
    phone: '+923334675014',
    whatsapp: '+923013662283',
    email: 'imranrahimkhanzada@gmail.com',
    maps: 'https://maps.app.goo.gl/gHsnZfTZx63NWeFq5',
    whatsappLink: 'https://wa.me/923013662283',
  },
];

const form = ref({ name: '', email: '', message: '' });
function sendMessage() {
  // Implement your message sending logic here
  alert('Message sent!');
  form.value = { name: '', email: '', message: '' };
}
</script>

<style scoped>
.contact-page {
  background: #f8fafb;
}
.contact-header {
  background: #155c36;
  color: #fff;
  display: flex;
  align-items: center;
  height: 35px;
  padding: 0 0 0 4px;
  position: sticky;
  top: 0;
  z-index: 10;
}
.contact-back-btn {
  --color: #fff;
  margin-right: 8px;
}
.contact-header-title {
  flex: 1;
  text-align: center;
  font-size: 1.05rem;
  font-weight: 600;
  letter-spacing: 0.01em;
}
.contact-content {
  padding: 0 0 80px 0;
}
.contact-hero-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: 24px 16px 12px 16px;
  gap: 12px;
}
.contact-hero-text {
  flex: 1 1 0;
}
.contact-hero-title {
  font-size: 1.08rem;
  font-weight: 700;
  color: #155c36;
  margin-bottom: 4px;
}
.contact-hero-desc {
  font-size: 0.82rem;
  color: #444;
}
.contact-hero-img {
  display: flex;
  align-items: center;
}
.contact-hero-img img {
  width: 125px;
  border-radius: 8px;
  object-fit: cover;
}
.contact-section {
  margin: 0 16px 22px 16px;
}
.contact-section-title {
  font-size: 0.98rem;
  font-weight: 600;
  color: #155c36;
  margin-bottom: 7px;
}
.quick-contact-row {
  display: flex;
  flex-direction: row !important;
  gap: 5px;
  margin-bottom: 10px;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
}
.quick-contact-card {
  flex: 1 1 0;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px 0 rgba(32,97,58,0.06);
  padding: 10px 1px 9px 1px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  text-decoration: none;
  color: #155c36;
  transition: box-shadow 0.2s;
  min-height: 60px;
}
.quick-contact-card:hover {
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.12);
}
.quick-contact-icon {
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 25px;
  height: 25px;
  font-size: 1rem;
  margin-bottom: 2px;
}
.quick-contact-label {
  font-size: 0.7rem;
  font-weight: 600;
}
.quick-contact-sublabel {
  font-size: 0.5rem;
  color: #444;
}
.office-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.office-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px 0 rgba(32,97,58,0.06);
  padding: 10px 6px 8px 6px;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.office-card-title-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}
.office-card-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #f3f7f3;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: #155c36;
  margin-bottom: 0;
}
.office-card-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #155c36;
  margin-bottom: 2px;
}
.office-card-address {
  font-size: 0.7rem;
  color: #444;
  display: flex;
  align-items: center;
  gap: 3px;
  margin-bottom: 4px;
}
.office-card-contact-row {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-bottom: 6px;
}
.office-card-contact {
  font-size: 0.7rem;
  color: #155c36;
  display: flex;
  align-items: center;
  gap: 4px;
}
.office-card-actions {
  display: flex;
  flex-direction: row;
  gap: 10px;
  margin-top: 4px;
}
.office-card-btn {
  flex: 1 1 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  border-radius: 6px;
  font-size: 0.8rem;
  padding: 7px 0;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, border 0.2s;
}
.office-card-btn.primary {
  background: #155c36;
  color: #fff;
  border: none;
}
.office-card-btn.outline {
  background: #fff;
  color: #155c36;
  border: 2px solid #155c36;
}
.office-card-btn.primary ion-icon {
  color: #fff;
}
.office-card-btn.outline ion-icon {
  color: #155c36;
}
.contact-form {
  background: #f3f5ef;
  border-radius: 10px;
  padding: 10px 6px 8px 6px;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.contact-form-field {
  display: flex;
  align-items: center;
  background: #f7f8f6;
  border-radius: 6px;
  padding: 6px 7px;
  gap: 5px;
}
.contact-form-field ion-icon {
  color: #bdbdbd;
  font-size: 0.9rem;
}
.contact-form-field input,
.contact-form-field textarea {
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.9rem;
  flex: 1 1 0;
  color: #222;
  padding: 0;
  resize: none;
}
.contact-form-btn {
  margin-top: 8px;
  margin-bottom: 35px;
  background: #155c36;
  color: #fff;
  border: none;
  border-radius: 8px;
  gap: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
  width: 100%;
  font-size: 0.8rem;
  padding: 7px 0;
}
.contact-form-btn ion-icon {
  font-size: 1rem;
}
/* Remove media query that stacks quick-contact-row vertically on small screens. */
@media (max-width: 600px) {
  .contact-header-title {
    font-size: 0.98rem;
  }
  .contact-hero-title {
    font-size: 0.98rem;
  }
  .contact-hero-desc {
    font-size: 0.7rem;
  }
  .contact-section {
    margin: 0 6px 18px 6px;
  }
  .contact-section-title {
    font-size: 0.9rem;
  }
  .quick-contact-card {
    border-radius: 7px;
    padding: 4px 0 3px 0;
  }
  .quick-contact-label {
    font-size: 0.6rem;
  }
  .quick-contact-sublabel {
    font-size: 0.4rem;
  }
  .office-card-title {
    font-size: 0.8rem;
  }
  .office-card-address {
    font-size: 0.6rem;
  }
  .office-card-contact {
    font-size: 0.6rem;
  }
  .office-card-btn {
    font-size: 0.7rem;
    padding: 5px 0;
  }
}
</style>

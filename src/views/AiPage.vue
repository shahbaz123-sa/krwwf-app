<template>
  <ion-page>
    <ion-content class="ai-content">
    <!-- Header with logo and title -->
    <div class="ai-header">
      <img src="/favicon.png" class="ai-logo" alt="KRWWF Logo" />
      <div class="ai-header-texts">
        <div class="ai-title">KRWWF AI Assistant</div>
        <div class="ai-subtitle">Ask anything about KRWWF</div>
      </div>
    </div>

    <!-- Welcome Card -->
    <div class="ai-welcome-card">
      <div class="ai-welcome-left">
        <ion-icon :icon="informationCircleOutline" class="ai-welcome-icon" />
        <div class="ai-welcome-title">How can I help you today?</div>
        <div class="ai-welcome-desc">
          I'm here to answer your questions about KRWWF and our initiatives.
        </div>
      </div>
      <img src="/src/assets/ai-welcome-art.png" class="ai-welcome-art" alt="Welcome Art" />
    </div>

    <!-- Suggested Questions -->
    <div class="ai-suggestions">
      <div class="ai-suggestion-chip">
        <ion-icon :icon="helpCircleOutline" />
        What is KRWWF?
      </div>
      <div class="ai-suggestion-chip">
        <ion-icon :icon="cardOutline" />
        How can I donate?
      </div>
      <div class="ai-suggestion-chip">
        <ion-icon :icon="schoolOutline" />
        Scholarship details
      </div>
    </div>

    <!-- Chat Area -->
    <div class="ai-chat-area" ref="chatAreaRef">
      <div v-for="msg in messages" :key="msg.id" :class="['ai-message', msg.sender === 'user' ? 'ai-message-user' : 'ai-message-bot']">
        <template v-if="msg.sender === 'bot'">
          <img src="/favicon.png" class="ai-bot-avatar" alt="Bot Logo" />
        </template>
        <div :class="['ai-message-bubble', msg.sender === 'user' ? 'ai-message-bubble-user' : 'ai-message-bubble-bot']">
          {{ msg.message }}
          <div class="ai-message-meta">
            <ion-icon :icon="msg.sender === 'bot' ? informationCircleOutline : checkmarkDoneOutline" class="ai-meta-icon" />
            <span>{{ new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Input Bar -->
    <div class="ai-input-bar">
      <ion-input v-model="inputMessage" placeholder="Type your question..." class="ai-input" @keyup.enter="handleSend" />
      <ion-button color="success" class="ai-send-btn" :disabled="!inputMessage.trim() || loading" @click="handleSend">
        <ion-icon :icon="paperPlane" />
      </ion-button>
    </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { IonPage, IonContent, IonInput, IonButton, IonIcon } from '@ionic/vue';
import { informationCircleOutline, helpCircleOutline, cardOutline, schoolOutline, checkmarkDoneOutline, paperPlane } from 'ionicons/icons';
import { getConversations, startConversation, getMessages, sendMessage, ChatbotMessage } from '@/services/chatbot';

const conversationId = ref<number|null>(null);
const messages = ref<ChatbotMessage[]>([]);
const inputMessage = ref('');
const loading = ref(false);
const chatAreaRef = ref<HTMLElement|null>(null);

async function loadOrCreateConversation() {
  loading.value = true;
  try {
    const { data: convs } = await getConversations();
    let conv = convs[0];
    if (!conv) {
      const { data: newConv } = await startConversation();
      conv = newConv;
    }
    conversationId.value = conv.id;
    await loadMessages();
  } finally {
    loading.value = false;
  }
}

async function loadMessages() {
  if (!conversationId.value) return;
  const { data: msgs } = await getMessages(conversationId.value);
  messages.value = msgs;
  await nextTick();
  scrollToBottom();
}

async function handleSend() {
  if (!inputMessage.value.trim() || !conversationId.value) return;
  const text = inputMessage.value;
  inputMessage.value = '';
  const { data: msg } = await sendMessage(conversationId.value, text);
  messages.value.push(msg);
  await nextTick();
  scrollToBottom();
  // Optionally: poll for bot reply or use websocket for real-time
}

function scrollToBottom() {
  const el = chatAreaRef.value;
  if (el) el.scrollTop = el.scrollHeight;
}

onMounted(() => {
  loadOrCreateConversation();
});
</script>

<style scoped>

.ai-header {
  display: flex;
  align-items: center;
  padding: 10px 16px 8px 16px;
  background: transparent;
}
.ai-logo {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  margin-right: 12px;
  background: #e6f2e6;
  object-fit: cover;
}
.ai-header-texts {
  display: flex;
  flex-direction: column;
}
.ai-title {
  font-size: 18px;
  font-weight: 700;
  color: #185c3c;
  line-height: 1.1;
}
.ai-subtitle {
  font-size: 13px;
  color: #4b6e5a;
  margin-top: 2px;
}
.ai-welcome-card {
  display: block;
  position: relative;
  background: rgba(230, 242, 230, 0.46);
  border-radius: 16px;
  border: 1px solid rgba(191, 200, 191, 0.28);
  margin: 0 16px 12px 16px;
  padding: 16px 16px 16px 16px; /* right padding for image space */
  min-height: 90px;
  overflow: hidden;
}
.ai-welcome-left {
  position: relative;

  flex: 1 1 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.ai-welcome-icon {
  font-size: 22px;
  width: 20px;
  color: #185c3c;
  margin-bottom: 4px;
}
.ai-welcome-title {
  font-size: 14px;
  font-weight: 600;
  color: #185c3c;
}
.ai-welcome-desc {
  font-size: 11px;
  color: #4b6e5a;
  margin: 2px 80px 0 0;
}
.ai-welcome-art {
  position: absolute;
  right: 0;
  bottom: 0;
  width: 130px;
  object-fit: contain;
  z-index: 1;
  margin: 0 -10px -10px 0;
  pointer-events: none;
}
.ai-suggestions {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  gap: 8px;
  margin: 0 8px 0 8px;
  padding: 0 8px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}
.ai-suggestion-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #fff;
  color: #185c3c;
  border-radius: 20px;
  font-size: 8px;
  font-weight: 500;
  padding: 6px 12px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  white-space: nowrap;
  border: 1px solid #e6f2e6;
  min-width: 120px;
  flex-shrink: 0;
  cursor: pointer;
}
.ai-suggestion-chip ion-icon {
  font-size: 13px;
  color: #185c3c;
}
@media (max-width: 633px) {
  .ai-suggestion-chip {
    font-size: 11px;
    padding: 4px 8px;
    min-width: 90px;
  }
  .ai-suggestions {
    gap: 4px;
    padding: 0 8px 0 8px;
  }
}
@media (max-width: 400px) {
  .ai-suggestion-chip {
    font-size: 8.5px;
    padding: 3px 6px;
    min-width: 70px;
  }
  .ai-suggestions {
    gap: 2px;
    padding: 0 2px;
  }
}
.ai-chat-area {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0 8px 100px 8px;
  margin-bottom: 18px;
}
.ai-chat-date {
  align-self: center;
  background: #e6f2e6;
  color: #185c3c;
  font-size: 12px;
  font-weight: 500;
  border-radius: 10px;
  padding: 2px 12px;
  margin: 8px 0 2px 0;

}
.ai-message {
  display: flex;
  align-items: flex-end;
  margin-bottom: 2px;
}
.ai-message-user {
  flex-direction: row-reverse;
}
.ai-message-bubble {
  border-radius: 14px;
  padding: 10px 16px 8px 16px;
  font-size: 11px;
  max-width: 80vw;
  box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  position: relative;
  margin-bottom: 2px;
}
.ai-message-bubble-user {
  background: #185c3c;
  color: #fff;
  align-self: flex-end;
}
.ai-message-bubble-bot {
  background: #f6f8f6;
  color: #185c3c;
  align-self: flex-start;
  border: 1px solid #e6f2e6;
}
.ai-bot-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  margin-right: 8px;
  background: #e6f2e6;
  object-fit: cover;
}
.ai-message-meta {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  color: #96aaa1;
  margin-top: 6px;
}
.ai-meta-icon {
  font-size: 13px;
  color: #96aaa1;
}
.ai-input-bar {
  display: flex;
  align-items: center;
  padding: 10px 12px 10px 12px;
  background: #f6f8f6;
  border-top: 1px solid #e6f2e6;
  position: fixed;
  left: 0;
  right: 0;
  bottom: 48px; /* height of bottom navbar */
  z-index: 100;
  max-width: 600px;
  margin: 0 auto;
  width: 100vw;
  box-sizing: border-box;
  border-radius: 50px;
  box-shadow: 0 2px 12px 0 rgba(0,0,0,0.04);
}
@media (max-width: 600px) {
  .ai-input-bar {
    max-width: 100vw;
    left: 0;
    right: 0;
    border-radius: 50px;
  }
}
.ai-input {
  flex: 1 1 auto;
  font-size: 15px;
  margin-right: 8px;
  --padding-start: 16px;
  --padding-end: 16px;
  --background: #fff;
  --border-radius: 20px;
  border-radius: 20px;
  background: #fff;
  border: 1px solid #e6f2e6;
  min-height: 40px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.ai-send-btn {
  min-width: 44px;
  min-height: 44px;
  --border-radius: 50%;
  --padding-start: 0;
  --padding-end: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  background: linear-gradient(135deg, #1fa463 60%, #185c3c 100%);
  color: #fff;
  box-shadow: 0 2px 8px 0 rgba(31,164,99,0.10);
  border-radius: 50%;
  margin-left: 6px;
}
.ai-send-btn:disabled {
  opacity: 0.6;
  background: #185c3c;
  color: #fff;
}
.ai-send-btn ion-icon {
  font-size: 22px;
  color: #fff;
}
</style>

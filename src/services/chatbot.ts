import api from './api';
import { getToken } from './auth';

export interface ChatbotConversation {
  id: number;
  user_id: number | null;
  title: string | null;
  status: 'active' | 'closed';
  created_at: string;
  updated_at: string;
}

export interface ChatbotMessage {
  id: number;
  conversation_id: number;
  user_id: number | null;
  sender: 'user' | 'bot' | 'admin';
  message: string;
  source_type?: string | null;
  source_ids?: any;
  is_helpful?: boolean | null;
  created_at: string;
  updated_at: string;
}

function getHeaders() {
  const token = getToken();
  return {
    Authorization: `Bearer ${token}`,
  };
}

export async function getConversations() {
  return api.get<ChatbotConversation[]>('/chatbot/conversations', { headers: getHeaders() });
}

export async function startConversation(title?: string) {
  return api.post<ChatbotConversation>('/chatbot/conversations', { title }, { headers: getHeaders() });
}

export async function getMessages(conversationId: number) {
  return api.get<ChatbotMessage[]>(`/chatbot/conversations/${conversationId}/messages`, { headers: getHeaders() });
}

export async function sendMessage(conversationId: number, message: string) {
  return api.post<ChatbotMessage>(`/chatbot/conversations/${conversationId}/messages`, { message }, { headers: getHeaders() });
}



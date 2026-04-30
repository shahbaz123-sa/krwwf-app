import api from "@/services/api";

const TOKEN_KEY = "token";

export type AuthUser = {
  id: number;
  name: string;
  email: string;
};

type LoginPayload = {
  email: string;
  password: string;
};

type RegisterPayload = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
};

type AuthResponse = {
  token: string;
  user: AuthUser;
};

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function isAuthenticated(): boolean {
  return Boolean(getToken());
}

export async function register(payload: RegisterPayload): Promise<AuthUser> {
  const response = await api.post<AuthResponse>("/auth/register", payload);
  persistToken(response.data.token);
  return response.data.user;
}

export async function login(payload: LoginPayload): Promise<AuthUser> {
  const response = await api.post<AuthResponse>("/auth/login", payload);
  persistToken(response.data.token);
  return response.data.user;
}

export async function fetchCurrentUser(): Promise<AuthUser> {
  const response = await api.get<AuthUser>("/user");
  return response.data;
}

export async function logout(): Promise<void> {
  try {
    await api.post("/auth/logout");
  } finally {
    localStorage.removeItem(TOKEN_KEY);
  }
}

function persistToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}


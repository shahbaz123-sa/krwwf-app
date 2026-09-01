import api from "@/services/api";

const TOKEN_KEY = "token";

export type AuthUser = {
  id: number;
  name: string;
  email: string | null;
  mobile_number: string | null;
  profile_picture: string | null;
  profile_picture_url: string | null;
  member_id?: string | null;
  location?: string | null;
  profession?: string | null;
  company?: string | null;
  experience?: string | null;
  skills?: string | null;
  role_in_community?: string | null;
  blood_group?: string | null;
  interests?: string | null;
  short_bio?: string | null;
  linkedin_profile?: string | null;
  date_of_birth?: string | null;
};

type LoginPayload = {
  login_with: "mobile" | "email";
  country_code?: string;
  mobile_number?: string;
  email?: string;
  password: string;
};

type RegisterPayload = {
  name: string;
  email?: string;
  country_code: string;
  mobile_number: string;
  password: string;
  password_confirmation: string;
  profile_picture?: File;
  date_of_birth?: string;
};

type AuthResponse = {
  token: string;
  user: AuthUser;
};

type UpdateProfilePayload = {
  name?: string;
  mobile_number?: string;
  email?: string | null;
  location?: string | null;
  password?: string;
  password_confirmation?: string;
  // additional profile fields
  profession?: string | null;
  company?: string | null;
  experience?: string | null;
  skills?: string | null;
  role_in_community?: string | null;
  blood_group?: string | null;
  interests?: string | null;
  short_bio?: string | null;
  linkedin_profile?: string | null;
  date_of_birth?: string | null;
};

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function isAuthenticated(): boolean {
  return Boolean(getToken());
}

export function clearSession(): void {
  localStorage.removeItem(TOKEN_KEY);
}

export async function register(payload: RegisterPayload): Promise<AuthUser> {
  const formData = new FormData();
  formData.append("name", payload.name);
  formData.append("country_code", payload.country_code);
  formData.append("mobile_number", payload.mobile_number);
  formData.append("password", payload.password);
  formData.append("password_confirmation", payload.password_confirmation);

  if (payload.email) {
    formData.append("email", payload.email);
  }

  if (payload.profile_picture) {
    formData.append("profile_picture", payload.profile_picture);
  }

  if (payload.date_of_birth) {
    formData.append("date_of_birth", payload.date_of_birth);
  }

  const response = await api.post<AuthResponse>("/auth/register", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
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

export async function updateProfile(payload: UpdateProfilePayload): Promise<AuthUser> {
  const response = await api.put<AuthUser>("/user", payload);
  return response.data;
}

export async function uploadProfilePicture(file: File): Promise<AuthUser> {
  const formData = new FormData();
  formData.append("profile_picture", file);

  const response = await api.post<AuthUser>("/user/picture", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });

  return response.data;
}

export async function logout(): Promise<void> {
  const token = getToken();
  clearSession();

  if (!token) {
    return;
  }

  try {
    await api.post("/auth/logout");
  } catch {
    // Token may already be invalid/expired; local session is already cleared.
  }
}

function persistToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}


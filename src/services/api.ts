import axios, { InternalAxiosRequestConfig } from "axios";

const envBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000/api";

function resolveBaseUrl(): string {
  const isNativeCapacitor =
    typeof window !== "undefined" && window.location.protocol === "capacitor:";
  const isAndroid =
    typeof navigator !== "undefined" && /Android/i.test(navigator.userAgent);

  if (!isNativeCapacitor || !isAndroid) {
    return envBaseUrl;
  }

  // Android emulator cannot reach host machine via localhost/127.0.0.1.
  return envBaseUrl
    .replace("http://127.0.0.1", "http://10.0.2.2")
    .replace("http://localhost", "http://10.0.2.2");
}

const baseURL = resolveBaseUrl();

const api = axios.create({
  baseURL,
  headers: {
    Accept: "application/json",
  },
});

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = localStorage.getItem("token");

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default api;


import axios, { InternalAxiosRequestConfig } from "axios";

// Prefer explicit env override, but default to XAMPP hosted Laravel backend.
// Web dev (XAMPP): http://localhost/krwwf-app/backend/public/api
// Android *emulator* host access: use 10.0.2.2
// Android *real device*: you must use your PC LAN IP, e.g. http://192.168.1.10:8000/api
const envBaseUrl =
  import.meta.env.VITE_API_BASE_URL ||
  "http://localhost/krwwf-app/backend/public/api";

const envAndroidBaseUrl = import.meta.env.VITE_API_BASE_URL_ANDROID as string | undefined;

function resolveBaseUrl(): string {
  const isNativeCapacitor =
    typeof window !== "undefined" && window.location.protocol === "capacitor:";
  const isAndroid =
    typeof navigator !== "undefined" && /Android/i.test(navigator.userAgent);

  if (!isNativeCapacitor || !isAndroid) return envBaseUrl;

  // If an Android-specific base URL is provided, always use it.
  // This is required for real devices where "localhost" points to the phone itself.
  if (envAndroidBaseUrl && envAndroidBaseUrl.trim().length > 0) {
    return envAndroidBaseUrl.trim();
  }

  // Fallback heuristic:
  // - Emulators can reach the host via 10.0.2.2
  // - Real devices need a LAN IP; in that case the env var above MUST be set.
  // Keep the old behavior for convenience in emulator.
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

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && localStorage.getItem("token")) {
      localStorage.removeItem("token");
    }

    return Promise.reject(error);
  },
);

export default api;


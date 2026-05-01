import { ref, watch } from "vue";

const THEME_KEY = "app-theme";

const isDark = ref<boolean>(localStorage.getItem(THEME_KEY) === "dark");

function applyTheme(dark: boolean): void {
  if (dark) {
    document.documentElement.classList.add("ion-palette-dark");
    document.body.classList.add("dark");
  } else {
    document.documentElement.classList.remove("ion-palette-dark");
    document.body.classList.remove("dark");
  }
}

// Apply on load immediately.
applyTheme(isDark.value);

watch(isDark, (val) => {
  applyTheme(val);
  localStorage.setItem(THEME_KEY, val ? "dark" : "light");
});

export function useTheme() {
  function toggleTheme(): void {
    isDark.value = !isDark.value;
  }

  function setDark(val: boolean): void {
    isDark.value = val;
  }

  return { isDark, toggleTheme, setDark };
}


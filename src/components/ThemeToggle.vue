<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, watch, ref } from 'vue';
import { useTheme } from '@/composables/useTheme';

const { isDark, toggleTheme } = useTheme();
const icon = computed(() => (isDark.value ? '🌙' : '☀️'));

// Fallback native button reference (if we need to create one programmatically)
const fallbackButton = ref<HTMLButtonElement | null>(null);

onMounted(() => {
  // Diagnostic: helps confirm the component rendered in the running app
  // eslint-disable-next-line no-console
  console.debug('[ThemeToggle] mounted');
  try {
    document.body.setAttribute('data-theme-toggle-mounted', 'true');
  } catch (e) {}

  // If the teleported element is not present, create a native fallback button immediately
  if (!document.getElementById('global-theme-toggle')) {
    // eslint-disable-next-line no-console
    console.debug('[ThemeToggle] teleport target missing — creating fallback button');
    const btn = document.createElement('button');
    btn.id = 'global-theme-toggle';
    btn.type = 'button';
    btn.setAttribute('aria-label', 'Toggle appearance');
    btn.setAttribute('aria-pressed', String(isDark.value));
    btn.textContent = icon.value;
    // inline styles to guarantee visibility
    btn.setAttribute(
      'style',
      'position: fixed !important; top: calc(8px + env(safe-area-inset-top, 0px)) !important; right: calc(8px + env(safe-area-inset-right, 0px)) !important; width: 44px !important; height: 44px !important; border-radius: 999px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; background: #fff !important; border: 2px solid rgba(0,0,0,0.12) !important; box-shadow: 0 12px 36px rgba(0,0,0,0.14) !important; z-index: 999999999 !important; padding: 0 !important; font-size: 16px !important; transform: translateZ(0) !important;'
    );
    btn.onclick = () => toggleTheme();
    document.body.appendChild(btn);
    fallbackButton.value = btn;

    // Keep fallback button in sync with theme changes
    const stop = watch(isDark, (val) => {
      if (!fallbackButton.value) return;
      fallbackButton.value.textContent = val ? '🌙' : '☀️';
      fallbackButton.value.setAttribute('aria-pressed', String(val));
      if (val) fallbackButton.value.style.background = 'var(--ion-color-primary)';
      else fallbackButton.value.style.background = '#fff';
    });

    // store stop so we can cleanup later by attaching to the element
    (fallbackButton.value as any).__stopWatcher = stop;
  }
});

onBeforeUnmount(() => {
  if (fallbackButton.value) {
    const stop = (fallbackButton.value as any).__stopWatcher;
    try {
      stop && stop();
    } catch (e) {}
    try {
      fallbackButton.value.remove();
    } catch (e) {}
    fallbackButton.value = null;
  }
});
</script>

<template>
  <teleport to="body">
    <button
      class="theme-toggle-global"
      :class="{ 'theme-toggle-global--active': isDark }"
      type="button"
      id="global-theme-toggle"
      @click="toggleTheme"
      :aria-pressed="isDark"
      aria-label="Toggle appearance"
      tabindex="0"
      style="position: fixed !important; top: calc(8px + env(safe-area-inset-top, 0px)) !important; right: calc(8px + env(safe-area-inset-right, 0px)) !important; width: 44px !important; height: 44px !important; border-radius: 999px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; background: #fff !important; border: 2px solid rgba(0,0,0,0.12) !important; box-shadow: 0 12px 36px rgba(0,0,0,0.14) !important; z-index: 999999999 !important; padding: 0 !important; font-size: 16px !important; transform: translateZ(0) !important; outline: none !important;"
    >
      <span class="theme-toggle-global__icon">{{ icon }}</span>
    </button>
  </teleport>
</template>

<style scoped>
.theme-toggle-global {
  position: fixed !important;
  top: calc(8px + env(safe-area-inset-top, 0px)) !important;
  right: calc(8px + env(safe-area-inset-right, 0px)) !important;
  width: 40px !important;
  height: 40px !important;
  border-radius: 999px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  background: var(--app-surface-color) !important;
  border: 1px solid rgba(0,0,0,0.06) !important;
  box-shadow: 0 8px 24px rgba(16,24,40,0.12) !important;
  z-index: 2147483647 !important;
  cursor: pointer !important;
  padding: 0 !important;
  font-size: 16px !important;
  transform: translateZ(0) !important;
  pointer-events: auto !important;
}
.theme-toggle-global--active {
  background: var(--ion-color-primary);
  color: #fff;
  border-color: transparent;
}
.theme-toggle-global__icon {
  line-height: 1;
  pointer-events: none;
}

@media (max-width: 420px) {
  .theme-toggle-global { width: 34px; height: 34px; font-size: 14px; }
}
</style>




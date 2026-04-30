import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.shahbaz.erpapp',
  appName: 'erpApp',
  webDir: 'dist',
  server: {
    cleartext: true,
  },
};

export default config;

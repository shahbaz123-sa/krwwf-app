import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.krwwf.app',
  appName: 'KRWWF',
  webDir: 'dist',
  server: {
    cleartext: true,
  },
};

export default config;

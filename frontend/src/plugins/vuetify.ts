import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import '@mdi/font/css/materialdesignicons.css';

export default createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#DC2626', // Rouge vif
          secondary: '#1F2937', // Noir/gris foncé
          accent: '#EF4444', // Rouge clair
          error: '#DC2626',
          warning: '#F59E0B',
          info: '#3B82F6',
          success: '#10B981',
          background: '#FFFFFF', // Blanc pur
          surface: '#FFFFFF',
          'surface-variant': '#F9FAFB',
          'on-primary': '#FFFFFF',
          'on-secondary': '#FFFFFF',
          'on-surface': '#1F2937',
          'on-background': '#1F2937',
        },
      },
      dark: {
        colors: {
          primary: '#EF4444',
          secondary: '#000000',
          accent: '#DC2626',
          error: '#EF4444',
          warning: '#F59E0B',
          info: '#60A5FA',
          success: '#34D399',
          background: '#0F172A',
          surface: '#1E293B',
          'surface-variant': '#334155',
        },
      },
    },
  },
  defaults: {
    VCard: {
      elevation: 2,
      rounded: 'lg',
    },
    VBtn: {
      rounded: 'lg',
      style: 'text-transform: none; font-weight: 500;',
    },
    VTextField: {
      variant: 'outlined',
      density: 'comfortable',
      rounded: 'lg',
    },
    VSelect: {
      variant: 'outlined',
      density: 'comfortable',
      rounded: 'lg',
    },
  },
});

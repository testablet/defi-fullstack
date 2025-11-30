import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import { useAuthStore } from './stores/authStore';
import fr from './locales/fr.json';
import en from './locales/en.json';
import './style.css';

const pinia = createPinia();

// Get saved locale from localStorage (before Pinia is initialized)
const savedLocale = localStorage.getItem('locale') || 'fr';

// Create i18n instance
const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'fr',
  messages: {
    fr,
    en,
  },
});

const app = createApp(App);

app.use(pinia);
app.use(router);
app.use(vuetify);
app.use(i18n);

// Initialize auth store and auto-login
const authStore = useAuthStore();
authStore.init().then(() => {
  app.mount('#app');
});




import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useLocaleStore = defineStore('locale', () => {
  const currentLocale = ref<string>(localStorage.getItem('locale') || 'fr');

  const setLocale = (locale: string) => {
    currentLocale.value = locale;
    localStorage.setItem('locale', locale);
  };

  return {
    currentLocale,
    setLocale,
  };
});


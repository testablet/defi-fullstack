import { defineStore } from 'pinia';
import { ref } from 'vue';
import apiClient from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('jwt_token'));
  const loading = ref(false);
  const error = ref<string | null>(null);

  const login = async (username: string = 'admin', password: string = 'password'): Promise<boolean> => {
    loading.value = true;
    error.value = null;

    try {
      const response = await apiClient.post('/auth/login', { username, password });
      if (response.data.token) {
        token.value = response.data.token;
        localStorage.setItem('jwt_token', response.data.token);
        return true;
      }
      return false;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to login';
      return false;
    } finally {
      loading.value = false;
    }
  };

  const logout = () => {
    token.value = null;
    localStorage.removeItem('jwt_token');
  };

  const isAuthenticated = () => {
    return token.value !== null;
  };

  // Auto-login on init
  const init = async () => {
    if (!token.value) {
      await login();
    }
  };

  return {
    token,
    loading,
    error,
    login,
    logout,
    isAuthenticated,
    init,
  };
});


import { defineStore } from 'pinia';
import { ref } from 'vue';
import { routeApi, type RouteRequest, type RouteResponse } from '@/services/api';

export const useRouteStore = defineStore('route', () => {
  const routes = ref<RouteResponse[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const createRoute = async (data: RouteRequest): Promise<RouteResponse | null> => {
    loading.value = true;
    error.value = null;

    try {
      const route = await routeApi.createRoute(data);
      routes.value.unshift(route);
      return route;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create route';
      return null;
    } finally {
      loading.value = false;
    }
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    routes,
    loading,
    error,
    createRoute,
    clearError,
  };
});


import axios, { AxiosInstance } from 'axios';

const apiClient: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/projets/train-routing-system/api/v1',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add JWT token to requests
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle 401 errors - auto retry with login
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401 && !error.config._retry) {
      error.config._retry = true;
      // Try to get a new token
      try {
        const { useAuthStore } = await import('@/stores/authStore');
        const authStore = useAuthStore();
        await authStore.login();
        // Retry the original request
        if (authStore.token) {
          error.config.headers.Authorization = `Bearer ${authStore.token}`;
          return apiClient(error.config);
        }
      } catch (e) {
        // Login failed, redirect or show error
        console.error('Auto-login failed:', e);
      }
    }
    return Promise.reject(error);
  }
);

export interface RouteRequest {
  fromStationId: string;
  toStationId: string;
  analyticCode: string;
}

export interface RouteResponse {
  id: string;
  fromStationId: string;
  toStationId: string;
  analyticCode: string;
  distanceKm: number;
  path: string[];
  createdAt: string;
}

export interface AnalyticDistance {
  analyticCode: string;
  totalDistanceKm: number;
  periodStart?: string;
  periodEnd?: string;
  group?: string;
}

export interface AnalyticDistanceList {
  from?: string;
  to?: string;
  groupBy: string;
  items: AnalyticDistance[];
}

export const routeApi = {
  createRoute: async (data: RouteRequest): Promise<RouteResponse> => {
    const response = await apiClient.post<RouteResponse>('/routes', data);
    return response.data;
  },
};

export const statsApi = {
  getDistances: async (
    from?: string,
    to?: string,
    groupBy: string = 'none'
  ): Promise<AnalyticDistanceList> => {
    const params = new URLSearchParams();
    if (from) params.append('from', from);
    if (to) params.append('to', to);
    params.append('groupBy', groupBy);

    const response = await apiClient.get<AnalyticDistanceList>(
      `/stats/distances?${params.toString()}`
    );
    return response.data;
  },
};

export default apiClient;




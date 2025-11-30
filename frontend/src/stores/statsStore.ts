import { defineStore } from 'pinia';
import { ref } from 'vue';
import { statsApi, type AnalyticDistanceList } from '@/services/api';

export const useStatsStore = defineStore('stats', () => {
  const stats = ref<AnalyticDistanceList | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchStats = async (
    from?: string,
    to?: string,
    groupBy: string = 'none'
  ): Promise<void> => {
    loading.value = true;
    error.value = null;

    try {
      stats.value = await statsApi.getDistances(from, to, groupBy);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch statistics';
    } finally {
      loading.value = false;
    }
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    stats,
    loading,
    error,
    fetchStats,
    clearError,
  };
});




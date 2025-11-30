<template>
  <div class="stats-view">
    <v-container class="py-8">
      <v-row>
        <!-- Header -->
        <v-col cols="12">
          <div class="text-center mb-8 fade-in-up">
            <h1 class="hero-title">
              {{ $t('stats.title') }}
            </h1>
            <p class="text-h6 text-grey-darken-2 hero-subtitle">
              {{ $t('stats.subtitle') }}
            </p>
          </div>
        </v-col>

        <!-- Filters Card -->
        <v-col cols="12" lg="4">
          <v-card
            class="filters-card fade-in-up"
            elevation="8"
            rounded="xl"
          >
            <v-card-title class="pa-6 pb-4">
              <div class="d-flex align-center">
                <v-icon icon="mdi-filter" size="32" color="primary" class="mr-3"></v-icon>
                <span class="text-h5 font-weight-bold">{{ $t('stats.filters') }}</span>
              </div>
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
              <v-text-field
                v-model="filters.from"
                type="date"
                :label="$t('stats.fromDate')"
                prepend-inner-icon="mdi-calendar-start"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                class="mb-4"
              ></v-text-field>

              <v-text-field
                v-model="filters.to"
                type="date"
                :label="$t('stats.toDate')"
                prepend-inner-icon="mdi-calendar-end"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                class="mb-4"
              ></v-text-field>

              <v-select
                v-model="filters.groupBy"
                :items="groupByOptions"
                :label="$t('stats.groupBy')"
                prepend-inner-icon="mdi-group"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                class="mb-6"
              ></v-select>

              <v-btn
                color="primary"
                size="x-large"
                :loading="statsStore.loading"
                @click="loadStats"
                block
                rounded="xl"
                prepend-icon="mdi-refresh"
              >
                <span class="text-h6 font-weight-bold">
                  {{ statsStore.loading ? $t('stats.loading') : $t('stats.loadStatistics') }}
                </span>
              </v-btn>

              <v-expand-transition>
                <v-alert
                  v-if="statsStore.error"
                  type="error"
                  variant="tonal"
                  rounded="lg"
                  class="mt-4"
                  dismissible
                  @click:close="statsStore.clearError"
                >
                  <v-alert-title>{{ $t('stats.error') }}</v-alert-title>
                  {{ statsStore.error }}
                </v-alert>
              </v-expand-transition>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Charts and Table -->
        <v-col cols="12" lg="8">
          <!-- Summary Cards -->
          <v-row v-if="statsStore.stats && statsStore.stats.items.length > 0" class="mb-4">
            <v-col cols="12" sm="6" md="3">
              <v-card
                class="summary-card"
                rounded="lg"
                elevation="4"
                color="primary"
                variant="tonal"
              >
                <v-card-text class="text-center pa-4">
                  <v-icon icon="mdi-chart-bar" size="40" color="primary" class="mb-2"></v-icon>
                  <div class="text-h5 font-weight-bold text-primary">
                    {{ statsStore.stats.items.length }}
                  </div>
                  <div class="text-caption text-grey">{{ $t('stats.analyticCodes') }}</div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card
                class="summary-card"
                rounded="lg"
                elevation="4"
                color="primary"
                variant="tonal"
              >
                <v-card-text class="text-center pa-4">
                  <v-icon icon="mdi-ruler" size="40" color="primary" class="mb-2"></v-icon>
                  <div class="text-h5 font-weight-bold text-primary">
                    {{ totalDistance.toFixed(1) }}
                  </div>
                  <div class="text-caption text-grey">{{ $t('stats.totalKm') }}</div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card
                class="summary-card"
                rounded="lg"
                elevation="4"
                color="secondary"
                variant="tonal"
              >
                <v-card-text class="text-center pa-4">
                  <v-icon icon="mdi-trending-up" size="40" color="secondary" class="mb-2"></v-icon>
                  <div class="text-h5 font-weight-bold text-secondary">
                    {{ averageDistance.toFixed(1) }}
                  </div>
                  <div class="text-caption text-grey">{{ $t('stats.avgKm') }}</div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card
                class="summary-card"
                rounded="lg"
                elevation="4"
                color="secondary"
                variant="tonal"
              >
                <v-card-text class="text-center pa-4">
                  <v-icon icon="mdi-chart-line-variant" size="40" color="secondary" class="mb-2"></v-icon>
                  <div class="text-h5 font-weight-bold text-secondary">
                    {{ maxDistance.toFixed(1) }}
                  </div>
                  <div class="text-caption text-grey">{{ $t('stats.maxKm') }}</div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <!-- Chart Card -->
          <v-expand-transition>
            <v-card
              v-if="statsStore.stats && statsStore.stats.items.length > 0"
              class="chart-card mb-4 slide-in-right"
              elevation="8"
              rounded="xl"
            >
              <v-card-title class="pa-6 pb-4">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-chart-bar" size="32" color="primary" class="mr-3"></v-icon>
                  <span class="text-h5 font-weight-bold">{{ $t('stats.distanceByCode') }}</span>
                </div>
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-6">
                <div class="chart-container">
                  <Bar
                    :data="chartData"
                    :options="chartOptions"
                  />
                </div>
              </v-card-text>
            </v-card>
          </v-expand-transition>

          <!-- Table Card -->
          <v-expand-transition>
            <v-card
              v-if="statsStore.stats"
              class="table-card slide-in-right"
              elevation="8"
              rounded="xl"
            >
              <v-card-title class="pa-6 pb-4">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-table" size="32" color="primary" class="mr-3"></v-icon>
                  <span class="text-h5 font-weight-bold">{{ $t('stats.detailedStats') }}</span>
                </div>
              </v-card-title>

              <v-divider></v-divider>

              <v-card-text class="pa-0">
                <v-data-table
                  :headers="headers"
                  :items="statsStore.stats.items"
                  :items-per-page="10"
                  class="stats-table"
                  item-value="analyticCode"
                >
                  <template #item.totalDistanceKm="{ item }">
                    <v-chip
                      color="primary"
                      variant="tonal"
                      size="small"
                    >
                      {{ item.totalDistanceKm.toFixed(2) }} {{ $t('common.km') }}
                    </v-chip>
                  </template>

                  <template #item.analyticCode="{ item }">
                    <div class="d-flex align-center">
                      <v-icon icon="mdi-tag" size="16" class="mr-2" color="secondary"></v-icon>
                      <span class="font-weight-medium">{{ item.analyticCode }}</span>
                    </div>
                  </template>

                  <template #no-data>
                    <div class="text-center pa-8">
                      <v-icon icon="mdi-information" size="64" color="grey" class="mb-4"></v-icon>
                      <p class="text-h6 text-grey">{{ $t('stats.noData') }}</p>
                      <p class="text-body-2 text-grey">{{ $t('stats.clickToLoad') }}</p>
                    </div>
                  </template>
                </v-data-table>
              </v-card-text>
            </v-card>
          </v-expand-transition>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useStatsStore } from '@/stores/statsStore';
import { Bar } from 'vue-chartjs';
import type { ChartData, ChartOptions } from 'chart.js';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

const { t } = useI18n();
const statsStore = useStatsStore();

const filters = ref({
  from: '',
  to: '',
  groupBy: 'none',
});

const groupByOptions = computed(() => [
  { title: t('stats.groupByOptions.none'), value: 'none' },
  { title: t('stats.groupByOptions.day'), value: 'day' },
  { title: t('stats.groupByOptions.month'), value: 'month' },
  { title: t('stats.groupByOptions.year'), value: 'year' },
]);

const headers = computed(() => [
  { title: t('stats.analyticCodes'), key: 'analyticCode', sortable: true },
  { title: t('stats.totalDistance'), key: 'totalDistanceKm', sortable: true },
  { title: t('stats.periodStart'), key: 'periodStart' },
  { title: t('stats.periodEnd'), key: 'periodEnd' },
  { title: t('stats.group'), key: 'group' },
]);

const totalDistance = computed(() => {
  if (!statsStore.stats) return 0;
  return statsStore.stats.items.reduce((sum, item) => sum + item.totalDistanceKm, 0);
});

const averageDistance = computed(() => {
  if (!statsStore.stats || statsStore.stats.items.length === 0) return 0;
  return totalDistance.value / statsStore.stats.items.length;
});

const maxDistance = computed(() => {
  if (!statsStore.stats || statsStore.stats.items.length === 0) return 0;
  return Math.max(...statsStore.stats.items.map(item => item.totalDistanceKm));
});

const chartData = computed<ChartData<'bar'>>(() => {
  if (!statsStore.stats || statsStore.stats.items.length === 0) {
    return {
      labels: [],
      datasets: [],
    };
  }

  const sortedItems = [...statsStore.stats.items].sort((a, b) => b.totalDistanceKm - a.totalDistanceKm);

  return {
    labels: sortedItems.map((item) => item.analyticCode),
    datasets: [
      {
        label: 'Total Distance (km)',
        data: sortedItems.map((item) => item.totalDistanceKm),
        backgroundColor: 'rgba(220, 38, 38, 0.6)',
        borderColor: 'rgba(220, 38, 38, 1)',
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
      },
    ],
  };
});

const chartOptions: ChartOptions<'bar'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      titleFont: {
        size: 14,
        weight: 'bold',
      },
      bodyFont: {
        size: 13,
      },
      cornerRadius: 8,
      displayColors: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.05)',
      },
      ticks: {
        font: {
          size: 12,
        },
        callback: function(value) {
          return value + ' km';
        },
      },
    },
    x: {
      grid: {
        display: false,
      },
      ticks: {
        font: {
          size: 11,
        },
        maxRotation: 45,
        minRotation: 45,
      },
    },
  },
};

const loadStats = () => {
  statsStore.fetchStats(
    filters.value.from || undefined,
    filters.value.to || undefined,
    filters.value.groupBy
  );
};

onMounted(() => {
  loadStats();
});
</script>

<style scoped>
.stats-view {
  position: relative;
  z-index: 1;
}

.hero-title {
  font-size: 48px;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 16px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
  position: relative;
  display: inline-block;
}

.hero-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 100%);
  border-radius: 2px;
}

.hero-subtitle {
  margin-top: 24px;
  color: #6B7280;
  font-weight: 400;
}

@media (max-width: 600px) {
  .hero-title {
    font-size: 32px;
  }
}

.filters-card,
.chart-card,
.table-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.summary-card {
  transition: all 0.3s ease;
  height: 100%;
}

.summary-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.chart-container {
  height: 400px;
  position: relative;
}

.stats-table {
  background: transparent;
}

.stats-table :deep(.v-data-table__thead) {
  background: rgba(99, 102, 241, 0.05);
}

.stats-table :deep(.v-data-table__tr) {
  transition: all 0.2s ease;
}

.stats-table :deep(.v-data-table__tr:hover) {
  background: rgba(99, 102, 241, 0.05);
}
</style>

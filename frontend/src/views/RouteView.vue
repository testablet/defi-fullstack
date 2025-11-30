<template>
  <div class="route-view">
    <v-container class="py-8">
      <v-row justify="center">
        <v-col cols="12" lg="10" xl="8">
          <!-- Hero Section -->
          <div class="text-center mb-8 fade-in-up">
            <h1 class="hero-title">
              {{ $t('routes.title') }}
            </h1>
            <p class="text-h6 text-grey-darken-2 hero-subtitle">
              {{ $t('routes.subtitle') }}
            </p>
          </div>

          <!-- Main Card -->
          <v-card
            class="route-card fade-in-up"
            elevation="8"
            rounded="xl"
          >
            <v-card-title class="pa-6 pb-4">
              <div class="d-flex align-center">
                <v-icon icon="mdi-map-marker-path" size="32" color="primary" class="mr-3"></v-icon>
                <span class="text-h5 font-weight-bold">{{ $t('routes.createRoute') }}</span>
              </div>
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
              <v-form @submit.prevent="handleSubmit" class="route-form">
                <!-- From Station -->
                <v-card
                  variant="outlined"
                  class="mb-4 station-selector"
                  rounded="lg"
                >
                  <v-card-text class="pa-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon icon="mdi-map-marker" color="primary" class="mr-2"></v-icon>
                      <span class="text-subtitle-1 font-weight-medium">{{ $t('routes.departureStation') }}</span>
                    </div>
                    <v-select
                      v-model="form.fromStationId"
                      :items="stations"
                      item-title="longName"
                      item-value="shortName"
                      :placeholder="$t('routes.selectDeparture')"
                      prepend-inner-icon="mdi-train"
                      variant="solo-filled"
                      density="comfortable"
                      hide-details
                      class="mt-2"
                    ></v-select>
                  </v-card-text>
                </v-card>

                <!-- Arrow Icon -->
                <div class="text-center my-2">
                  <v-icon
                    icon="mdi-arrow-down"
                    size="32"
                    color="primary"
                    class="arrow-icon"
                  ></v-icon>
                </div>

                <!-- To Station -->
                <v-card
                  variant="outlined"
                  class="mb-4 station-selector"
                  rounded="lg"
                >
                  <v-card-text class="pa-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon icon="mdi-map-marker-check" color="secondary" class="mr-2"></v-icon>
                      <span class="text-subtitle-1 font-weight-medium">{{ $t('routes.arrivalStation') }}</span>
                    </div>
                    <v-select
                      v-model="form.toStationId"
                      :items="stations"
                      item-title="longName"
                      item-value="shortName"
                      :placeholder="$t('routes.selectArrival')"
                      prepend-inner-icon="mdi-train"
                      variant="solo-filled"
                      density="comfortable"
                      hide-details
                      class="mt-2"
                    ></v-select>
                  </v-card-text>
                </v-card>

                <!-- Analytic Code -->
                <v-card
                  variant="outlined"
                  class="mb-6"
                  rounded="lg"
                >
                  <v-card-text class="pa-4">
                    <div class="d-flex align-center mb-2">
                      <v-icon icon="mdi-tag" color="secondary" class="mr-2"></v-icon>
                      <span class="text-subtitle-1 font-weight-medium">{{ $t('routes.analyticCode') }}</span>
                    </div>
                    <v-text-field
                      v-model="form.analyticCode"
                      :placeholder="$t('routes.enterAnalyticCode')"
                      prepend-inner-icon="mdi-identifier"
                      variant="solo-filled"
                      density="comfortable"
                      hide-details
                      class="mt-2"
                    ></v-text-field>
                  </v-card-text>
                </v-card>

                <!-- Submit Button -->
                <v-btn
                  type="submit"
                  color="primary"
                  size="x-large"
                  :loading="routeStore.loading"
                  :disabled="!isFormValid"
                  block
                  rounded="xl"
                  class="submit-btn"
                  prepend-icon="mdi-calculator"
                >
                  <span class="text-h6 font-weight-bold">
                    {{ routeStore.loading ? $t('routes.calculating') : $t('routes.calculateRoute') }}
                  </span>
                </v-btn>
              </v-form>

              <!-- Error Alert -->
              <v-expand-transition>
                <v-alert
                  v-if="routeStore.error"
                  type="error"
                  variant="tonal"
                  rounded="lg"
                  class="mt-4"
                  dismissible
                  @click:close="routeStore.clearError"
                >
                  <v-alert-title>{{ $t('routes.error') }}</v-alert-title>
                  {{ routeStore.error }}
                </v-alert>
              </v-expand-transition>
            </v-card-text>
          </v-card>

          <!-- Result Card -->
          <v-expand-transition>
            <div v-if="lastRoute" class="result-container mt-8">
              <!-- Success Header -->
              <div class="result-header">
                <div class="success-badge">
                  <v-icon icon="mdi-check-circle" size="40" color="white"></v-icon>
                </div>
                <div class="result-title-section">
                  <h2 class="result-title">{{ $t('routes.routeCalculated') }}</h2>
                  <p class="result-subtitle">{{ $t('routes.optimalPathFound') }}</p>
                </div>
                <div class="result-id">
                  <v-chip size="small" color="secondary" variant="tonal">
                    ID: {{ lastRoute.id.slice(0, 8) }}
                  </v-chip>
                </div>
              </div>

              <!-- Main Result Card -->
              <v-card
                class="result-card-main"
                elevation="12"
                rounded="xl"
              >
                <!-- Key Metrics Row -->
                <div class="metrics-row">
                  <div class="metric-card distance-metric">
                    <div class="metric-icon-wrapper">
                      <v-icon icon="mdi-ruler" size="56" color="primary"></v-icon>
                      <div class="metric-glow"></div>
                    </div>
                    <div class="metric-content">
                      <div class="metric-label">{{ $t('routes.totalDistance') }}</div>
                      <div class="metric-value">{{ lastRoute.distanceKm.toFixed(2) }}</div>
                      <div class="metric-unit">{{ $t('routes.kilometers') }}</div>
                    </div>
                  </div>

                  <div class="metric-divider"></div>

                  <div class="metric-card stations-metric">
                    <div class="metric-icon-wrapper">
                      <v-icon icon="mdi-map-marker-multiple" size="56" color="secondary"></v-icon>
                      <div class="metric-glow"></div>
                    </div>
                    <div class="metric-content">
                      <div class="metric-label">{{ $t('routes.stations') }}</div>
                      <div class="metric-value">{{ lastRoute.path.length }}</div>
                      <div class="metric-unit">{{ $t('routes.stationsCount') }}</div>
                    </div>
                  </div>

                  <div class="metric-divider"></div>

                  <div class="metric-card code-metric">
                    <div class="metric-icon-wrapper">
                      <v-icon icon="mdi-tag" size="56" color="secondary"></v-icon>
                      <div class="metric-glow"></div>
                    </div>
                    <div class="metric-content">
                      <div class="metric-label">{{ $t('routes.analyticCodeLabel') }}</div>
                      <div class="metric-value-small">{{ lastRoute.analyticCode }}</div>
                      <div class="metric-unit">reference</div>
                    </div>
                  </div>
                </div>

                <v-divider class="my-6"></v-divider>

                <!-- Path Visualization Section -->
                <div class="path-section">
                  <div class="path-section-header">
                    <v-icon icon="mdi-route" size="28" color="primary" class="mr-3"></v-icon>
                    <h3 class="path-section-title">Route Path Visualization</h3>
                  </div>
                  <RoutePathVisualization
                    :path="lastRoute.path"
                    :distance="lastRoute.distanceKm"
                    :stations="stations"
                  />
                </div>

                <!-- Additional Info -->
                <v-divider class="my-6"></v-divider>
                
                <div class="additional-info">
                  <div class="info-item">
                    <v-icon icon="mdi-calendar-clock" size="20" color="grey-darken-1" class="mr-2"></v-icon>
                    <span class="info-label">Created:</span>
                    <span class="info-value">{{ formatDate(lastRoute.createdAt) }}</span>
                  </div>
                  <div class="info-item">
                    <v-icon icon="mdi-clock-outline" size="20" color="grey-darken-1" class="mr-2"></v-icon>
                    <span class="info-label">Est. Time:</span>
                    <span class="info-value">{{ estimatedTime(lastRoute.distanceKm) }}</span>
                  </div>
                  <div class="info-item">
                    <v-icon icon="mdi-speedometer" size="20" color="grey-darken-1" class="mr-2"></v-icon>
                    <span class="info-label">Avg Speed:</span>
                    <span class="info-value">{{ averageSpeed(lastRoute.distanceKm, lastRoute.path.length) }} km/h</span>
                  </div>
                </div>
              </v-card>
            </div>
          </v-expand-transition>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouteStore } from '@/stores/routeStore';
import type { RouteRequest } from '@/services/api';
import RoutePathVisualization from '@/components/RoutePathVisualization.vue';
import axios from 'axios';

const routeStore = useRouteStore();
const lastRoute = ref<any>(null);

const form = ref<RouteRequest>({
  fromStationId: '',
  toStationId: '',
  analyticCode: '',
});

const stations = ref<Array<{ shortName: string; longName: string }>>([]);

const isFormValid = computed(() => {
  return (
    form.value.fromStationId !== '' &&
    form.value.toStationId !== '' &&
    form.value.analyticCode !== '' &&
    form.value.fromStationId !== form.value.toStationId
  );
});

const handleSubmit = async () => {
  const route = await routeStore.createRoute(form.value);
  if (route) {
    lastRoute.value = route;
    form.value = {
      fromStationId: '',
      toStationId: '',
      analyticCode: '',
    };
    // Scroll to result
    setTimeout(() => {
      const resultElement = document.querySelector('.result-container');
      if (resultElement) {
        resultElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }, 100);
  }
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const estimatedTime = (distance: number): string => {
  const hours = distance / 50;
  if (hours < 1) {
    return Math.round(hours * 60) + ' min';
  }
  const h = Math.floor(hours);
  const m = Math.round((hours - h) * 60);
  return `${h}h ${m}m`;
};

const averageSpeed = (distance: number, stations: number): number => {
  return Math.round(distance / (stations * 0.02));
};

onMounted(async () => {
  try {
    // Try multiple paths to load stations
    let response;
    try {
      response = await axios.get('/stations.json');
    } catch {
      try {
        response = await axios.get('/data/stations.json');
      } catch {
        response = await axios.get('http://localhost/stations.json');
      }
    }
    stations.value = response.data;
  } catch (error) {
    console.error('Failed to load stations', error);
    // Fallback: use a minimal set of stations for demo
    stations.value = [
      { shortName: 'MX', longName: 'Montreux' },
      { shortName: 'ZW', longName: 'Zweisimmen' },
      { shortName: 'CGE', longName: 'Montreux-Collège' },
    ];
  }
});
</script>

<style scoped>
.route-view {
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

.route-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.station-selector {
  transition: all 0.3s ease;
}

.station-selector:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.arrow-icon {
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.submit-btn {
  height: 56px !important;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
  transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
}

/* Result Container */
.result-container {
  animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Result Header */
.result-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
  padding: 20px;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(31, 41, 55, 0.1) 100%);
  border-radius: 20px;
  border: 2px solid rgba(220, 38, 38, 0.2);
}

.success-badge {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);
  animation: successPulse 2s ease-in-out infinite;
  flex-shrink: 0;
}

@keyframes successPulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(220, 38, 38, 0.5);
  }
}

.result-title-section {
  flex: 1;
}

.result-title {
  font-size: 28px;
  font-weight: 700;
  color: #1F2937;
  margin-bottom: 4px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.result-subtitle {
  font-size: 14px;
  color: #6B7280;
  margin: 0;
}

.result-id {
  flex-shrink: 0;
}

/* Main Result Card */
.result-card-main {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
  border: 2px solid rgba(220, 38, 38, 0.1);
  overflow: hidden;
  position: relative;
}

.result-card-main::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 100%);
}

/* Metrics Row */
.metrics-row {
  display: flex;
  align-items: stretch;
  padding: 32px;
  gap: 24px;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.02) 0%, rgba(31, 41, 55, 0.02) 100%);
}

.metric-card {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px;
  background: white;
  border-radius: 16px;
  border: 2px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.metric-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.metric-card:hover::before {
  transform: scaleX(1);
}

.metric-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  border-color: rgba(220, 38, 38, 0.3);
}

.metric-icon-wrapper {
  position: relative;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(31, 41, 55, 0.1) 100%);
  flex-shrink: 0;
}

.metric-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 16px;
  background: inherit;
  opacity: 0.5;
  animation: metricGlow 2s ease-in-out infinite;
}

@keyframes metricGlow {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.6;
    transform: scale(1.1);
  }
}

.metric-content {
  flex: 1;
}

.metric-label {
  font-size: 12px;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.metric-value {
  font-size: 42px;
  font-weight: 700;
  color: #1F2937;
  line-height: 1;
  margin-bottom: 4px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.metric-value-small {
  font-size: 24px;
  font-weight: 700;
  color: #1F2937;
  line-height: 1;
  margin-bottom: 4px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.metric-unit {
  font-size: 13px;
  color: #9CA3AF;
  font-weight: 500;
}

.metric-divider {
  width: 1px;
  background: linear-gradient(180deg, transparent 0%, rgba(220, 38, 38, 0.2) 50%, transparent 100%);
  margin: 0 8px;
}

.distance-metric .metric-icon-wrapper {
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.15) 0%, rgba(239, 68, 68, 0.15) 100%);
}

.stations-metric .metric-icon-wrapper {
  background: linear-gradient(135deg, rgba(31, 41, 55, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%);
}

.code-metric .metric-icon-wrapper {
  background: linear-gradient(135deg, rgba(31, 41, 55, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%);
}

/* Path Section */
.path-section {
  padding: 32px;
  background: white;
}

.path-section-header {
  display: flex;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid rgba(220, 38, 38, 0.1);
}

.path-section-title {
  font-size: 20px;
  font-weight: 700;
  color: #1F2937;
  margin: 0;
}

/* Additional Info */
.additional-info {
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 20px 32px;
  background: linear-gradient(135deg, rgba(249, 250, 251, 0.5) 0%, rgba(243, 244, 246, 0.5) 100%);
  flex-wrap: wrap;
  gap: 16px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.info-label {
  font-size: 13px;
  color: #6B7280;
  font-weight: 500;
}

.info-value {
  font-size: 13px;
  color: #1F2937;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 960px) {
  .metrics-row {
    flex-direction: column;
    gap: 16px;
  }

  .metric-divider {
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(220, 38, 38, 0.2) 50%, transparent 100%);
    margin: 8px 0;
  }

  .result-header {
    flex-wrap: wrap;
  }

  .additional-info {
    flex-direction: column;
    align-items: flex-start;
  }
}

.stat-card {
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

</style>

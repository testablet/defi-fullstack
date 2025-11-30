<template>
  <div class="route-path-visualization">
    <!-- Header with stats and controls -->
    <div class="path-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap mb-4">
        <div>
          <h3 class="text-h5 font-weight-bold mb-2">
            <v-icon icon="mdi-route" color="primary" class="mr-2"></v-icon>
            Route Path
          </h3>
          <p class="text-body-2 text-grey-darken-1">
            {{ path.length }} station{{ path.length > 1 ? 's' : '' }} • {{ distance.toFixed(2) }} km
          </p>
        </div>
        <div class="d-flex align-center gap-2">
          <v-chip
            color="primary"
            variant="tonal"
            size="large"
            prepend-icon="mdi-ruler"
            class="mr-2"
          >
            {{ distance.toFixed(2) }} km
          </v-chip>
          <v-btn
            icon
            variant="text"
            size="small"
            @click="toggleCompactView"
            :title="compactView ? 'Expand view' : 'Compact view'"
          >
            <v-icon>{{ compactView ? 'mdi-arrow-expand' : 'mdi-arrow-collapse' }}</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- Summary Cards -->
      <v-row dense>
        <v-col cols="6" sm="3">
          <v-card
            class="summary-card-mini"
            rounded="lg"
            elevation="2"
            color="primary"
            variant="tonal"
          >
            <v-card-text class="text-center pa-3">
              <v-icon icon="mdi-map-marker-distance" size="24" color="primary" class="mb-1"></v-icon>
              <div class="text-h6 font-weight-bold text-primary">{{ path.length }}</div>
              <div class="text-caption text-grey">Stations</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card
            class="summary-card-mini"
            rounded="lg"
            elevation="2"
            color="secondary"
            variant="tonal"
          >
            <v-card-text class="text-center pa-3">
              <v-icon icon="mdi-ruler" size="24" color="success" class="mb-1"></v-icon>
              <div class="text-h6 font-weight-bold text-success">{{ distance.toFixed(1) }}</div>
              <div class="text-caption text-grey">km</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card
            class="summary-card-mini"
            rounded="lg"
            elevation="2"
            color="secondary"
            variant="tonal"
          >
            <v-card-text class="text-center pa-3">
              <v-icon icon="mdi-clock-outline" size="24" color="secondary" class="mb-1"></v-icon>
              <div class="text-h6 font-weight-bold text-secondary">{{ estimatedTime }}</div>
              <div class="text-caption text-grey">Est. Time</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card
            class="summary-card-mini"
            rounded="lg"
            elevation="2"
            color="accent"
            variant="tonal"
          >
            <v-card-text class="text-center pa-3">
              <v-icon icon="mdi-speedometer" size="24" color="accent" class="mb-1"></v-icon>
              <div class="text-h6 font-weight-bold text-accent">{{ averageSpeed }}</div>
              <div class="text-caption text-grey">km/h avg</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Compact View (for long routes) -->
    <div v-if="compactView && path.length > 8" class="compact-view-container">
      <div class="compact-stations">
        <div
          v-for="(station, index) in path"
          :key="index"
          class="compact-station"
          :class="{
            'start': index === 0,
            'end': index === path.length - 1,
            'intermediate': index > 0 && index < path.length - 1
          }"
        >
          <div class="compact-node">
            <v-icon
              :icon="getStationIcon(index)"
              :size="index === 0 || index === path.length - 1 ? 20 : 16"
              :color="getStationColor(index)"
            ></v-icon>
          </div>
          <div class="compact-info">
            <div class="compact-code font-weight-bold">{{ station }}</div>
            <div class="compact-name text-caption">{{ getStationName(station) }}</div>
          </div>
          <v-chip
            v-if="index < path.length - 1"
            size="x-small"
            color="primary"
            variant="tonal"
            class="compact-distance"
          >
            {{ getSegmentDistance(index) }}km
          </v-chip>
        </div>
      </div>
    </div>

    <!-- Full View with Scrollable Timeline -->
    <div v-else class="path-container-wrapper">
      <!-- Desktop: Horizontal Scrollable Timeline -->
      <div class="path-timeline-scroll d-none d-md-block" ref="timelineScroll">
        <div class="path-timeline-inner" :style="{ width: `${path.length * 200}px` }">
          <div
            v-for="(station, index) in path"
            :key="index"
            class="path-node-wrapper"
            :style="{ width: '200px' }"
          >
            <div class="path-node-container">
              <!-- Connection Line -->
              <div
                v-if="index < path.length - 1"
                class="path-connection"
                :class="{ 'animated': isAnimated }"
              >
                <div class="connection-line"></div>
                <div class="connection-train">
                  <v-icon icon="mdi-train" size="20" color="primary"></v-icon>
                </div>
              </div>

              <!-- Station Node -->
              <div
                class="path-node"
                :class="{
                  'start': index === 0,
                  'end': index === path.length - 1,
                  'intermediate': index > 0 && index < path.length - 1,
                  'animated': isAnimated
                }"
                :style="{ animationDelay: `${index * 0.1}s` }"
              >
                <div class="node-icon-wrapper">
                  <v-icon
                    :icon="getStationIcon(index)"
                    :size="index === 0 || index === path.length - 1 ? 32 : 24"
                    :color="getStationColor(index)"
                  ></v-icon>
                </div>
                <div class="node-pulse" v-if="index === 0 || index === path.length - 1"></div>
                <div class="node-number">{{ index + 1 }}</div>
              </div>

              <!-- Station Info Card -->
              <v-card
                class="station-card"
                :class="{ 'animated': isAnimated }"
                :style="{ animationDelay: `${index * 0.1 + 0.2}s` }"
                elevation="4"
                rounded="lg"
              >
                <v-card-text class="pa-3">
                  <div class="station-code font-weight-bold text-h6 mb-1">{{ station }}</div>
                  <div class="station-name text-caption text-grey-darken-1 mb-2">
                    {{ getStationName(station) }}
                  </div>
                  <div v-if="index < path.length - 1" class="segment-info">
                    <v-chip
                      size="small"
                      color="primary"
                      variant="tonal"
                      prepend-icon="mdi-arrow-right"
                    >
                      {{ getSegmentDistance(index) }} km
                    </v-chip>
                  </div>
                </v-card-text>
              </v-card>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile: Vertical Scrollable Timeline -->
      <div class="path-timeline-vertical-scroll d-flex d-md-none" ref="verticalScroll">
        <div class="path-timeline-vertical-inner">
          <div
            v-for="(station, index) in path"
            :key="index"
            class="path-node-wrapper-vertical"
          >
            <div class="path-node-container-vertical">
              <!-- Connection Line -->
              <div
                v-if="index < path.length - 1"
                class="path-connection-vertical"
                :class="{ 'animated': isAnimated }"
              >
                <div class="connection-line-vertical"></div>
                <div class="connection-train-vertical">
                  <v-icon icon="mdi-train" size="16" color="primary"></v-icon>
                </div>
              </div>

              <!-- Station Node -->
              <div
                class="path-node-vertical"
                :class="{
                  'start': index === 0,
                  'end': index === path.length - 1,
                  'intermediate': index > 0 && index < path.length - 1,
                  'animated': isAnimated
                }"
                :style="{ animationDelay: `${index * 0.1}s` }"
              >
                <div class="node-icon-wrapper-vertical">
                  <v-icon
                    :icon="getStationIcon(index)"
                    :size="index === 0 || index === path.length - 1 ? 28 : 20"
                    :color="getStationColor(index)"
                  ></v-icon>
                </div>
                <div class="node-pulse-vertical" v-if="index === 0 || index === path.length - 1"></div>
                <div class="node-number-vertical">{{ index + 1 }}</div>
              </div>

              <!-- Station Info Card -->
              <v-card
                class="station-card-vertical"
                :class="{ 'animated': isAnimated }"
                :style="{ animationDelay: `${index * 0.1 + 0.2}s` }"
                elevation="4"
                rounded="lg"
              >
                <v-card-text class="pa-3">
                  <div class="d-flex align-center justify-space-between">
                    <div class="flex-grow-1">
                      <div class="station-code-vertical font-weight-bold text-h6 mb-1">{{ station }}</div>
                      <div class="station-name-vertical text-caption text-grey-darken-1">
                        {{ getStationName(station) }}
                      </div>
                    </div>
                    <v-chip
                      v-if="index < path.length - 1"
                      size="small"
                      color="primary"
                      variant="tonal"
                      class="ml-2"
                    >
                      {{ getSegmentDistance(index) }} km
                    </v-chip>
                  </div>
                </v-card-text>
              </v-card>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll Indicators -->
      <div v-if="path.length > 6" class="scroll-indicators">
        <v-btn
          icon
          variant="text"
          size="small"
          class="scroll-btn scroll-left"
          @click="scrollLeft"
          :disabled="scrollPosition <= 0"
        >
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>
        <v-btn
          icon
          variant="text"
          size="small"
          class="scroll-btn scroll-right"
          @click="scrollRight"
        >
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-container mt-4">
      <div class="d-flex align-center justify-space-between mb-2">
        <span class="text-caption text-grey-darken-1">Route Progress</span>
        <span class="text-caption font-weight-bold">{{ Math.round((distance / maxDistance) * 100) }}%</span>
      </div>
      <v-progress-linear
        :model-value="(distance / maxDistance) * 100"
        color="primary"
        height="8"
        rounded
        class="progress-bar"
      ></v-progress-linear>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';

interface Props {
  path: string[];
  distance: number;
  stations?: Array<{ shortName: string; longName: string }>;
}

const props = withDefaults(defineProps<Props>(), {
  stations: () => [],
});

const isAnimated = ref(false);
const compactView = ref(props.path.length > 10);
const stationNames = ref<Record<string, string>>({});
const timelineScroll = ref<HTMLElement | null>(null);
const verticalScroll = ref<HTMLElement | null>(null);
const scrollPosition = ref(0);
const maxDistance = ref(200); // Maximum expected distance for progress bar

onMounted(() => {
  // Load station names
  if (props.stations.length > 0) {
    props.stations.forEach(station => {
      stationNames.value[station.shortName] = station.longName;
    });
  } else {
    axios.get('/stations.json').then(response => {
      response.data.forEach((station: any) => {
        stationNames.value[station.shortName] = station.longName;
      });
    }).catch(() => {});
  }

  // Update max distance based on actual distance
  maxDistance.value = Math.max(props.distance * 1.2, 100);

  // Trigger animation
  setTimeout(() => {
    isAnimated.value = true;
  }, 100);
});

const toggleCompactView = () => {
  compactView.value = !compactView.value;
};

const getStationIcon = (index: number): string => {
  if (index === 0) return 'mdi-map-marker';
  if (index === props.path.length - 1) return 'mdi-map-marker-check';
  return 'mdi-circle-small';
};

const getStationColor = (index: number): string => {
  if (index === 0) return 'primary'; // Rouge pour départ
  if (index === props.path.length - 1) return 'secondary'; // Noir pour arrivée
  return 'grey-darken-1'; // Gris pour intermédiaires
};

const getStationName = (code: string): string => {
  return stationNames.value[code] || code;
};

const getSegmentDistance = (index: number): string => {
  const segmentDistance = props.distance / (props.path.length - 1);
  return segmentDistance.toFixed(1);
};

const estimatedTime = computed(() => {
  const hours = props.distance / 50;
  if (hours < 1) {
    return Math.round(hours * 60) + ' min';
  }
  const h = Math.floor(hours);
  const m = Math.round((hours - h) * 60);
  return `${h}h ${m}m`;
});

const averageSpeed = computed(() => {
  return Math.round(props.distance / (props.path.length * 0.02)); // Approximate
});

const scrollLeft = () => {
  if (timelineScroll.value) {
    const scrollContainer = timelineScroll.value.querySelector('.path-timeline-inner');
    if (scrollContainer) {
      scrollContainer.scrollBy({ left: -400, behavior: 'smooth' });
    }
  }
  if (verticalScroll.value) {
    verticalScroll.value.scrollBy({ top: -300, behavior: 'smooth' });
  }
};

const scrollRight = () => {
  if (timelineScroll.value) {
    const scrollContainer = timelineScroll.value.querySelector('.path-timeline-inner');
    if (scrollContainer) {
      scrollContainer.scrollBy({ left: 400, behavior: 'smooth' });
    }
  }
  if (verticalScroll.value) {
    verticalScroll.value.scrollBy({ top: 300, behavior: 'smooth' });
  }
};
</script>

<style scoped>
.route-path-visualization {
  position: relative;
  width: 100%;
}

.path-header {
  padding: 20px;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.08) 0%, rgba(31, 41, 55, 0.08) 100%);
  border-radius: 20px;
  border: 1px solid rgba(220, 38, 38, 0.15);
}

.summary-card-mini {
  transition: all 0.3s ease;
  height: 100%;
}

.summary-card-mini:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

/* Compact View */
.compact-view-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
  border-radius: 20px;
  border: 2px solid rgba(220, 38, 38, 0.15);
  padding: 24px;
  max-height: 500px;
  overflow-y: auto;
}

.compact-stations {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.compact-station {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: white;
  border-radius: 12px;
  border: 2px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.compact-station:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: rgba(220, 38, 38, 0.3);
}

.compact-station.start {
  border-color: rgba(220, 38, 38, 0.4);
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(239, 68, 68, 0.1) 100%);
}

.compact-station.end {
  border-color: rgba(31, 41, 55, 0.4);
  background: linear-gradient(135deg, rgba(31, 41, 55, 0.1) 0%, rgba(0, 0, 0, 0.1) 100%);
}

.compact-node {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
}

.compact-info {
  flex: 1;
  min-width: 0;
}

.compact-code {
  font-size: 14px;
  color: #1E293B;
  margin-bottom: 2px;
}

.compact-name {
  font-size: 11px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.compact-distance {
  flex-shrink: 0;
}

/* Desktop Scrollable Timeline */
.path-container-wrapper {
  position: relative;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
  border-radius: 24px;
  border: 2px solid rgba(220, 38, 38, 0.15);
  padding: 40px 20px;
  overflow: hidden;
}

.path-timeline-scroll {
  overflow-x: auto;
  overflow-y: hidden;
  padding: 20px 0;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
  scrollbar-color: rgba(220, 38, 38, 0.3) transparent;
}

.path-timeline-scroll::-webkit-scrollbar {
  height: 8px;
}

.path-timeline-scroll::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 4px;
}

.path-timeline-scroll::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #DC2626 0%, #1F2937 100%);
  border-radius: 4px;
}

.path-timeline-scroll::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #EF4444 0%, #374151 100%);
}

.path-timeline-inner {
  display: flex;
  min-width: 100%;
  position: relative;
  padding: 0 100px;
}

.path-node-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex-shrink: 0;
  z-index: 2;
}

.path-node-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.path-connection {
  position: absolute;
  top: 40px;
  left: 50%;
  width: calc(200px - 80px);
  height: 4px;
  z-index: 1;
  transform: translateX(-50%);
}

.connection-line {
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, rgba(220, 38, 38, 0.3) 0%, rgba(220, 38, 38, 0.6) 50%, rgba(31, 41, 55, 0.3) 100%);
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.connection-line::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  animation: shimmer 2s infinite;
}

.connection-train {
  position: absolute;
  top: -8px;
  left: 50%;
  transform: translateX(-50%);
  background: white;
  border-radius: 50%;
  padding: 4px;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
  animation: moveTrain 4s infinite;
}

.path-node {
  position: relative;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  z-index: 3;
  transition: all 0.3s ease;
  margin-bottom: 16px;
}

.path-node.start {
  background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
  box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);
  width: 90px;
  height: 90px;
}

.path-node.end {
  background: linear-gradient(135deg, #1F2937 0%, #000000 100%);
  box-shadow: 0 8px 24px rgba(31, 41, 55, 0.4);
  width: 90px;
  height: 90px;
}

.path-node.intermediate {
  background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
  width: 70px;
  height: 70px;
}

.path-node.animated {
  animation: nodePulse 0.6s ease-out;
}

.node-icon-wrapper {
  z-index: 2;
}

.node-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: inherit;
  opacity: 0.6;
  animation: pulse 2s infinite;
}

.node-number {
  position: absolute;
  bottom: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: bold;
  color: #DC2626;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  border: 2px solid #DC2626;
}

.station-card {
  width: 180px;
  margin-top: 8px;
  transition: all 0.3s ease;
  background: white;
}

.station-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.station-card.animated {
  animation: fadeInUp 0.5s ease-out;
}

.station-code {
  font-size: 18px;
  color: #1E293B;
}

.station-name {
  font-size: 12px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

/* Mobile Vertical Scrollable Timeline */
.path-timeline-vertical-scroll {
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 600px;
  padding: 20px;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
  scrollbar-color: rgba(220, 38, 38, 0.3) transparent;
}

.path-timeline-vertical-scroll::-webkit-scrollbar {
  width: 6px;
}

.path-timeline-vertical-scroll::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 3px;
}

.path-timeline-vertical-scroll::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #DC2626 0%, #1F2937 100%);
  border-radius: 3px;
}

.path-timeline-vertical-inner {
  position: relative;
  padding: 0 20px;
}

.path-node-wrapper-vertical {
  position: relative;
  display: flex;
  align-items: flex-start;
  width: 100%;
  margin-bottom: 24px;
}

.path-node-container-vertical {
  display: flex;
  align-items: flex-start;
  width: 100%;
  position: relative;
}

.path-connection-vertical {
  position: absolute;
  left: 35px;
  top: 70px;
  width: 4px;
  height: calc(100% - 70px);
  z-index: 1;
}

.connection-line-vertical {
  width: 4px;
  height: 100%;
  background: linear-gradient(180deg, rgba(220, 38, 38, 0.3) 0%, rgba(220, 38, 38, 0.6) 50%, rgba(31, 41, 55, 0.3) 100%);
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.connection-line-vertical::after {
  content: '';
  position: absolute;
  top: -100%;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  animation: shimmerVertical 2s infinite;
}

.connection-train-vertical {
  position: absolute;
  top: 50%;
  left: -6px;
  transform: translateY(-50%);
  background: white;
  border-radius: 50%;
  padding: 2px;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
  animation: moveTrainVertical 4s infinite;
}

.path-node-vertical {
  position: relative;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  z-index: 3;
  flex-shrink: 0;
  margin-right: 16px;
}

.path-node-vertical.start {
  background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
  box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
  width: 80px;
  height: 80px;
}

.path-node-vertical.end {
  background: linear-gradient(135deg, #10B981 0%, #34D399 100%);
  box-shadow: 0 6px 20px rgba(31, 41, 55, 0.4);
  width: 80px;
  height: 80px;
}

.path-node-vertical.intermediate {
  background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
}

.path-node-vertical.animated {
  animation: nodePulse 0.6s ease-out;
}

.node-icon-wrapper-vertical {
  z-index: 2;
}

.node-pulse-vertical {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: inherit;
  opacity: 0.6;
  animation: pulse 2s infinite;
}

.node-number-vertical {
  position: absolute;
  bottom: -6px;
  right: -6px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: bold;
  color: #6366F1;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  border: 2px solid #6366F1;
}

.station-card-vertical {
  flex: 1;
  transition: all 0.3s ease;
  background: white;
}

.station-card-vertical:hover {
  transform: translateX(4px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1) !important;
}

.station-card-vertical.animated {
  animation: slideInRight 0.5s ease-out;
}

.station-code-vertical {
  font-size: 16px;
  color: #1E293B;
}

.station-name-vertical {
  font-size: 12px;
}

/* Scroll Indicators */
.scroll-indicators {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 100%;
  display: flex;
  justify-content: space-between;
  pointer-events: none;
  padding: 0 10px;
}

.scroll-btn {
  pointer-events: all;
  background: rgba(255, 255, 255, 0.9) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.scroll-left {
  left: 10px;
}

.scroll-right {
  right: 10px;
}

/* Progress Bar */
.progress-container {
  padding: 16px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 12px;
}

.progress-bar {
  border-radius: 4px;
  overflow: hidden;
}

/* Animations */
@keyframes moveTrain {
  0% {
    left: 0;
  }
  100% {
    left: 100%;
  }
}

@keyframes moveTrainVertical {
  0% {
    top: 0;
  }
  100% {
    top: 100%;
  }
}

@keyframes shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

@keyframes shimmerVertical {
  0% {
    top: -100%;
  }
  100% {
    top: 100%;
  }
}

@keyframes nodePulse {
  0% {
    transform: scale(0.8);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 0.6;
  }
  50% {
    transform: scale(1.5);
    opacity: 0;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .path-timeline-inner {
    padding: 0 50px;
  }
  
  .path-node-wrapper {
    min-width: 150px;
  }
  
  .station-card {
    width: 140px;
  }
}

@media (max-width: 600px) {
  .compact-stations {
    grid-template-columns: 1fr;
  }
  
  .path-header {
    padding: 16px;
  }
  
  .summary-card-mini {
    margin-bottom: 8px;
  }
}
</style>

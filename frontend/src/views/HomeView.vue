<template>
  <div class="home-view">
    <v-container class="py-0">
      <!-- Hero Section -->
      <section class="hero-section">
        <div class="text-center py-16 fade-in-up">
          <h1 class="hero-title-main">
            {{ $t('home.title') }}
          </h1>
          <p class="hero-subtitle-main">
            {{ $t('home.subtitle') }}
          </p>
          <div class="mt-8">
            <v-btn
              to="/routes"
              color="primary"
              size="x-large"
              rounded="xl"
              prepend-icon="mdi-map"
              class="mr-4 mb-4"
            >
              {{ $t('home.calculateRoute') }}
            </v-btn>
            <v-btn
              to="/stats"
              color="secondary"
              size="x-large"
              variant="outlined"
              rounded="xl"
              prepend-icon="mdi-chart-line"
              class="mb-4"
            >
              {{ $t('home.viewStats') }}
            </v-btn>
          </div>
        </div>
      </section>

      <!-- Project Presentation Section -->
      <section class="section py-16">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="text-center mb-12">
              <h2 class="section-title">
                {{ $t('home.aboutTitle') }}
              </h2>
              <p class="section-subtitle">
                {{ $t('home.aboutSubtitle') }}
              </p>
            </div>

            <v-card
              class="info-card mb-8"
              elevation="8"
              rounded="xl"
            >
              <v-card-text class="pa-8">
                <p class="text-h6 mb-6 text-justify">
                  <i18n-t keypath="home.aboutText1" tag="span">
                    <template #challenge>
                      <strong>{{ $t('home.challenge') }}</strong>
                    </template>
                    <template #mob>
                      <strong>{{ $t('home.mob') }}</strong>
                    </template>
                  </i18n-t>
                </p>
                <v-divider class="my-6"></v-divider>
                <p class="text-body-1 text-justify">
                  {{ $t('home.aboutText2') }}
                </p>
              </v-card-text>
            </v-card>

            <v-row class="mt-8">
              <v-col
                v-for="feature in features"
                :key="feature.title"
                cols="12"
                md="6"
                lg="4"
              >
                <v-card
                  class="feature-card h-100"
                  elevation="6"
                  rounded="xl"
                >
                  <v-card-text class="pa-6 text-center">
                    <div class="feature-icon mb-4">
                      <v-icon
                        :icon="feature.icon"
                        :color="feature.color"
                        size="56"
                      ></v-icon>
                    </div>
                    <h3 class="text-h6 font-weight-bold mb-3">
                      {{ feature.title }}
                    </h3>
                    <p class="text-body-2 text-grey-darken-1">
                      {{ feature.description }}
                    </p>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </section>

      <!-- Technical Architecture Section -->
      <section class="section py-16 technical-section">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="text-center mb-12">
              <h2 class="section-title">
                {{ $t('home.techTitle') }}
              </h2>
              <p class="section-subtitle">
                {{ $t('home.techSubtitle') }}
              </p>
            </div>

            <!-- Backend Stack -->
            <v-card
              class="tech-card mb-6"
              elevation="8"
              rounded="xl"
            >
              <v-card-title class="pa-6 pb-4">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-server" size="32" color="primary" class="mr-3"></v-icon>
                  <span class="text-h5 font-weight-bold">{{ $t('home.backend') }}</span>
                </div>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-6">
                <v-row>
                  <v-col
                    v-for="tech in backendStack"
                    :key="tech.name"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <div class="tech-item">
                      <v-icon :icon="tech.icon" size="24" :color="tech.color" class="mr-2"></v-icon>
                      <div>
                        <div class="font-weight-bold">{{ tech.name }}</div>
                        <div class="text-caption text-grey">{{ tech.version }}</div>
                      </div>
                    </div>
                  </v-col>
                </v-row>
                <v-divider class="my-6"></v-divider>
                <div class="mt-4">
                  <h4 class="text-h6 font-weight-bold mb-3">{{ $t('home.practices') }}</h4>
                  <v-chip-group>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-code-tags">DDD</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-test-tube">TDD</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-shield-check">PSR-12</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-chart-line">PHPStan Level 8</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-api">OpenAPI 3.1</v-chip>
                  </v-chip-group>
                </div>
              </v-card-text>
            </v-card>

            <!-- Frontend Stack -->
            <v-card
              class="tech-card mb-6"
              elevation="8"
              rounded="xl"
            >
              <v-card-title class="pa-6 pb-4">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-monitor" size="32" color="secondary" class="mr-3"></v-icon>
                  <span class="text-h5 font-weight-bold">{{ $t('home.frontend') }}</span>
                </div>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-6">
                <v-row>
                  <v-col
                    v-for="tech in frontendStack"
                    :key="tech.name"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <div class="tech-item">
                      <v-icon :icon="tech.icon" size="24" :color="tech.color" class="mr-2"></v-icon>
                      <div>
                        <div class="font-weight-bold">{{ tech.name }}</div>
                        <div class="text-caption text-grey">{{ tech.version }}</div>
                      </div>
                    </div>
                  </v-col>
                </v-row>
                <v-divider class="my-6"></v-divider>
                <div class="mt-4">
                  <h4 class="text-h6 font-weight-bold mb-3">{{ $t('home.practices') }}</h4>
                  <v-chip-group>
                    <v-chip color="secondary" variant="tonal" prepend-icon="mdi-language-typescript">TypeScript Strict</v-chip>
                    <v-chip color="secondary" variant="tonal" prepend-icon="mdi-test-tube">Vitest</v-chip>
                    <v-chip color="secondary" variant="tonal" prepend-icon="mdi-code-braces">ESLint + Prettier</v-chip>
                    <v-chip color="secondary" variant="tonal" prepend-icon="mdi-palette">Material Design</v-chip>
                    <v-chip color="secondary" variant="tonal" prepend-icon="mdi-responsive">Responsive Design</v-chip>
                  </v-chip-group>
                </div>
              </v-card-text>
            </v-card>

            <!-- Infrastructure -->
            <v-card
              class="tech-card mb-6"
              elevation="8"
              rounded="xl"
            >
              <v-card-title class="pa-6 pb-4">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-docker" size="32" color="primary" class="mr-3"></v-icon>
                  <span class="text-h5 font-weight-bold">{{ $t('home.infrastructure') }}</span>
                </div>
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text class="pa-6">
                <v-row>
                  <v-col
                    v-for="tech in infrastructureStack"
                    :key="tech.name"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <div class="tech-item">
                      <v-icon :icon="tech.icon" size="24" :color="tech.color" class="mr-2"></v-icon>
                      <div>
                        <div class="font-weight-bold">{{ tech.name }}</div>
                        <div class="text-caption text-grey">{{ tech.version }}</div>
                      </div>
                    </div>
                  </v-col>
                </v-row>
                <v-divider class="my-6"></v-divider>
                <div class="mt-4">
                  <h4 class="text-h6 font-weight-bold mb-3">{{ $t('home.cicd') }}</h4>
                  <v-chip-group>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-github">GitHub Actions</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-shield-alert">Trivy Security</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-chart-box">Coverage ≥80%</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-auto-fix">Linting Automatique</v-chip>
                    <v-chip color="primary" variant="tonal" prepend-icon="mdi-docker">Docker Compose</v-chip>
                  </v-chip-group>
                </div>
              </v-card-text>
            </v-card>

            <!-- Architecture Highlights -->
            <v-row class="mt-8">
              <v-col
                v-for="highlight in architectureHighlights"
                :key="highlight.title"
                cols="12"
                md="6"
              >
                <v-card
                  class="highlight-card h-100"
                  elevation="6"
                  rounded="xl"
                >
                  <v-card-text class="pa-6">
                    <div class="d-flex align-center mb-4">
                      <v-icon
                        :icon="highlight.icon"
                        :color="highlight.color"
                        size="40"
                        class="mr-3"
                      ></v-icon>
                      <h3 class="text-h6 font-weight-bold">
                        {{ highlight.title }}
                      </h3>
                    </div>
                    <p class="text-body-2 text-grey-darken-1">
                      {{ highlight.description }}
                    </p>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </section>

      <!-- MOB Section -->
      <section class="section py-16 mob-section">
        <v-row justify="center">
          <v-col cols="12" lg="10" xl="8">
            <v-card
              class="mob-card"
              elevation="16"
              rounded="xl"
            >
              <!-- Header Section with Logo -->
              <div class="mob-card-header">
                <div class="mob-header-content">
                  <!-- Logo Section -->
                  <div class="mob-logo-section">
                    <a
                      href="https://www.mob.ch/fr/"
                      target="_blank"
                      rel="noopener"
                      class="mob-logo-wrapper"
                      aria-label="MOB - Montreux Oberland Bernois Website"
                    >
                      <div class="mob-logo-container">
                        <img
                          src="/logo/logo-mob.svg"
                          alt="MOB - Montreux Oberland Bernois Logo"
                          class="mob-logo-image"
                        />
                        <div class="mob-logo-glow"></div>
                      </div>
                      <div class="mob-logo-badge">
                        <v-icon icon="mdi-open-in-new" size="14" color="white"></v-icon>
                      </div>
                    </a>
                  </div>

                  <!-- Title and Info Section -->
                  <div class="mob-title-section">
                    <div class="mob-title-wrapper">
                      <h2 class="mob-title">
                        {{ $t('home.mobTitle') }}
                      </h2>
                      <v-chip
                        size="small"
                        color="primary"
                        variant="tonal"
                        class="mob-badge"
                      >
                        <v-icon icon="mdi-train" size="14" class="mr-1"></v-icon>
                        {{ $t('home.challenge') }}
                      </v-chip>
                    </div>
                    <p class="mob-subtitle">
                      {{ $t('home.mobSubtitle') }}
                    </p>
                    <div class="mob-info-row">
                      <div class="mob-info-item">
                        <v-icon icon="mdi-map-marker" size="16" color="primary" class="mr-1"></v-icon>
                        <span>Suisse</span>
                      </div>
                      <v-divider vertical class="mx-2"></v-divider>
                      <div class="mob-info-item">
                        <v-icon icon="mdi-calendar" size="16" color="secondary" class="mr-1"></v-icon>
                        <span>{{ new Date().getFullYear() }}</span>
                      </div>
                      <v-divider vertical class="mx-2"></v-divider>
                      <div class="mob-info-item">
                        <v-icon icon="mdi-code-tags" size="16" color="primary" class="mr-1"></v-icon>
                        <span>Full Stack</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <v-divider class="mob-divider"></v-divider>

              <!-- Content Section -->
              <v-card-text class="mob-card-content">
                <div class="mob-text-content">
                  <p class="mob-text">
                    <i18n-t keypath="home.mobText1" tag="span">
                      <template #challenge>
                        <strong class="highlight-text">{{ $t('home.challenge') }}</strong>
                      </template>
                      <template #mob>
                        <strong class="highlight-text">{{ $t('home.mob') }}</strong>
                      </template>
                    </i18n-t>
                  </p>
                  <p class="mob-text mt-4">
                    {{ $t('home.mobText2') }}
                  </p>
                </div>

                <v-divider class="mob-divider my-8"></v-divider>

                <!-- Footer Section -->
                <div class="mob-footer">
                  <div class="mob-footer-item">
                    <span class="mob-footer-text">
                      <i18n-t keypath="home.developedBy" tag="span">
                        <template #name>
                          <strong class="developer-name">ESTABLET Teddy</strong>
                        </template>
                      </i18n-t>
                    </span>
                  </div>
                  <div class="mob-footer-item mt-3">
                    <span class="mob-footer-text">
                        <v-btn
                            href="https://www.amcnumerique.fr"
                            target="_blank"
                            rel="noopener"
                            color="primary"
                            size="small"
                            class="ml-2 company-btn"
                            variant="outlined"
                            rounded="lg"
                          >
                            AMC Numérique
                        </v-btn>
                    </span>
                  </div>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </section>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const features = computed(() => [
  {
    title: t('home.features.routeCalculation.title'),
    description: t('home.features.routeCalculation.description'),
    icon: 'mdi-map',
    color: 'primary',
  },
  {
    title: t('home.features.analytics.title'),
    description: t('home.features.analytics.description'),
    icon: 'mdi-chart-bar',
    color: 'primary',
  },
  {
    title: t('home.features.interface.title'),
    description: t('home.features.interface.description'),
    icon: 'mdi-palette',
    color: 'secondary',
  },
  {
    title: t('home.features.api.title'),
    description: t('home.features.api.description'),
    icon: 'mdi-api',
    color: 'primary',
  },
  {
    title: t('home.features.auth.title'),
    description: t('home.features.auth.description'),
    icon: 'mdi-shield-lock',
    color: 'secondary',
  },
  {
    title: t('home.features.docker.title'),
    description: t('home.features.docker.description'),
    icon: 'mdi-docker',
    color: 'primary',
  },
]);

const backendStack = ref([
  { name: 'PHP', version: '8.4', icon: 'mdi-language-php', color: 'primary' },
  { name: 'Symfony', version: '7.1', icon: 'mdi-symfony', color: 'primary' },
  { name: 'PostgreSQL', version: '16', icon: 'mdi-database', color: 'primary' },
  { name: 'Doctrine ORM', version: '3', icon: 'mdi-database-search', color: 'primary' },
  { name: 'JWT Auth', version: 'Lexik', icon: 'mdi-key', color: 'primary' },
  { name: 'PHPUnit', version: '11', icon: 'mdi-test-tube', color: 'primary' },
]);

const frontendStack = ref([
  { name: 'Vue.js', version: '3', icon: 'mdi-vuejs', color: 'secondary' },
  { name: 'TypeScript', version: '5', icon: 'mdi-language-typescript', color: 'secondary' },
  { name: 'Vuetify', version: '3', icon: 'mdi-material-design', color: 'secondary' },
  { name: 'Pinia', version: 'Latest', icon: 'mdi-store', color: 'secondary' },
  { name: 'Chart.js', version: 'Latest', icon: 'mdi-chart-line', color: 'secondary' },
  { name: 'Vitest', version: 'Latest', icon: 'mdi-test-tube', color: 'secondary' },
]);

const infrastructureStack = ref([
  { name: 'Docker', version: '25+', icon: 'mdi-docker', color: 'primary' },
  { name: 'Docker Compose', version: 'Latest', icon: 'mdi-docker', color: 'primary' },
  { name: 'Nginx', version: 'Latest', icon: 'mdi-server-network', color: 'primary' },
  { name: 'GitHub Actions', version: 'CI/CD', icon: 'mdi-github', color: 'primary' },
  { name: 'Trivy', version: 'Security', icon: 'mdi-shield-alert', color: 'primary' },
  { name: 'PHPStan', version: 'Level 8', icon: 'mdi-code-tags', color: 'primary' },
]);

const architectureHighlights = computed(() => [
  {
    title: t('home.architecture.ddd.title'),
    description: t('home.architecture.ddd.description'),
    icon: 'mdi-sitemap',
    color: 'primary',
  },
  {
    title: t('home.architecture.tdd.title'),
    description: t('home.architecture.tdd.description'),
    icon: 'mdi-test-tube',
    color: 'secondary',
  },
  {
    title: t('home.architecture.cleanCode.title'),
    description: t('home.architecture.cleanCode.description'),
    icon: 'mdi-code-braces',
    color: 'primary',
  },
  {
    title: t('home.architecture.cicd.title'),
    description: t('home.architecture.cicd.description'),
    icon: 'mdi-source-branch',
    color: 'secondary',
  },
]);
</script>

<style scoped>
.home-view {
  position: relative;
  z-index: 1;
}

.hero-section {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
}

.hero-title-main {
  font-size: 64px;
  font-weight: 900;
  line-height: 1.1;
  margin-bottom: 24px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -1px;
  position: relative;
  display: inline-block;
}

.hero-title-main::after {
  content: '';
  position: absolute;
  bottom: -12px;
  left: 50%;
  transform: translateX(-50%);
  width: 120px;
  height: 5px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 100%);
  border-radius: 3px;
}

.hero-subtitle-main {
  font-size: 20px;
  color: #6B7280;
  font-weight: 400;
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.6;
}

.section {
  position: relative;
}

.section-title {
  font-size: 42px;
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

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 100%);
  border-radius: 2px;
}

.section-subtitle {
  font-size: 18px;
  color: #6B7280;
  font-weight: 400;
  margin-top: 16px;
}

.info-card,
.tech-card,
.mob-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.feature-card {
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}

.feature-icon {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 80px;
  height: 80px;
  margin: 0 auto;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(31, 41, 55, 0.1) 100%);
  transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
  transform: scale(1.1);
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.2) 0%, rgba(31, 41, 55, 0.2) 100%);
}

.tech-item {
  display: flex;
  align-items: center;
  padding: 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
  margin-bottom: 8px;
}

.tech-item:hover {
  background: rgba(220, 38, 38, 0.05);
  transform: translateX(4px);
}

.highlight-card {
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.highlight-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.technical-section {
  background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 1) 100%);
}

.mob-section {
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.03) 0%, rgba(31, 41, 55, 0.03) 50%, rgba(220, 38, 38, 0.02) 100%);
  position: relative;
  overflow: hidden;
}

.mob-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(220, 38, 38, 0.05) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.mob-section::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -10%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(31, 41, 55, 0.05) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.mob-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.99) 0%, rgba(255, 255, 255, 0.95) 100%);
  border: 2px solid rgba(220, 38, 38, 0.15);
  backdrop-filter: blur(20px);
  position: relative;
  overflow: hidden;
  transition: all 0.4s ease;
}

.mob-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #DC2626 0%, #1F2937 50%, #DC2626 100%);
  background-size: 200% 100%;
  animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.mob-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
  border-color: rgba(220, 38, 38, 0.25);
}

.mob-card-header {
  padding: 48px 40px 32px;
  position: relative;
}

.mob-header-content {
  display: flex;
  align-items: flex-start;
  gap: 32px;
  max-width: 1000px;
  margin: 0 auto;
}

.mob-logo-section {
  flex-shrink: 0;
}

.mob-logo-wrapper {
  position: relative;
  display: inline-block;
  text-decoration: none;
  transition: all 0.3s ease;
}

.mob-logo-container {
  position: relative;
  padding: 24px;
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.06) 0%, rgba(31, 41, 55, 0.06) 100%);
  border-radius: 24px;
  border: 2px solid rgba(220, 38, 38, 0.12);
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.mob-logo-wrapper:hover .mob-logo-container {
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.12) 0%, rgba(31, 41, 55, 0.12) 100%);
  border-color: rgba(220, 38, 38, 0.25);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(220, 38, 38, 0.15);
}

.mob-logo-image {
  height: auto;
  max-width: 280px;
  width: 100%;
  min-width: 200px;
  object-fit: contain;
  filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.12));
  transition: all 0.3s ease;
  position: relative;
  z-index: 2;
  display: block;
}

.mob-logo-wrapper:hover .mob-logo-image {
  filter: drop-shadow(0 6px 20px rgba(220, 38, 38, 0.25));
  transform: scale(1.02);
}

.mob-logo-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 130%;
  height: 130%;
  background: radial-gradient(circle, rgba(220, 38, 38, 0.2) 0%, transparent 70%);
  border-radius: 50%;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
  pointer-events: none;
}

.mob-logo-wrapper:hover .mob-logo-glow {
  opacity: 1;
  animation: pulseGlow 2s ease-in-out infinite;
}

.mob-logo-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.3s ease;
  z-index: 3;
}

.mob-logo-wrapper:hover .mob-logo-badge {
  opacity: 1;
  transform: scale(1);
}

@keyframes pulseGlow {
  0%, 100% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0.2;
  }
  50% {
    transform: translate(-50%, -50%) scale(1.15);
    opacity: 0.35;
  }
}

.mob-title-section {
  flex: 1;
  text-align: left;
  padding-top: 8px;
}

.mob-title-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.mob-title {
  font-size: 36px;
  font-weight: 900;
  line-height: 1.1;
  margin: 0;
  background: linear-gradient(135deg, #DC2626 0%, #1F2937 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.8px;
}

.mob-badge {
  font-weight: 600;
  letter-spacing: 0.3px;
  height: 28px;
}

.mob-subtitle {
  font-size: 18px;
  color: #6B7280;
  font-weight: 500;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.mob-info-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(220, 38, 38, 0.1);
}

.mob-info-item {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #6B7280;
  font-weight: 500;
}

.mob-divider {
  border-color: rgba(220, 38, 38, 0.15) !important;
  margin: 0 40px;
}

.mob-card-content {
  padding: 30px 40px 40px !important;
}

.mob-text-content {
  max-width: 800px;
  margin: 0 auto;
}

.mob-text {
  font-size: 16px;
  line-height: 1.8;
  color: #374151;
  text-align: justify;
  margin: 0;
}

.highlight-text {
  color: #DC2626;
  font-weight: 700;
}

.mob-footer {
  text-align: center;
  padding-top: 16px;
}

.mob-footer-item {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
}

.mob-footer-text {
  font-size: 14px;
  color: #6B7280;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  justify-content: center;
}

.developer-name {
  color: #DC2626;
  font-weight: 700;
  font-size: 15px;
}

.company-btn {
  text-transform: none !important;
  font-weight: 700 !important;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
}

.company-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3) !important;
}

@media (max-width: 960px) {
  .mob-card-header {
    padding: 36px 32px 24px;
  }

  .mob-header-content {
    flex-direction: column;
    align-items: center;
    gap: 24px;
    text-align: center;
  }

  .mob-title-section {
    text-align: center;
    width: 100%;
  }

  .mob-title-wrapper {
    justify-content: center;
  }

  .mob-info-row {
    justify-content: center;
  }

  .mob-card-content {
    padding: 24px 32px 32px !important;
  }

  .mob-title {
    font-size: 30px;
  }

  .mob-subtitle {
    font-size: 16px;
  }

  .mob-logo-image {
    max-width: 260px;
  }

  .mob-logo-container {
    padding: 20px;
  }

  .mob-divider {
    margin: 0 32px;
  }
}

@media (max-width: 600px) {
  .mob-card-header {
    padding: 28px 24px 20px;
  }

  .mob-header-content {
    gap: 20px;
  }

  .mob-card-content {
    padding: 20px 24px 24px !important;
  }

  .mob-title {
    font-size: 26px;
    letter-spacing: -0.5px;
  }

  .mob-subtitle {
    font-size: 15px;
  }

  .mob-logo-image {
    max-width: 200px;
    min-width: 180px;
  }

  .mob-logo-container {
    padding: 16px;
    border-radius: 20px;
  }

  .mob-badge {
    font-size: 11px;
    height: 24px;
  }

  .mob-info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    padding-top: 12px;
  }

  .mob-info-item {
    font-size: 13px;
  }

  .mob-text {
    font-size: 15px;
    text-align: left;
  }

  .mob-divider {
    margin: 0 24px;
  }

  .mob-footer-item {
    flex-direction: column;
    gap: 8px;
  }
}

.fade-in-up {
  animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 960px) {
  .hero-title-main {
    font-size: 48px;
  }

  .hero-subtitle-main {
    font-size: 18px;
  }

  .section-title {
    font-size: 32px;
  }
}

@media (max-width: 600px) {
  .hero-title-main {
    font-size: 36px;
  }

  .hero-subtitle-main {
    font-size: 16px;
  }

  .section-title {
    font-size: 28px;
  }

  .hero-section {
    padding: 40px 0;
    min-height: 50vh;
  }
}
</style>




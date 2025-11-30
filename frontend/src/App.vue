<template>
  <v-app>
    <v-app-bar
      :elevation="2"
      color="surface"
      class="app-bar"
      height="80"
    >
      <template #prepend>
        <router-link to="/" class="logo-link">
          <img
            v-if="logoExists"
            :src="logoPath"
            alt="Train Routing Logo"
            class="logo-image"
            @error="onLogoError"
            @load="onLogoLoad"
          />
          <v-icon
            v-else
            icon="mdi-train"
            size="48"
            color="primary"
            class="logo-fallback"
          ></v-icon>
        </router-link>
      </template>
      
      <v-spacer></v-spacer>

      <!-- Desktop Navigation -->
      <div class="d-none d-md-flex align-center">
        <v-btn
          to="/"
          :active="$route.path === '/'"
          variant="text"
          rounded="lg"
          class="mx-1 nav-btn"
          prepend-icon="mdi-home"
        >
          {{ $t('nav.home') }}
        </v-btn>

        <v-btn
          to="/routes"
          :active="$route.path === '/routes'"
          variant="text"
          rounded="lg"
          class="mx-1 nav-btn"
          prepend-icon="mdi-map"
        >
          {{ $t('nav.routes') }}
        </v-btn>
        
        <v-btn
          to="/stats"
          :active="$route.path === '/stats'"
          variant="text"
          rounded="lg"
          class="mx-1 nav-btn"
          prepend-icon="mdi-chart-line"
        >
          {{ $t('nav.stats') }}
        </v-btn>

        <!-- Language Selector -->
        <v-menu location="bottom">
          <template #activator="{ props }">
            <v-btn
              v-bind="props"
              variant="text"
              rounded="lg"
              class="mx-1 nav-btn language-btn"
              :prepend-icon="localeStore.currentLocale === 'fr' ? 'mdi-flag' : 'mdi-flag-outline'"
            >
              {{ localeStore.currentLocale === 'fr' ? 'FR' : 'EN' }}
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              :active="localeStore.currentLocale === 'fr'"
              @click="switchLocale('fr')"
            >
              <template #prepend>
                <v-icon icon="mdi-flag" class="mr-2"></v-icon>
              </template>
              <v-list-item-title>Français</v-list-item-title>
            </v-list-item>
            <v-list-item
              :active="localeStore.currentLocale === 'en'"
              @click="switchLocale('en')"
            >
              <template #prepend>
                <v-icon icon="mdi-flag-outline" class="mr-2"></v-icon>
              </template>
              <v-list-item-title>English</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <!-- Mobile Menu Button -->
      <div class="d-md-none d-flex align-center">
        <!-- Language Selector Mobile -->
        <v-menu location="bottom">
          <template #activator="{ props }">
            <v-btn
              v-bind="props"
              icon
              variant="text"
              class="mr-2"
              size="small"
            >
              <v-icon>{{ localeStore.currentLocale === 'fr' ? 'mdi-flag' : 'mdi-flag-outline' }}</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              :active="localeStore.currentLocale === 'fr'"
              @click="switchLocale('fr')"
            >
              <template #prepend>
                <v-icon icon="mdi-flag" class="mr-2"></v-icon>
              </template>
              <v-list-item-title>Français</v-list-item-title>
            </v-list-item>
            <v-list-item
              :active="localeStore.currentLocale === 'en'"
              @click="switchLocale('en')"
            >
              <template #prepend>
                <v-icon icon="mdi-flag-outline" class="mr-2"></v-icon>
              </template>
              <v-list-item-title>English</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      </div>
    </v-app-bar>

    <!-- Mobile Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      location="right"
      class="mobile-drawer"
    >
      <v-list nav density="comfortable">
        <v-list-item
          to="/"
          :active="$route.path === '/'"
          prepend-icon="mdi-home"
          :title="$t('nav.home')"
          @click="drawer = false"
        ></v-list-item>
        <v-list-item
          to="/routes"
          :active="$route.path === '/routes'"
          prepend-icon="mdi-map"
          :title="$t('nav.routes')"
          @click="drawer = false"
        ></v-list-item>
        <v-list-item
          to="/stats"
          :active="$route.path === '/stats'"
          prepend-icon="mdi-chart-line"
          :title="$t('nav.stats')"
          @click="drawer = false"
        ></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="main-content">
      <div class="background-pattern"></div>
      <router-view v-slot="{ Component }">
        <v-fade-transition>
          <component :is="Component" />
        </v-fade-transition>
      </router-view>
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useLocaleStore } from './stores/localeStore';

const $route = useRoute();
const { locale } = useI18n();
const localeStore = useLocaleStore();

const logoExists = ref(true);
const logoPath = ref('/logo/logo.png');
const drawer = ref(false);

// Sync i18n locale with store
watch(() => localeStore.currentLocale, (newLocale) => {
  locale.value = newLocale;
}, { immediate: true });

const switchLocale = (lang: string) => {
  localeStore.setLocale(lang);
  locale.value = lang;
};

const onLogoError = () => {
  console.warn('Logo failed to load:', logoPath.value);
  logoExists.value = false;
  if (logoPath.value === '/logo/logo.png') {
    logoPath.value = 'http://localhost/logo/logo.png';
  }
};

const onLogoLoad = () => {
  logoExists.value = true;
  console.log('Logo loaded successfully');
};
</script>

<style scoped>
.app-bar {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.98) !important;
  border-bottom: 1px solid rgba(220, 38, 38, 0.1);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
  padding: 0 16px;
}

.logo-link {
  text-decoration: none;
  display: flex;
  align-items: center;
  margin-right: 32px;
  transition: opacity 0.2s ease;
}

.logo-link:hover {
  opacity: 0.85;
}

.logo-image {
  height: 64px;
  width: auto;
  max-width: 300px;
  min-width: 150px;
  object-fit: contain;
  display: block;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
  transition: transform 0.2s ease;
}

.logo-link:hover .logo-image {
  transform: scale(1.02);
}

.logo-fallback {
  filter: drop-shadow(0 2px 4px rgba(220, 38, 38, 0.2));
}

.nav-btn {
  color: #1F2937 !important;
  font-weight: 500;
  font-size: 15px;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
  padding: 8px 16px;
}

.nav-btn:hover {
  background: rgba(220, 38, 38, 0.08) !important;
  color: #DC2626 !important;
  transform: translateY(-1px);
}

.nav-btn.v-btn--active {
  background: rgba(220, 38, 38, 0.12) !important;
  color: #DC2626 !important;
  font-weight: 600;
}

.mobile-drawer {
  z-index: 2000;
}

.mobile-drawer :deep(.v-list-item--active) {
  background: rgba(220, 38, 38, 0.1);
  color: #DC2626;
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .app-bar {
    padding: 0 12px;
  }

  .logo-image {
    height: 56px;
    max-width: 250px;
    min-width: 120px;
  }

  .logo-link {
    margin-right: 16px;
  }
}

@media (max-width: 600px) {
  .app-bar {
    height: 70px;
    padding: 0 8px;
  }

  .logo-image {
    height: 48px;
    max-width: 200px;
    min-width: 100px;
  }

  .logo-link {
    margin-right: 12px;
  }
}

.main-content {
  position: relative;
  min-height: 100vh;
  background: #FFFFFF;
}

.background-pattern {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 50%, rgba(220, 38, 38, 0.03) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(31, 41, 55, 0.03) 0%, transparent 50%),
    linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(249, 250, 251, 1) 100%);
  z-index: 0;
  pointer-events: none;
}
</style>

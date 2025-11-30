import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import RouteView from '@/views/RouteView.vue';
import StatsView from '@/views/StatsView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL || '/projets/train-routing-system/'),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/routes',
      name: 'routes',
      component: RouteView,
    },
    {
      path: '/stats',
      name: 'stats',
      component: StatsView,
    },
  ],
});

export default router;




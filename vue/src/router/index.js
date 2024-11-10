import { createRouter, createWebHistory } from 'vue-router';
import store from '@/store';

import {
  userRoutes,
  authRoutes,
  productRoutes,
  brandRoutes,
  attributeRoutes,
  widgetRoutes,
  voucherRoutes,
  orderRoutes,
  chatRoutes,
  postRoutes,
  flashSaleRoutes,
  evaluateRoutes,
  reportRoutes,
  sliderRoutes
} from './backend';

import { isLoggedIn } from '@/middlewares/authenticate';
// import { isAdmin } from '@/middlewares/authorization';

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/components/backend/layouts/NotFound.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/backend/DashboardView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/setting',
    name: 'setting',
    component: () => import('@/views/backend/setting/IndexVue.vue'),
    beforeEnter: [isLoggedIn]
  },
  ...userRoutes,
  ...authRoutes,
  ...productRoutes,
  ...brandRoutes,
  ...productRoutes,
  ...attributeRoutes,
  ...widgetRoutes,
  ...voucherRoutes,
  ...orderRoutes,
  ...postRoutes,
  ...chatRoutes,
  ...flashSaleRoutes,
  ...evaluateRoutes,
  ...reportRoutes,
  ...sliderRoutes
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  store.dispatch('loadingStore/startLoading');
  next();
});

router.afterEach(() => {
  store.dispatch('loadingStore/stopLoading');
});

export default router;

import store from '@/store';
import { createRouter, createWebHistory } from 'vue-router';

import {
  attributeRoutes,
  authRoutes,
  brandRoutes,
  chatRoutes,
  evaluateRoutes,
  flashSaleRoutes,
  orderRoutes,
  postRoutes,
  productRoutes,
  prohibitedWordRoutes,
  reportRoutes,
  sliderRoutes,
  userRoutes,
  voucherRoutes,
  widgetRoutes
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
  ...sliderRoutes,
  ...prohibitedWordRoutes
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});
export default router;

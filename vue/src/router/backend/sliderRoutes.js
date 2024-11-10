import { isLoggedIn } from '@/middlewares/authenticate';

const sliderRoutes = [
  {
    path: '/slider',
    name: 'slider.index',
    component: () => import('@/views/backend/slider/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/slider/create',
    name: 'slider.create',
    component: () => import('@/views/backend/slider/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/slider/update/:id(\\d+)',
    name: 'slider.update',
    component: () => import('@/views/backend/slider/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default sliderRoutes;

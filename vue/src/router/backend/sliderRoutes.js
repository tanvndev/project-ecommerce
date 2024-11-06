import { isLoggedIn } from '@/middlewares/authenticate';

const sliderRoutes = [
  {
    path: '/slider',
    name: 'slider.index',
    component: () => import('@/views/backend/slider/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
];

export default sliderRoutes;


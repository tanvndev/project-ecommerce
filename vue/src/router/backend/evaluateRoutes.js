import { isLoggedIn } from '@/middlewares/authenticate';

const evaluateRoutes = [
  {
    path: '/evaluate',
    name: 'evaluate.index',
    component: () => import('@/views/backend/evaluate/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/evaluate/replies/:id(\\d+)',
    name: 'evaluate.replies',
    component: () => import('@/views/backend/evaluate/UpdateView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default evaluateRoutes;

import { isLoggedIn } from '@/middlewares/authenticate';

const prohibitedWordRoutes = [
  {
    path: '/prohibited-word/index',
    name: 'prohibited-word.index',
    component: () => import('@/views/backend/prohibited-word/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
];

export default prohibitedWordRoutes;

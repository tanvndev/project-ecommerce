import { isLoggedIn } from '@/middlewares/authenticate';

const chatRoutes = [
  {
    path: '/chat/index',
    name: 'chat.index',
    component: () => import('@/views/backend/chat/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default chatRoutes;

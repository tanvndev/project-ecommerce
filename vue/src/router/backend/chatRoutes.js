import { isLoggedIn } from '@/middlewares/authenticate';
import { isAdmin } from '@/middlewares/authorization';

const chatRoutes = [
  {
    path: '/chat/index',
    name: 'chat.index',
    component: () => import('@/views/backend/chat/IndexView.vue'),
    beforeEnter: [isLoggedIn, isAdmin]
  }
];

export default chatRoutes;

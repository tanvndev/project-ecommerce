import { isLoggedIn } from '@/middlewares/authenticate';

const reportRoutes = [
  {
    path: '/report/index',
    name: 'report.index',
    component: () => import('@/views/backend/report/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/sales',
    name: 'report.sales',
    component: () => import('@/views/backend/report/SalesView.vue'),
    beforeEnter: [isLoggedIn]
  },
];

export default reportRoutes;

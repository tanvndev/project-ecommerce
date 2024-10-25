import { isLoggedIn } from '@/middlewares/authenticate';

const flashSaleRoutes = [
  {
    path: '/flash-sale/index',
    name: 'flash-sale.index',
    component: () => import('@/views/backend/flash-sale/indexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/flash-sale/create',
    name: 'flash-sale.create',
    component: () => import('@/views/backend/flash-sale/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/flash-sale/update/:id(\\d+)',
    name: 'flash-sale.update',
    component: () => import('@/views/backend/flash-sale/UpdateView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default flashSaleRoutes;

import { isLoggedIn } from '@/middlewares/authenticate';

const reportRoutes = [
  {
    path: '/report/index',
    name: 'report.index',
    component: () => import('@/views/backend/report/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/product/index',
    name: 'report.product.index',
    component: () => import('@/views/backend/report/ProductReport.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/revenue/index',
    name: 'report.revenue.index',
    component: () => import('@/views/backend/report/RevenueReport.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/user/index',
    name: 'report.user.index',
    component: () => import('@/views/backend/report/UserReport.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/sales',
    name: 'report.sales',
    component: () => import('@/views/backend/report/SalesView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/top-product',
    name: 'report.top.product',
    component: () => import('@/views/backend/report/TopProductView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/top-popular-product',
    name: 'report.top.popular.product',
    component: () => import('@/views/backend/report/TopPopularProductView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/top-search-history',
    name: 'report.top.search.history',
    component: () => import('@/views/backend/report/TopSearchHistoryView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/report/top-loyal-customer',
    name: 'report.top.loyal.customer',
    component: () => import('@/views/backend/report/TopLoyalCustomerView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default reportRoutes;

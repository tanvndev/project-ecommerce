import { isLoggedIn } from '@/middlewares/authenticate';

const userRoutes = [
  {
    path: '/user/index',
    name: 'user.index',
    component: () => import('@/views/backend/user/user/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/staff/index',
    name: 'user.staff.index',
    component: () => import('@/views/backend/user/user/StaffIndex.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/admin/index',
    name: 'user.admin.index',
    component: () => import('@/views/backend/user/user/AdminIndex.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/store',
    name: 'user.store',
    component: () => import('@/views/backend/user/user/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/update/:id(\\d+)',
    name: 'user.update',
    component: () => import('@/views/backend/user/user/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/catalogue/index',
    name: 'user.catalogue.index',
    component: () => import('@/views/backend/user/catalogue/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/catalogue/store',
    name: 'user.catalogue.store',
    component: () => import('@/views/backend/user/catalogue/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/catalogue/update/:id(\\d+)',
    name: 'user.catalogue.update',
    component: () => import('@/views/backend/user/catalogue/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/user/catalogue/permission',
    name: 'user.catalogue.permission',
    component: () => import('@/views/backend/user/catalogue/PermissionVue.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/permission/index',
    name: 'permission.index',
    component: () => import('@/views/backend/user/permission/IndexView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/permission/store',
    name: 'permission.store',
    component: () => import('@/views/backend/user/permission/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  },
  {
    path: '/permission/update/:id(\\d+)',
    name: 'permission.update',
    component: () => import('@/views/backend/user/permission/StoreView.vue'),
    beforeEnter: [isLoggedIn]
  }
];

export default userRoutes;

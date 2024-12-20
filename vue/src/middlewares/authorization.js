import store from '@/store';

const isAdmin = (to, from, next) => {
  const role = store.getters['authStore/getRole'];
  console.log(role);

  const roleNames = ['admin', 'staff'];
//   if (!role || !roleNames.includes(role)) {
//     next({ name: 'forbidden' });
//   }
  next();
};

export { isAdmin };

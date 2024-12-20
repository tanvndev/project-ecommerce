const isAdmin = (to, from, next) => {
  const role = sessionStorage.getItem('role');

  const roleNames = ['admin', 'staff'];
  if (!role || !roleNames.includes(role)) {
    next({ name: 'forbidden' });
  }
  next();
};

export { isAdmin };

const sidebar = [
  {
    id: 'dashboard_sidebar',
    icon: 'fas fa-home-lg-alt',
    name: 'Dashboard',
    route: 'dashboard',
    subMenu: []
  },

  {
    id: 'order_sidebar',
    icon: 'fas fa-pallet-alt',
    name: 'Đơn hàng',
    active: ['order'],
    subMenu: [
      {
        name: 'Danh sách đơn hàng',
        route: 'order.index'
      }
    ]
  },
  {
    id: 'user_sidebar',
    icon: 'fas fa-users-medical',
    name: 'Thành viên',
    active: ['user'],
    subMenu: [
      {
        name: 'Danh sách thành viên',
        route: 'user.index'
      },
      {
        name: 'Danh sách khách hàng',
        route: 'user.index'
      },
      {
        name: 'Nhóm thành viên',
        route: 'user.catalogue.index'
      },
      {
        name: 'Quyền thành viên',
        route: 'permission.index'
      }
    ]
  },
  {
    id: 'attribute_sidebar',
    icon: 'fab fa-buffer',
    name: 'Thuộc tính',
    active: ['attribute'],
    subMenu: [
      {
        name: 'Danh sách thuộc tính',
        route: 'attribute.index'
      },
      {
        name: 'Danh sách thương hiệu',
        route: 'brand.index'
      },
      {
        name: 'Danh sách sản phẩm',
        route: 'product.index'
      },
      {
        name: 'Nhóm sản phẩm',
        route: 'product.catalogue.index'
      }
    ]
  }
];
export default sidebar;

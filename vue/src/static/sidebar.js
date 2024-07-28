const sidebar = [
  {
    id: 'dashboard_sidebar',
    icon: 'fas fa-home-lg-alt',
    name: 'Dashboard',
    route: 'dashboard',
    subMenu: []
  },
  {
    id: 'user_sidebar',
    icon: 'fas fa-users-medical',
    name: 'QL Thành viên',
    route: '',
    subMenu: [
      {
        name: 'Quản lý thành viên',
        route: 'user.index'
      },
      {
        name: 'Quản lý nhóm thành viên',
        route: 'user.catalogue.index'
      },
      {
        name: 'Quản lý quyền',
        route: 'permission.index'
      }
    ]
  },
  {
    id: 'brand_sidebar',
    icon: 'fas fa-windsock',
    name: 'QL Thương hiệu',
    route: '',
    subMenu: [
      {
        name: 'Quản lý thương hiệu',
        route: 'brand.index'
      }
    ]
  },
  {
    id: 'supplier_sidebar',
    icon: 'fas fa-parachute-box',
    name: 'QL Nhà cung cấp',
    route: '',
    subMenu: [
      {
        name: 'Quản lý nhà cung cấp',
        route: 'supplier.index'
      }
    ]
  },
  {
    id: 'warehouse_sidebar',
    icon: 'fas fa-warehouse-alt',
    name: 'QL kho hàng',
    route: '',
    subMenu: [
      {
        name: 'Quản lý kho hàng',
        route: 'warehouse.index'
      }
    ]
  },
  {
    id: 'product_sidebar',
    icon: 'fas fa-box-check',
    name: 'QL Sản phẩm',
    route: '',
    subMenu: [
      {
        name: 'Quản lý sản phẩm',
        route: 'product.index'
      },
      {
        name: 'Quản lý nhóm sản phẩm',
        route: 'product.catalogue.index'
      }
    ]
  }
];
export default sidebar;

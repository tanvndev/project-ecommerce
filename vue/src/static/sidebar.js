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
      },
      {
        name: 'Tạo mới đơn hàng',
        route: 'order.store'
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
    id: 'product_sidebar',
    icon: 'fas fa-box-check',
    name: 'Sản phẩm',
    active: ['product', 'brand', 'attribute'],
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
  },
  {
    id: 'live_chat_sidebar',
    icon: 'fas fa-comments',
    name: 'Tin nhắn',
    route: 'chat.index',
    subMenu: []
  },
  {
    id: 'marketing_sidebar',
    icon: 'fas fa-bullhorn',
    name: 'Marketing',
    active: ['voucher', 'widget'],
    subMenu: [
      {
        name: 'Danh sách khuyến mại',
        route: 'voucher.index'
      },
      {
        name: 'Danh sách widget',
        route: 'widget.index'
      }
    ]
  },
  {
    id: 'post_sidebar',
    icon: 'fas fa-newspaper',
    name: 'Bài viết',
    active: ['post'],
    subMenu: [
      {
        name: 'Danh sách bài viết',
        route: 'post.index'
      },
      {
        name: 'Nhóm bài viết',
        route: 'post.catalogue.index'
      }
    ]
  }
];
export default sidebar;

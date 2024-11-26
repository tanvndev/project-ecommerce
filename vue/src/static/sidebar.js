const sidebar = [
  {
    id: 'dashboard_sidebar',
    icon: 'fas fa-home-lg-alt',
    name: 'Tổng quan',
    route: 'dashboard',
    subMenu: []
  },
  {
    id: 'report_sidebar',
    icon: 'fas fa-chart-bar',
    name: 'Báo cáo',
    route: 'report.index',
    subMenu: []
  },

  {
    id: 'live_chat_sidebar',
    icon: 'fas fa-comments',
    name: 'Tin nhắn',
    route: 'chat.index',
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
      },
      {
        name: 'Danh sách yêu cầu',
        route: 'order.change-status-request'
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
      },
      {
        name: 'Đánh giá sản phẩm',
        route: 'evaluate.index'
      }
    ]
  },

  {
    id: 'marketing_sidebar',
    icon: 'fas fa-bullhorn',
    name: 'Marketing',
    active: ['voucher', 'widget', 'flash-sale', 'slider'],
    subMenu: [
      {
        name: 'Danh sách khuyến mại',
        route: 'voucher.index'
      },
      {
        name: 'Danh sách widget',
        route: 'widget.index'
      },
      {
        name: 'Danh sách Flash sale',
        route: 'flash-sale.index'
      },
      {
        name: 'Tạo mới Flash sale',
        route: 'flash-sale.create'
      },
      {
        name: 'Danh sách slider',
        route: 'slider.index'
      },
      {
        name: 'Danh sách từ ngữ khiếm nhã',
        route: 'prohibited-word.index'
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

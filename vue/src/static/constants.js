const LARAVEL_URL = import.meta.env.VITE_LARAVEL_URL;
const NUXT_URL = import.meta.env.VITE_NUXT_URL;

const ROLE_ADMIN = 1;
const ROLE_USER = 2;
const ROLE_STAFF = 3;

const PUBLISH = [
  {
    value: 0,
    label: 'Chọn tình trạng'
  },
  {
    value: 1,
    label: 'Đã xuất bản'
  },
  {
    value: 2,
    label: 'Chưa xuất bản'
  }
];

const EFFECT = [
  {
    value: 'slide',
    label: 'Slide'
  },
  {
    value: 'fade',
    label: 'Fade'
  },
  {
    value: 'cube',
    label: 'Cube'
  },
  {
    value: 'coverflow',
    label: 'Coverflow'
  },
  {
    value: 'flip',
    label: 'Flip'
  }
];

const PAGESIZE = ['10', '20', '30', '50', '100'];

const PRODUCT_TYPE = [
  {
    value: 'simple',
    label: 'Sản phẩm đơn giản'
  },
  {
    value: 'variable',
    label: 'Sản phẩm có biến thể'
  }
];

const TAXT_STATUS = [
  {
    value: 1,
    label: 'Giá sản phẩm đã bao gồm thuế'
  },
  {
    value: 2,
    label: 'Giá sản phẩm chưa bao gồm thuế'
  }
];

const WAREHOUSE_CONFIG = [
  {
    value: '5-10-3-5',
    label: 'Nhỏ (5 dãy, 10 kệ, 3 tầng, 5 khoang)'
  },
  {
    value: '10-20-4-8',
    label: 'Trung bình (10 dãy, 20 kệ, 4 tầng, 8 khoang)'
  },
  {
    value: '20-30-5-10',
    label: 'Lớn (20 dãy, 30 kệ, 5 tầng, 10 khoang)'
  }
];

const STOCK_STATUS = [
  {
    value: 'instock',
    label: 'Còn hàng'
  },
  {
    value: 'outofstock',
    label: 'Hết hàng'
  }
];

const WIDGET_MODEL = [
  {
    value: 'Product',
    label: 'Sản phẩm'
  },
  {
    value: 'ProductCatalogue',
    label: 'Nhóm sản phẩm'
  },
  {
    value: 'Brand',
    label: 'Thương hiệu'
  }
];

const WIDGET_TYPE = [
  {
    value: 'product',
    label: 'Hiển thị dạng sản phẩm'
  },
  {
    value: 'advertisement',
    label: 'Hiển thị dạng quảng cáo'
  }
];

const DISCOUNT_TYPE = [
  {
    value: 'fixed',
    label: 'Số tiền (₫)'
  },
  {
    value: 'percentage',
    label: 'Phần chăm (%)'
  }
];

const DISCOUNT_CONDITION_APPLY = [
  {
    value: 'all',
    label: 'Không có'
  },
  {
    value: 'subtotal_price',
    label: 'Tổng giá trị đơn hàng tối thiểu'
  },
  {
    value: 'min_quantity',
    label: 'Tổng số lượng sản phẩm được khuyến mại tối thiểu'
  }
];

const INDUSTRY = [
  {
    value: 'Thiết bị điện tử',
    label: 'Thiết bị điện tử'
  },
  {
    value: 'Thiết bị công nghệ',
    label: 'Thiết bị công nghệ'
  },
  {
    value: 'Đồng hồ',
    label: 'Đồng hồ'
  },
  {
    value: 'Giày dép',
    label: 'Giày dép'
  },
  {
    value: 'Mẹ và bé',
    label: 'Mẹ và bé'
  }
];

const TONE_AI = [
  {
    icon: 'fal fa-briefcase',
    value: 'Chuyên nghiệp',
    label: 'Chuyên nghiệp'
  },
  {
    icon: 'fas fa-grin',
    value: 'Vui tươi',
    label: 'Vui tươi'
  },
  {
    icon: 'fas fa-laugh-wink',
    value: 'Hài hước',
    label: 'Hài hước'
  }
];
const TEXT_STYLE_AI = [
  {
    icon: 'far fa-comment-alt-lines',
    value: 'Khoảng 70 từ',
    label: 'Tiêu đề (khoảng 70 từ)'
  },
  {
    icon: 'far fa-comment-alt-lines',
    value: 'Khoảng 500 từ',
    label: 'Tóm tắt (khoảng 500 từ)'
  },
  {
    icon: 'far fa-comment-alt-lines',
    value: 'Khoảng 1000 từ',
    label: 'Tiêu chuẩn (khoảng 1000 từ)'
  }
];
export {
  DISCOUNT_CONDITION_APPLY,
  DISCOUNT_TYPE,
  EFFECT,
  INDUSTRY,
  LARAVEL_URL,
  NUXT_URL,
  PAGESIZE,
  PRODUCT_TYPE,
  PUBLISH,
  ROLE_ADMIN,
  ROLE_STAFF,
  ROLE_USER,
  STOCK_STATUS,
  TAXT_STATUS,
  TEXT_STYLE_AI,
  TONE_AI,
  WAREHOUSE_CONFIG,
  WIDGET_MODEL,
  WIDGET_TYPE
};

const ORDER_STATUS = [
  {
    label: 'Chờ xác nhận',
    value: 'pending'
  },
  {
    label: 'Đang xử lý',
    value: 'processing'
  },
  {
    label: 'Đang giao',
    value: 'delivering'
  },
  {
    label: 'Hoàn thành',
    value: 'completed'
  },
  {
    label: 'Đã hủy',
    value: 'canceled'
  }
  //   {
  //     label: 'Trả hàng',
  //     value: 'returned'
  //   }
];

const PAYMENT_STATUS = [
  {
    value: 'unpaid',
    label: 'Chưa thanh toán'
  },
  {
    value: 'paid',
    label: 'Đã thanh toán'
  }
];

const ORDER_STATUS_SELECT = [
  {
    value: '',
    label: 'Trạng thái đơn hàng'
  },
  {
    label: 'Chờ xác nhận',
    value: 'pending'
  },
  {
    label: 'Đang xử lý',
    value: 'processing'
  },
  {
    label: 'Đang giao',
    value: 'delivering'
  },
  {
    label: 'Hoàn thành',
    value: 'completed'
  },
  {
    label: 'Đã hủy',
    value: 'canceled'
  }
  //   {
  //     label: 'Trả hàng',
  //     value: 'returned'
  //   }
];

const PAYMENT_STATUS_SELECT = [
  {
    value: '',
    label: 'Trạng thái thanh toán'
  },
  {
    value: 'unpaid',
    label: 'Chưa thanh toán'
  },
  {
    value: 'paid',
    label: 'Đã thanh toán'
  }
];

const DELYVERY_STATUS = [
  {
    value: 'pending',
    label: 'Chờ giao'
  },
  {
    value: 'delivering',
    label: 'Đang giao'
  },
  {
    value: 'delivered',
    label: 'Đã giao hàng'
  },
  {
    value: 'failed',
    label: 'Giao thất bại'
  }
];

export {
  DELYVERY_STATUS,
  ORDER_STATUS,
  ORDER_STATUS_SELECT,
  PAYMENT_STATUS,
  PAYMENT_STATUS_SELECT
};

const ORDER_STATUS_TABS = [
  {
    title: 'Tất cả',
    value: 'all',
  },
  {
    title: 'Chờ xác nhận',
    value: 'pending',
  },
  {
    title: 'Đang xử lý',
    value: 'processing',
  },
  {
    title: 'Đang giao',
    value: 'delivering',
  },
  {
    title: 'Hoàn thành',
    value: 'completed',
  },
  {
    title: 'Đã huỷ',
    value: 'canceled',
  },
  {
    title: 'Trả hàng',
    value: 'returned',
  },
]

const ORDER_STATUS = [
  {
    title: 'Chờ xác nhận',
    value: 'pending',
  },
  {
    title: 'Đang xử lý',
    value: 'processing',
  },
  {
    label: 'Đang giao',
    value: 'delivering',
  },
  {
    label: 'Hoàn thành',
    value: 'completed',
  },
  {
    label: 'Đã hủy',
    value: 'canceled',
  },
  {
    label: 'Trả hàng',
    value: 'returned',
  },
]

const PAYMENT_STATUS = [
  {
    value: 'unpaid',
    label: 'Chưa thanh toán',
  },
  {
    value: 'paid',
    label: 'Đã thanh toán',
  },
]

export { ORDER_STATUS_TABS, ORDER_STATUS, PAYMENT_STATUS }

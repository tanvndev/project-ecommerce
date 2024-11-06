const columns = [
  {
    title: 'Tên Flash sale',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name)
  },
  {
    title: 'Ngày bắt đầu',
    dataIndex: 'start_date',
    key: 'start_date',
    sorter: (a, b) => a.start_date.localeCompare(b.start_date),
    width: '15%'
  },
  {
    title: 'Ngày kết thúc',
    dataIndex: 'end_date',
    key: 'end_date',
    sorter: (a, b) => a.end_date.localeCompare(b.end_date),
    width: '15%'
  },
  {
    title: 'Tổng sản phẩm',
    dataIndex: 'total_product',
    key: 'total_product',
    width: '10%'
  },
  {
    title: 'Trạng thái',
    dataIndex: 'status',
    key: 'status',
    width: '8%'
  },
  {
    title: 'Trạng thái',
    dataIndex: 'publish',
    key: 'publish',
    width: '7%'
  }
];

const innerColumns = [
  {
    title: 'Tên biến thể',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name)
  },
  {
    title: 'Số lượng tối đa',
    dataIndex: 'max_quantity',
    key: 'max_quantity',
    sorter: (a, b) => a.max_quantity.localeCompare(b.max_quantity)
  },
  {
    title: 'Số lượng đã mua',
    dataIndex: 'sold_quantity',
    key: 'sold_quantity',
    sorter: (a, b) => a.sold_quantity.localeCompare(b.sold_quantity)
  },
  {
    title: 'Giá khuyến mãi',
    dataIndex: 'sale_price',
    key: 'sale_price'
  }
];

export { columns, innerColumns };

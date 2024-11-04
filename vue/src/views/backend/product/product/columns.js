const columns = [
  {
    title: 'Tên sản phẩm',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Loại sản phẩm',
    dataIndex: 'product_type',
    key: 'product_type'
  },
  {
    title: 'Tổng tồn kho',
    dataIndex: 'total_stock',
    key: 'total_stock'
  },
  {
    title: 'Thương hiệu',
    dataIndex: 'brand_name',
    key: 'brand_name',
    sorter: (a, b) => a.brand_name.localeCompare(b.brand_name)
  },
  {
    title: 'Nhóm sản phẩm',
    dataIndex: 'catalogues',
    key: 'catalogues'
  },
  {
    title: 'Tình trạng',
    dataIndex: 'publish',
    key: 'publish',
    width: '7%'
  }
];

const innerColumns = [
  {
    title: 'Tên sản phẩm',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Giá nhập',
    dataIndex: 'cost_price',
    key: 'cost_price'
  },
  {
    title: 'Giá bán',
    dataIndex: 'price',
    key: 'price'
  },
  {
    title: 'Giá khuyến mãi',
    dataIndex: 'sale_price',
    key: 'sale_price'
  },
  {
    title: 'Tồn kho',
    dataIndex: 'stock',
    key: 'stock'
  },
  {
    title: 'SKU',
    dataIndex: 'sku',
    key: 'sku'
  },
  {
    title: 'Giao hàng',
    dataIndex: 'shipping',
    key: 'shipping'
  }
];

export { columns, innerColumns };

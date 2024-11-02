const columns = [
  {
    title: 'PRODUCT',
    dataIndex: 'product_name',
    key: 'product_name',
    sorter: (a, b) => a.product_name.localeCompare(b.product_name)
  },
  {
    title: 'USER',
    dataIndex: 'fullname',
    key: 'fullname',
    sorter: (a, b) => a.fullname.localeCompare(b.fullname)
  },
  {
    title: 'STAR',
    dataIndex: 'rating',
    key: 'rating',
  },
  {
    title: 'COMMENT',
    dataIndex: 'comment',
    key: 'comment',
    sorter: (a, b) => a.comment.localeCompare(b.comment)
  },
  {
    title: 'IMAGES',
    dataIndex: 'image',
    key: 'image',
  },
  {
    title: 'CREATED AT',
    dataIndex: 'created_at',
    key: 'created_at',
  },

  {
    title: 'PUBLISH',
    dataIndex: 'publish',
    key: 'publish'
  }
];

export { columns };

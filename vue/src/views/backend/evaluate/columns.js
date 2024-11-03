const columns = [
  {
    title: 'Sản phẩm',
    dataIndex: 'product_name',
    key: 'product_name',
    sorter: (a, b) => a.product_name.localeCompare(b.product_name)
  },
  {
    title: 'Người dùng',
    dataIndex: 'fullname',
    key: 'fullname',
    sorter: (a, b) => a.fullname.localeCompare(b.fullname)
  },
  {
    title: 'Đánh giá',
    dataIndex: 'rating',
    key: 'rating'
  },
  {
    title: 'Bình luận',
    dataIndex: 'comment',
    key: 'comment',
    sorter: (a, b) => a.comment.localeCompare(b.comment)
  },
  {
    title: 'Ảnh',
    dataIndex: 'image',
    key: 'image'
  },
  {
    title: 'Thời gian',
    dataIndex: 'created_at',
    key: 'created_at'
  },
  {
    title: 'Phản hồi',
    dataIndex: 'status',
    key: 'status'
  },
  {
    title: 'Trạng thái',
    dataIndex: 'publish',
    key: 'publish'
  }
];

export { columns };

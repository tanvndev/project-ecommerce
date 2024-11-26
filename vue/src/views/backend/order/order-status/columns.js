const columns = [
  {
    title: 'Mã đơn hàng',
    dataIndex: 'code',
    key: 'code',
    sorter: (a, b) => a.code.localeCompare(b.code)

  },
  {
    title: 'Người yêu cầu',
    dataIndex: 'requested_by',
    key: 'requested_by'
  },
  {
    title: 'Trạng thái đơn hàng',
    dataIndex: 'current_status',
    key: 'current_status'
  },
  {
    title: 'Đổi thành',
    dataIndex: 'requested_status',
    key: 'requested_status',
  },
  {
    title: 'Lý do',
    dataIndex: 'reason',
    key: 'reason'
  },

  {
    title: 'Trạng thái yêu cầu',
    dataIndex: 'status',
    key: 'status'
  },
  {
    title: 'Hành động',
    dataIndex: 'action',
    key: 'action'
  }
];

const innerColumns = [
  {
    title: 'Chấp nhận bởi ai',
    dataIndex: 'approved_by',
    key: 'approved_by'
  },
  {
    title: 'Thời gian chấp nhận',
    dataIndex: 'approved_at',
    key: 'approved_at'
  },
  {
    title: 'Lý do từ chối',
    dataIndex: 'rejection_reason',
    key: 'rejection_reason'
  }
];

export { columns, innerColumns };

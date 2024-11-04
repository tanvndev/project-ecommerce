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
    sorter: (a, b) => a.start_date.localeCompare(b.start_date)
  },
  {
    title: 'Ngày kết thúc',
    dataIndex: 'end_date',
    key: 'end_date',
    sorter: (a, b) => a.end_date.localeCompare(b.end_date)
  },
  {
    title: 'Trạng thái',
    dataIndex: 'publish',
    key: 'publish'
  }
];

export { columns };

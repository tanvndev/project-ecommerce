const columns = [
  {
    title: 'Mã',
    dataIndex: 'code',
    key: 'code',
    sorter: (a, b) => a.code.localeCompare(b.code)
  },
  {
    title: 'Tiêu đề',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name)
  },
  {
    title: 'Trạng thái',
    dataIndex: 'publish',
    key: 'publish'
  }
];

export { columns };

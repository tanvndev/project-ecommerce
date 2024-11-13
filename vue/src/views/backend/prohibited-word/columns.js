const columns = [
  {
    title: 'Từ ngữ',
    dataIndex: 'keyword',
    key: 'keyword',
    sorter: (a, b) => a.keyword.localeCompare(b.keyword)
  },
];

export { columns };

import _ from 'lodash'
const formatMessages = (messages) => {
  if (messages && typeof messages === 'string') {
    return [messages]
  }

  const formattedMessages = []
  for (const key in messages) {
    formattedMessages.push(
      Array.isArray(messages[key]) ? messages[key][0] : messages[0]
    )
  }
  return formattedMessages
}

const formatDataToSelect = (data, value = 'id', title = 'name') => {
  const formattedData = []

  if (_.isEmpty(data)) {
    return formattedData
  }

  data.forEach((item) => {
    formattedData.push({ value: item[value], title: item[title] })
  })

  return formattedData
}

const formatDataToRadio = (data, value = 'id', label = 'name') => {
  const formattedData = []

  if (_.isEmpty(data)) {
    return formattedData
  }

  data.forEach((item) => {
    formattedData.push({ label: item[label], value: item[value] })
  })

  return formattedData
}

const formatDataToTreeSelect = (
  data,
  value = 'id',
  label = 'name',
  parentId = 0
) => {
  if (_.isEmpty(data)) {
    return [
      {
        label: 'Root',
        value: 0,
        children: [],
      },
    ]
  }

  const itemMap = new Map()

  data.forEach((item) => {
    itemMap.set(item[value], { ...item, children: [] })
  })

  const tree = []

  data.forEach((item) => {
    if (item.parent_id) {
      const parent = itemMap.get(item.parent_id)
      if (parent) {
        parent.children.push(itemMap.get(item[value]))
      }
    } else {
      tree.push(itemMap.get(item[value]))
    }
  })

  const formatNode = (node) => ({
    label: node[label],
    value: node[value],
    children: node.children.length ? node.children.map(formatNode) : undefined,
  })

  return tree.map(formatNode)
}

const formatTimestampToDate = (timestamp, dateFormat = 'YYYY-MM-DD') => {
  const date = new Date(timestamp * 1000)
  const year = date.getFullYear()
  const month = (date.getMonth() + 1).toString().padStart(2, '0')
  const day = date.getDate().toString().padStart(2, '0')

  // Thực hiện định dạng ngày dựa trên dateFormat
  let formattedDate = dateFormat.replace('YYYY', year)
  formattedDate = formattedDate.replace('MM', month)
  formattedDate = formattedDate.replace('DD', day)

  return formattedDate
}

const formatBytesToKBMB = (bytes) => {
  if (bytes < 1000) {
    return `${bytes} bytes`
  } else if (bytes < 1000000) {
    const kb = (bytes / 1000).toFixed(0)
    return `${kb} KB`
  } else {
    const mb = (bytes / 1000000).toFixed(0)
    return `${mb} MB`
  }
}

const formatCurrency = (amount, currencyCode = 'vn') => {
  amount = parseFloat(amount)

  if (amount === 0) {
    return '0 ₫'
  }
  if (!amount) {
    return '-'
  }

  switch (currencyCode.toUpperCase()) {
    case 'VN':
      return amount.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND',
      })
    case 'CN':
      return amount.toLocaleString('zh-CN', {
        style: 'currency',
        currency: 'CNY',
      })
    case 'EN':
      return amount.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
      })
    default:
      return amount.toString()
  }
}

export {
  formatBytesToKBMB,
  formatCurrency,
  formatDataToRadio,
  formatDataToSelect,
  formatDataToTreeSelect,
  formatMessages,
  formatTimestampToDate,
}

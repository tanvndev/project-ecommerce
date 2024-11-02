import dayjs from 'dayjs'

const toast = (message = '', type = 'success', duration = 3000) => {
  const { $toast } = useNuxtApp()

  $toast(message, {
    theme: 'auto',
    type: type,
    position: 'top-center',
    autoClose: duration,
    hideProgressBar: false,
    dangerouslyHTMLString: true,
  })
}
const debounce = (func, delay) => {
  let timerId
  return (...args) => {
    clearTimeout(timerId)
    timerId = setTimeout(() => {
      func(...args)
    }, delay)
  }
}

const resizeImage = (image, width, height) => {
  if (!image) {
    return image
  }
  const params = []

  if (width) {
    params.push(`w=${width}`)
  }
  if (height) {
    params.push(`h=${height}`)
  }

  if (params.length > 0) {
    const separator = image.includes('?') ? '&' : '?'
    image += separator + params.join('&')
  }

  return image
}

const getBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => resolve(reader.result)
    reader.onerror = (error) => reject(error)
  })
}

const getFileNameFromUrl = (url) => {
  const parts = url.split('/')
  return parts[parts.length - 1]
}

const getFileFromFileList = (fileList) => {
  if (!fileList || fileList.length === 0) {
    return []
  }
  const fileArr = fileList.map((file) => file.url)
  return JSON.stringify(fileArr, null, 2)
}

const getImageToAnt = (images) => {
  if (!images) return []

  // Kiểm tra nếu images là một chuỗi
  if (typeof images === 'string') {
    const fileName = getFileNameFromUrl(images)
    return [
      {
        uid: fileName,
        name: fileName,
        status: 'done',
        url: images,
      },
    ]
  }

  // Kiểm tra nếu images là một mảng
  if (Array.isArray(images)) {
    return images.map((image) => ({
      uid: getFileNameFromUrl(image),
      name: getFileNameFromUrl(image),
      status: 'done',
      url: image,
    }))
  }

  // Trường hợp còn lại, trả về mảng rỗng
  return []
}

const isJSONString = (str) => {
  try {
    JSON.parse(str)
    return true
  } catch (error) {
    return false
  }
}

const cleanedData = (data) => {
  return Object.entries(data).filter(
    ([key, value]) =>
      value != null && value != '' && value != undefined && value != 'null'
  )
}

const generateSlug = (str) => {
  if (!str) return ''
  const specialCharsMap = {
    à: 'a',
    á: 'a',
    ä: 'a',
    â: 'a',
    ã: 'a',
    å: 'a',
    ă: 'a',
    æ: 'ae',
    ą: 'a',
    ç: 'c',
    ć: 'c',
    č: 'c',
    đ: 'd',
    ď: 'd',
    è: 'e',
    é: 'e',
    ě: 'e',
    ė: 'e',
    ë: 'e',
    ê: 'e',
    ę: 'e',
    ğ: 'g',
    ǵ: 'g',
    ḧ: 'h',
    ì: 'i',
    í: 'i',
    ï: 'i',
    î: 'i',
    į: 'i',
    ł: 'l',
    ḿ: 'm',
    ǹ: 'n',
    ń: 'n',
    ň: 'n',
    ñ: 'n',
    ò: 'o',
    ó: 'o',
    ö: 'o',
    ô: 'o',
    œ: 'oe',
    ø: 'o',
    ṕ: 'p',
    ŕ: 'r',
    ř: 'r',
    ß: 'ss',
    ş: 's',
    ś: 's',
    š: 's',
    ș: 's',
    ť: 't',
    ț: 't',
    ù: 'u',
    ú: 'u',
    ü: 'u',
    û: 'u',
    ǘ: 'u',
    ů: 'u',
    ű: 'u',
    ū: 'u',
    ų: 'u',
    ẃ: 'w',
    ẍ: 'x',
    ÿ: 'y',
    ý: 'y',
    ỳ: 'y',
    ỷ: 'y',
    ỹ: 'y',
    ỵ: 'y',
    ź: 'z',
    ž: 'z',
    ż: 'z',
    '·': '-',
    '/': '-',
    _: '-',
    ',': '-',
    ':': '-',
    '&': '-and-',
  }
  return str
    .toString()
    .toLowerCase()
    .replace(/[^\w\s-]/g, (match) => specialCharsMap[match] || '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '')
}

const handleDateChangeToAnt = (dates) => {
  if (!Array.isArray(dates) || dates.length !== 2) {
    return []
  }
  const formattedDates = dates.map((date) => dayjs(date))

  return formattedDates
}

const sortAndConcatenate = (array) => {
  if (!Array.isArray(array)) {
    throw new TypeError('The input must be an array.')
  }

  const filteredArray = array.filter((item) => item !== null)
  const sortedArray = filteredArray.sort((a, b) => a - b)
  return sortedArray.join(',')
}

const getLastPartOfSlug = (slug, separator = '-') => {
  const parts = slug.split(separator)
  const lastPart = parts[parts.length - 1]

  return lastPart
}

const removeLastSegment = (slug, separator = '-') => {
  const parts = slug.split(separator)
  parts.pop()

  return parts.join(separator)
}

const handleSocialIconClick = (event) => {
  const url = location?.href
  const platform = event.currentTarget.getAttribute('data-platform')

  // Copy URL to clipboard
  navigator.clipboard
    .writeText(url)
    .then(() => {
      console.log('URL copied to clipboard:', url)
    })
    .catch((err) => {
      console.error('Failed to copy URL:', err)
    })

  let formattedUrl

  switch (platform) {
    case 'facebook':
      formattedUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
      break

    case 'twitter':
      formattedUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}`
      break

    case 'pinterest':
      formattedUrl = `https://www.pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}`
      break

    case 'whatsapp':
      formattedUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`
      break

    case 'linkedin':
      formattedUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}`
      break

    default:
      formattedUrl = url
      break
  }

  window.open(formattedUrl, '_blank')
}

const handlePrice = (productVariant) => {
  if (!productVariant) {
    return
  }
  let prices = {}

  const { price, is_discount_time, sale_price, sale_price_time } =
    productVariant

  prices = {
    price: formatCurrency(price),
  }

  if (sale_price) {
    if (is_discount_time && sale_price_time && sale_price_time.length === 2) {
      const [startTime, endTime] = sale_price_time.map((time) => new Date(time))

      if (new Date() >= startTime && new Date() <= endTime) {
        prices.sale_price = formatCurrency(sale_price)
      }
    } else if (!is_discount_time) {
      prices.sale_price = formatCurrency(sale_price)
    }
  }
  return prices
}

const handleRenderPrice = (productVariant) => {
  if (!productVariant) {
    return ''
  }

  const { price, is_discount_time, sale_price, sale_price_time } =
    productVariant
  let html = ''

  // Format the regular price
  const formattedPrice = formatCurrency(price)

  if (sale_price) {
    if (is_discount_time && sale_price_time && sale_price_time.length === 2) {
      const [startTime, endTime] = sale_price_time.map((time) => new Date(time))
      const now = new Date()

      // Check if the current time is within the discount period
      if (now >= startTime && now <= endTime) {
        html = `
            <div class="product-price">
              <ins class="new-price">${formatCurrency(sale_price)}</ins>
              <del class="old-price">${formattedPrice}</del>
            </div>
          `
      } else {
        html = `
            <div class="product-price">
              <ins class="new-price">${formattedPrice}</ins>
            </div>
          `
      }
    } else if (!is_discount_time) {
      html = `
          <div class="product-price">
            <ins class="new-price">${formatCurrency(sale_price)}</ins>
            <del class="old-price">${formattedPrice}</del>
          </div>
        `
    }
  } else {
    html = `
        <div class="product-price">
          <ins class="new-price">${formattedPrice}</ins>
        </div>
      `
  }

  return html
}

const generateUUID = () => {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[x]/g, function (c) {
    let r = (Math.random() * 16) | 0,
      v = c === 'x' ? r : (r & 0x3) | 0x8
    return v.toString(16)
  })
}

const generateRandomString = (length) => {
  let result = ''
  const characters =
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  const charactersLength = characters.length
  let counter = 0
  while (counter < length) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength))
    counter += 1
  }
  return result
}

const hintPhoneNumber = (phoneNumber) => {
  const cleaned = phoneNumber.replace(/\D/g, '')

  if (cleaned.length < 4) return 'Invalid phone number'

  const hinted =
    cleaned.slice(0, 2) + '*'.repeat(cleaned.length - 4) + cleaned.slice(-2)

  return hinted
}

const hintEmail = (email) => {
  const [localPart, domain] = email.split('@')
  if (!domain) return 'Invalid email address'

  const hintedLocalPart =
    localPart.charAt(0) +
    '*'.repeat(localPart.length - 2) +
    localPart.charAt(localPart.length - 1)
  const hintedDomain =
    domain.charAt(0) +
    '.'.repeat(domain.length - 2) +
    domain.charAt(domain.length - 1)

  return `${hintedLocalPart}@${hintedDomain}`
}

const formatTime = (value) => {
  return String(value).padStart(2, '0') // Đảm bảo luôn có 2 chữ số
}

const showNotification = (title, body, icon = 'src/assets/images/logo.png') => {
  if (Notification.permission === 'granted') {
    new Notification(title, {
      body: body,
      icon: icon,
    })
  } else if (Notification.permission !== 'denied') {
    Notification.requestPermission().then(function (permission) {
      if (permission === 'granted') {
        // Hiển thị thông báo
        new Notification(title, {
          body: body,
          icon: icon,
        })
      }
    })
  }
}

const timeAgo = (dateString) => {
  const now = new Date()
  const pastDate = new Date(dateString)
  const seconds = Math.floor((now - pastDate) / 1000)

  let interval = Math.floor(seconds / 31536000)
  if (interval >= 1) return interval + ' năm trước'

  interval = Math.floor(seconds / 2592000)
  if (interval >= 1) return interval + ' tháng trước'

  interval = Math.floor(seconds / 86400)
  if (interval >= 1) return interval + ' ngày trước'

  interval = Math.floor(seconds / 3600)
  if (interval >= 1) return interval + ' giờ trước'

  interval = Math.floor(seconds / 60)
  if (interval >= 1) return interval + ' phút trước'

  return 'vừa mới'
}

const numberWithCommas = (x) => {
    x = x.toString().replace(/[^0-9]/g, '');
    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, '.');
};
export {
  debounce,
  resizeImage,
  getBase64,
  getFileNameFromUrl,
  getFileFromFileList,
  getImageToAnt,
  isJSONString,
  cleanedData,
  generateSlug,
  handleDateChangeToAnt,
  sortAndConcatenate,
  getLastPartOfSlug,
  removeLastSegment,
  handleSocialIconClick,
  handlePrice,
  handleRenderPrice,
  toast,
  generateUUID,
  generateRandomString,
  hintPhoneNumber,
  hintEmail,
  formatTime,
  showNotification,
  timeAgo,
  numberWithCommas
}

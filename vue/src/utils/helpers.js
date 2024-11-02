import dayjs from 'dayjs';

const debounce = (func, delay) => {
  let timerId;
  return (...args) => {
    clearTimeout(timerId);
    timerId = setTimeout(() => {
      func(...args);
    }, delay);
  };
};

const resizeImage = (image, width, height) => {
  if (!image) {
    return image;
  }
  const params = [];

  if (width) {
    params.push(`w=${width}`);
  }
  if (height) {
    params.push(`h=${height}`);
  }

  if (params.length > 0) {
    const separator = image.includes('?') ? '&' : '?';
    image += separator + params.join('&');
  }

  return image;
};

const getBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
};

const getFileNameFromUrl = (url) => {
  const parts = url.split('/');
  return parts[parts.length - 1];
};

const getFileFromFileList = (fileList) => {
  if (!fileList || fileList.length === 0) {
    return [];
  }
  const fileArr = fileList.map((file) => file.url);
  return JSON.stringify(fileArr, null, 2);
};

const getImageToAnt = (images) => {
  if (!images) return [];

  // Kiểm tra nếu images là một chuỗi
  if (typeof images === 'string') {
    const fileName = getFileNameFromUrl(images);
    return [
      {
        uid: fileName,
        name: fileName,
        status: 'done',
        url: images
      }
    ];
  }

  // Kiểm tra nếu images là một mảng
  if (Array.isArray(images)) {
    return images.map((image) => ({
      uid: getFileNameFromUrl(image),
      name: getFileNameFromUrl(image),
      status: 'done',
      url: image
    }));
  }

  // Trường hợp còn lại, trả về mảng rỗng
  return [];
};

const isJSONString = (str) => {
  try {
    JSON.parse(str);
    return true;
  } catch (error) {
    return false;
  }
};

const cleanedData = (data) => {
  return Object.entries(data).filter(
    ([key, value]) => value != null && value != '' && value != undefined && value != 'null'
  );
};

const generateSlug = (str) => {
  if (!str) return '';
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
    '&': '-and-'
  };
  return str
    .toString()
    .toLowerCase()
    .replace(/[^\w\s-]/g, (match) => specialCharsMap[match] || '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
};

const handleDateChangeToAnt = (dates) => {
  if (!Array.isArray(dates) || dates.length !== 2) {
    return [];
  }
  const formattedDates = dates.map((date) => dayjs(date));

  return formattedDates;
};

const generateRandomString = (length) => {
  let result = '';
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  const charactersLength = characters.length;
  let counter = 0;
  while (counter < length) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
    counter += 1;
  }
  return result;
};
const timeAgo = (dateString) => {
  const now = new Date();
  const pastDate = new Date(dateString);
  const seconds = Math.floor((now - pastDate) / 1000);

  let interval = Math.floor(seconds / 31536000);
  if (interval >= 1) return interval + ' năm trước';

  interval = Math.floor(seconds / 2592000);
  if (interval >= 1) return interval + ' tháng trước';

  interval = Math.floor(seconds / 86400);
  if (interval >= 1) return interval + ' ngày trước';

  interval = Math.floor(seconds / 3600);
  if (interval >= 1) return interval + ' giờ trước';

  interval = Math.floor(seconds / 60);
  if (interval >= 1) return interval + ' phút trước';

  return 'vừa mới';
};

const getErrorMsg = (error) => {
  if (error.response) {
    return error?.response?.data?.messages || 'Something went wrong!';
  }
  return error?.message || 'Something went wrong!';
};

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
  generateRandomString,
  timeAgo,
  getErrorMsg,
  numberWithCommas
};

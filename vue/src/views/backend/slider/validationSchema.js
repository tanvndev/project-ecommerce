import * as yup from 'yup';

const validationSchema = yup.object({

    name: yup.string().required('Tiêu đề là bắt buộc'),
    code: yup.string().required('code là bắt buộc'),
    width: yup.number().required('Chiều rộng là bắt buộc').positive('Chiều rộng phải là số dương'),
    height: yup.number().required('Chiều dài là bắt buộc').positive('Chiều dài phải là số dương'),
    effect: yup.string().required('Hiệu ứng là bắt buộc'),
    showArrow: yup.boolean().required('Mũi tên là bắt buộc'),
    navigation: yup.string().required('Điều hướng là bắt buộc'),
    autoPlay: yup.boolean(),
    pauseOnHover: yup.boolean(),
    transitionSpeed: yup
      .number()
      .required('Chuyển cảnh là bắt buộc')
      .positive('Chuyển cảnh phải là số dương'),
    effectSpeed: yup
      .number()
      .required('Tốc độ hiệu ứng là bắt buộc')
      .positive('Tốc độ hiệu ứng phải là số dương'),
   
  item: yup
    .array()
    .of(
      yup.object({
        description: yup.string().required('Mô tả không được bỏ trống'),
        url: yup.string().url('URL không hợp lệ').required('URL không được bỏ trống')
      })
    )
    .min(1, 'Phải có ít nhất một slide')
});

export { validationSchema };

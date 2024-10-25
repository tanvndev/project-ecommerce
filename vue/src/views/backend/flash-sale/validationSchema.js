import * as yup from 'yup';

export const validationSchema = yup.object().shape({
  name: yup.string().required('Tên là bắt buộc'),
  products: yup
    .array()
    .of(
      yup.object().shape({
        price: yup.number().positive('Giá phải là số dương').required('Giá là bắt buộc'),
        quantity: yup
          .number()
          .integer('Số lượng phải là số nguyên')
          .positive('Số lượng phải là số dương')
          .required('Số lượng là bắt buộc')
      })
    )
    .required('Ít nhất phải có một sản phẩm'),
  publish: yup.string().required('Trạng thái là bắt buộc'),
  start_date: yup
    .date()
    .required('Ngày bắt đầu là bắt buộc')
    .min(new Date(), 'Ngày bắt đầu phải lớn hơn ngày hôm nay'),
    end_date: yup
    .date()
    .required('Ngày kết thúc là bắt buộc')
    .min(yup.ref('start_date'), 'Ngày kết thúc phải sau ngày bắt đầu')
});

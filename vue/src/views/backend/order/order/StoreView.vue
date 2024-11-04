<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="18">
              <a-card class="mt-3" title="Danh sách sản phẩm">
                <AleartError :errors="!_.isEmpty(errors) ? errors : state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <SearchProductView @on-save="handleSave" />
                  </a-col>
                </a-row>
              </a-card>
              <a-card class="mt-3" title="Thông tin đơn hàng">
                <a-row :gutter="[16, 16]" class="mt-3 flex justify-between">
                  <a-col :span="12">
                    <div class="mb-4">
                      <SelectComponent
                        name="payment_method_id"
                        label="Phương thức thanh toán"
                        :options="state.paymentMethods"
                        :required="true"
                        placeholder="Chọn phương thức thanh toán"
                      />
                    </div>
                    <div class="mb-4">
                      <SelectComponent
                        name="shipping_method_id"
                        label="Hình thức vận chuyển"
                        :options="state.shippingMethodSelects"
                        :required="true"
                        placeholder="Chọn hình thức với chuyển"
                        @on-change="handleChangeShippingMethod"
                      />
                    </div>

                    <div class="mb-4">
                      <InputComponent
                        name="note"
                        type-input="textarea"
                        label="Ghi chú"
                        :show-count="false"
                        placeholder="Để lại lời nhắn"
                      />
                    </div>
                  </a-col>
                  <a-col>
                    <div class="mt-7 flex flex-col gap-3 text-[15px]">
                      <div class="d-flex items-center justify-end">
                        <span class="font-bold">Tạm tính</span>
                        <span class="w-[200px] text-right">
                          {{ formatCurrency(total_price) }}
                        </span>
                      </div>

                      <div class="d-flex items-center justify-end">
                        <span class="font-bold">Phí vận chuyển</span>
                        <span class="w-[200px] text-right">
                          {{ formatCurrency(shipping_fee) }}
                        </span>
                      </div>

                      <div class="d-flex items-center justify-end text-[16px]">
                        <span class="font-bold">Tổng cuối</span>
                        <span class="w-[200px] text-right font-bold">
                          {{ formatCurrency(final_price) }}
                        </span>
                      </div>
                    </div>
                  </a-col>
                </a-row>
              </a-card>
            </a-col>

            <a-col :span="6">
              <SearchUser @change-user="handleChangeUser" />
            </a-col>
          </a-row>

          <div class="fixed bottom-0 right-[19px] p-10">
            <a-button html-type="submit" type="primary" size="large">
              <i class="fas fa-save mr-2"></i>
              <span>Lưu thông tin</span>
            </a-button>
          </div>
        </form>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  AleartError,
  BreadcrumbComponent,
  InputComponent,
  MasterLayout,
  SelectComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import { formatCurrency, formatDataToSelect, formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import _ from 'lodash';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import SearchProductView from './partials/SearchProductView.vue';
import SearchUser from './partials/SearchUser.vue';

const { create, messages, data, getAll } = useCRUD();
const router = useRouter();

// STATE
const state = reactive({
  endpoint: 'orders/create',
  pageTitle: 'Thêm mới đơn hàng',
  errors: {},
  products: [],
  paymentMethods: [],
  shippingMethods: [],
  shippingMethodSelects: []
});

const shipping_fee = ref(0);
const total_price = computed(() => {
  let total = 0;
  state.products.forEach((item) => {
    total += item.price * item.quantity;
  });
  return total;
});
const final_price = computed(() => total_price.value + shipping_fee.value);

const { handleSubmit, setValues, errors } = useForm({
  validationSchema: yup.object({
    shipping_method_id: yup.string().required('Hình thức vận chuyển không được để trống.'),
    payment_method_id: yup.string().required('Phương thức thanh toán không được để trống.'),
    order_items: yup.array().required('Danh sách sản phẩm đơn mua không đặt.'),
    user_id: yup.string().required('Vui lòng chọn khác hàng.'),
    customer_name: yup.string().required('Tên khách hàng không được để trống.'),
    customer_email: yup
      .string()
      .required('Email không được để trống.')
      .email('Email không đúng định dạng.'),
    customer_phone: yup.string().required('Số điện thoại không được để trống.'),
    shipping_address: yup.string().required('Địa chỉ giao hàng không được để trống.'),
    province_id: yup.string().required('Vui lòng chọn Tỉnh/ Thành phố.'),
    district_id: yup.string().required('Vui bạn chọn Quận/ Huyện.'),
    ward_id: yup.string().required('Vui này chọn phú Phường/ Xã.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  const response = await create(state.endpoint, values);
  console.log(errors.value);

  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  state.errors = {};
  message.success(messages.value);
  router.push({ name: 'order.index' });
});

const handleSave = (data) => {
  state.products = data;
  console.log(state.products);

  setValues({
    order_items: data
  });
};

const handleChangeShippingMethod = (value) => {
  const shipping = state.shippingMethods.find((item) => item.id == value);
  shipping_fee.value = parseFloat(shipping.base_cost) || 0;
};

const handleChangeUser = ({ address, user }) => {
  setValues({
    customer_name: address.fullname,
    customer_email: user.email,
    customer_phone: address.phone,
    shipping_address: address.shipping_address,
    province_id: address.province_id,
    district_id: address.district_id,
    ward_id: address.ward_id,
    user_id: user.id
  });
};

const getPaymentMethods = async () => {
  try {
    await getAll('payment-methods');
    state.paymentMethods = formatDataToSelect(data.value);
  } catch (error) {
    console.log(error);
  }
};

const getShippingMethod = async () => {
  try {
    await getAll('shipping-methods');
    state.shippingMethods = data.value;
    state.shippingMethodSelects = formatDataToSelect(data.value);
  } catch (error) {
    console.log(error);
  }
};

onMounted(() => {
  getPaymentMethods();
  getShippingMethod();
});
</script>

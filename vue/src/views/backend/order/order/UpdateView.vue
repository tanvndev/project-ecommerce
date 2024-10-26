<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-10 h-screen min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16" v-if="order">
            <a-col :span="18">
              <a-card class="mt-3">
                <AleartError :errors="state.errors" />
                <template #title> Đơn hàng #{{ order.code }} </template>
                <!-- Order detail -->
                <OrderDetail :order="order" @update:status="fetchOne" />

                <!-- Delivery Status -->
                <DeliveryStatus :order="order" :weight="state.weight" />
              </a-card>
            </a-col>

            <a-col :span="6">
              <!-- Aside View -->
              <CutomerInfo :order="order" />

              <!-- Start Status -->
              <a-card class="mt-3" title="Trạng thái">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col span="24">
                    <SelectComponent
                      name="order_status"
                      label="Trạng thái đơn hàng"
                      :options="ORDER_STATUS"
                    />
                  </a-col>
                  <a-col span="24">
                    <SelectComponent
                      name="payment_status"
                      label="Trạng thái thanh toán"
                      :options="PAYMENT_STATUS"
                    />
                  </a-col>
                  <a-col span="24">
                    <SelectComponent
                      name="delivery_status"
                      label="Trạng thái vận chuyển"
                      :options="DELYVERY_STATUS"
                    />
                  </a-col>
                  <a-col span="24">
                    <a-button html-type="submit" class="float-end" size="large">
                      Cập nhập
                    </a-button>
                  </a-col>
                </a-row>
              </a-card>
            </a-col>
          </a-row>
          <div class="p-20 text-center" v-else>
            <a-spin />
          </div>
        </form>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  MasterLayout,
  BreadcrumbComponent,
  SelectComponent,
  AleartError
} from '@/components/backend';
import { computed, onMounted, reactive, ref } from 'vue';
import { useForm } from 'vee-validate';
import { formatMessages } from '@/utils/format';
import * as yup from 'yup';
import router from '@/router';
import { ORDER_STATUS, PAYMENT_STATUS, DELYVERY_STATUS } from '@/static/order';
import { useCRUD } from '@/composables';
import DeliveryStatus from './partials/DeliveryStatus.vue';
import OrderDetail from './partials/OrderDetail.vue';
import CutomerInfo from './partials/CutomerInfo.vue';
import { message } from 'ant-design-vue';

// VARIABLES
const { getOne, update, messages, data } = useCRUD();
const code = computed(() => router.currentRoute.value.params.code || null);

// STATE
const state = reactive({
  endpoint: 'orders',
  pageTitle: 'Chi tiết đơn hàng',
  errors: {},
  weight: 0
});
const order = ref(null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    payment_status: yup.string().required('Vui lòng chọn trạng thái thanh toán.'),
    delivery_status: yup.string().required('Vui lòng chọn trạng thái vận chuyển.'),
    order_status: yup.string().required('Vui lòng chọn trạng thái đơn hàng.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  console.log(messages.value);

  const response = await update(state.endpoint, order.value.id, values);
  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  message.success(messages.value);
  fetchOne();
  state.errors = {};
});

const fetchOne = async () => {
  await getOne(state.endpoint, code.value);
  order.value = data.value;
  const { order_items, payment_status_code, delivery_status_code, order_status_code } = data.value;

  state.weight =
    order_items?.reduce((total, item) => {
      return total + (item.weight || 0) * (item.quantity || 0);
    }, 0) || 0;

  setValues({
    payment_status: payment_status_code,
    delivery_status: delivery_status_code,
    order_status: order_status_code
  });
};

onMounted(() => {
  if (code.value) {
    fetchOne();
  }
});
</script>

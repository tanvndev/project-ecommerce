<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen pb-10">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16" v-if="order">
            <a-col :span="18">
              <a-card class="mt-3">
                <AleartError :errors="state.errors" />
                <template #title>
                  <div class="flex items-center justify-between">
                    <h3 class="font-medium capitalize">Đơn hàng #{{ order.code }}</h3>
                    <a-tag :color="order.order_status_color"> {{ order.order_status }} </a-tag>
                  </div>
                </template>
                <!-- Order detail -->
                <OrderDetail :order="order" @update:status="fetchOne" />

                <!-- Delivery Status -->
                <DeliveryStatus :order="order" :weight="state.weight" />
              </a-card>
            </a-col>

            <a-col :span="6">
              <!-- Start Status -->
              <a-card
                class="mt-3"
                title="Trạng thái"
                v-if="
                  checkAdmin(role) &&
                  order.order_status_code != ORDER_STATUS[3].value &&
                  order.order_status_code != ORDER_STATUS[4].value
                "
              >
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
                    <a-button html-type="submit" class="float-end" size="large">
                      Cập nhập
                    </a-button>
                  </a-col>
                </a-row>
              </a-card>

              <!-- Order request -->
              <a-card
                class="mt-3"
                title="Yêu cầu thay đổi trạng thái"
                v-if="!checkAdmin(role) && order?.order_status_code != ORDER_STATUS[3].value"
              >
                <!-- Nút tạo yêu cầu -->
                <a-button size="large" type="primary" @click="showModal">Tạo yêu cầu</a-button>

                <!-- Popup (Modal) -->
                <a-modal
                  :open="isModalVisible"
                  title="Tạo yêu cầu thay đổi trạng thái"
                  @ok="handleOk"
                  @cancel="handleCancel"
                  cancelText="Hủy bỏ"
                  okText="Xác nhận"
                >
                  <a-divider class="my-2" />
                  <div class="mb-3">
                    <label for="reason" class="mb-2 block">Lý do</label>

                    <a-textarea
                      size="large"
                      id="reason"
                      placeholder="Lý do tạo yêu cầu"
                      name="reason"
                      v-model:value="reason"
                      :auto-size="true"
                    />
                  </div>

                  <div class="mb-3">
                    <label for="requested_status" class="mb-2 block"
                      >Trạng thái muốn đổi thành</label
                    >
                    <a-select
                      size="large"
                      name="requested_status"
                      v-model:value="requested_status"
                      class="w-full"
                      :options="ORDER_STATUS_SELECT"
                    />
                  </div>
                </a-modal>
              </a-card>

              <!-- Aside View -->
              <CutomerInfo :order="order" />

              <a-card
                class="mt-3"
                title="Trạng thái"
                v-if="order.order_status_code == ORDER_STATUS[3].value"
              >
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    width="25"
                    focusable="false"
                    aria-hidden="true"
                  >
                    <g clip-path="url(#174__a)">
                      <circle
                        cx="12"
                        cy="12"
                        r="10"
                        fill="#fff"
                        stroke="#CFF6E7"
                        stroke-width="4"
                      ></circle>
                      <path
                        fill="#0DB473"
                        fill-rule="evenodd"
                        d="M4 12c0-4.416 3.584-8 8-8s8 3.584 8 8-3.584 8-8 8-8-3.584-8-8m6.4 1.736 5.272-5.272 1.128 1.136-6.4 6.4-3.2-3.2 1.128-1.128z"
                        clip-rule="evenodd"
                      ></path>
                    </g>
                    <defs>
                      <clipPath id="174__a"><path fill="#fff" d="M0 0h24v24h-24z"></path></clipPath>
                    </defs>
                  </svg>
                  <h3 class="ms-2">Đã hoàn thành xử lý giao hàng.</h3>
                </div>
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
  AleartError,
  BreadcrumbComponent,
  MasterLayout,
  SelectComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import axios from '@/configs/axios';
import router from '@/router';
import { ORDER_STATUS, ORDER_STATUS_SELECT, PAYMENT_STATUS } from '@/static/order';
import { formatMessages } from '@/utils/format';
import { checkAdmin } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive, ref } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';
import CutomerInfo from './partials/CutomerInfo.vue';
import DeliveryStatus from './partials/DeliveryStatus.vue';
import OrderDetail from './partials/OrderDetail.vue';

// STATE
const state = reactive({
  endpoint: 'orders',
  pageTitle: 'Chi tiết đơn hàng',
  errors: {},
  weight: 0
});

const store = useStore();
const role = computed(() => store.getters['authStore/getRole']);

// VARIABLES
const { getOne, update, messages, data, create } = useCRUD();

const code = computed(() => router.currentRoute.value.params.code || null);

const isModalVisible = ref(false);
const reason = ref('');
const requested_status = ref('');

const showModal = () => {
  isModalVisible.value = true;
};

const handleOk = async () => {
  if (!reason.value.trim()) {
    message.error('Lý do không được để trống!');
    return;
  }

  if (!requested_status.value) {
    message.error('Trạng thái không được để trống!');
    return;
  }

  const payload = {
    requested_status: requested_status.value,
    reason: reason.value,
    order_id: order.value.id
  };

  const response = await create(state.endpoint + '/status-change-request', payload);

  if (!response) {
    return message.error(messages.value);
  }

  state.errors = {};
  message.success(messages.value);

  reason.value = '';
  requested_status.value = '';

  isModalVisible.value = false;
};

// Xử lý khi nhấn Cancel
const handleCancel = () => {
  console.log('Cancel');

  isModalVisible.value = false;
};

const order = ref(null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    payment_status: yup.string().required('Vui lòng chọn trạng thái thanh toán.'),
    order_status: yup.string().required('Vui lòng chọn trạng thái đơn hàng.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  try {
    state.errors = {};

    const routeApi = `/orders/${code.value}/admin/update?_method=PUT`;

    const response = await axios.post(routeApi, values);

    message.success(response.messages);
    fetchOne();
    state.errors = {};
  } catch (error) {
    const msg = error.response.data.messages || 'Có lỗi từ máy chủ vui lòng thử lại.';
    message.error(msg);

    return (state.errors = formatMessages(msg));
  }
});

const fetchOne = async () => {
  await getOne(state.endpoint, code.value);
  order.value = data.value;
  const { order_items, payment_status_code, order_status_code } = data.value;

  state.weight =
    order_items?.reduce((total, item) => {
      return total + (item.weight || 0) * (item.quantity || 0);
    }, 0) || 0;

  setValues({
    payment_status: payment_status_code,
    order_status: order_status_code
  });
};

onMounted(() => {
  if (code.value) {
    fetchOne();
  }
});
</script>

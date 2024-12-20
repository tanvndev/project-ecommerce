<template>
  <AleartError :errors="errors" />

  <a-row
    v-for="item in order?.order_items"
    :key="item.id"
    :gutter="16"
    class="mb-4 items-center border-b pb-4"
  >
    <a-col :span="15">
      <div class="flex items-center">
        <div class="mr-2">
          <img class="h-[60px] w-[60px] object-contain" :src="item.image" alt="" />
        </div>
        <div>
          <RouterLink
            :to="`/product/update/${item.product_id}?variant_id=${item.product_variant_id}`"
            class="mb-1 block text-primary-500"
            >{{ item.product_variant_name }}</RouterLink
          >
          <span class="text-[13px] text-gray-500">Phân loại: {{ item.attribute_values }}</span>
        </div>
      </div>
    </a-col>
    <a-col :span="4">
      <div class="text-left">
        <span class="block">{{ formatCurrency(item.sale_price || item.price) }}</span>
        <del v-if="item.sale_price">{{ formatCurrency(item.price) }}</del>
      </div>
    </a-col>
    <a-col :span="1">
      <span class="block text-center">x{{ item.quantity }}</span>
    </a-col>
    <a-col :span="4">
      <div class="text-right">
        <span class="block">{{
          formatCurrency((item.sale_price || item.price) * item.quantity)
        }}</span>
      </div>
    </a-col>
  </a-row>
  <div class="flex flex-col gap-3 text-[15px]" v-if="order">
    <div class="flex items-center justify-end">
      <span class="font-bold">Tạm tính</span>
      <span class="w-[300px] text-right">
        {{ formatCurrency(order?.total_price) }}
      </span>
    </div>
    <div class="flex items-center justify-end" v-if="order?.discount">
      <span class="font-bold">Giảm giá</span>
      <span class="w-[300px] text-right">
        {{ formatCurrency(order?.discount) }}
      </span>
    </div>
    <div class="flex items-center justify-end">
      <span class="font-bold">Phí vận chuyển</span>
      <span class="w-[300px] text-right">
        {{ formatCurrency(order?.shipping_fee) }}
      </span>
    </div>
    <div class="flex items-center justify-end">
      <span class="font-bold">Phương thức thanh toán</span>
      <span class="w-[300px] text-right">{{
        order?.additional_details?.payment_method?.name
      }}</span>
    </div>
    <div class="flex items-center justify-end">
      <span class="font-bold">Trạng thái thanh toán</span>
      <div class="w-[300px] text-right">
        <a-tag :color="order?.payment_status_color" class="-mr-1">
          {{ order?.payment_status }}
        </a-tag>
      </div>
    </div>
    <div class="flex items-center justify-end">
      <span class="font-bold">Tổng cuối</span>
      <span class="w-[300px] text-right">
        {{ formatCurrency(order?.final_price) }}
      </span>
    </div>
  </div>

  <a-divider />
  <div class="text-right">
    <a-button size="large" class="mr-3">
      <i class="fas fa-print mr-2"></i>
      In đơn hàng
    </a-button>
    <a-button size="large">
      <i class="fas fa-download mr-2"></i>
      Tải về đơn hàng
    </a-button>
  </div>

  <div class="ml-auto mt-6 w-[400px]">
    <label class="mb-2 block text-[#222]">Ghi chú</label>
    <a-textarea
      v-model:value="note"
      placeholder="Ghi chú của bạn cho người bán"
      :auto-size="{ minRows: 3, maxRows: 5 }"
    />
    <a-button size="large" class="mt-3" @click="handleUpdateNote"> Cập nhập </a-button>
  </div>
  <a-divider />
  <div class="flex items-center justify-between">
    <span class="uppercase">
      <i
        class="fas fa-shopping-basket mr-2"
        v-if="
          order?.order_status_code == ORDER_STATUS[0].value ||
          order?.order_status_code == ORDER_STATUS[4].value
        "
      ></i>
      <i class="fas fa-check mr-2 text-green-500" v-else></i>
      {{ order?.order_status }}
    </span>

    <a-button
      size="large"
      type="primary"
      @click="orderStatusModal = true"
      v-if="order?.order_status_code == ORDER_STATUS[0].value"
    >
      Xác nhận đơn hàng
    </a-button>

    <a-button
      size="large"
      type="primary"
      @click="orderStatusModal = true"
      v-if="order?.order_status_code == ORDER_STATUS[1].value"
    >
      Xác nhận đang giao
    </a-button>

    <a-button
      size="large"
      type="primary"
      @click="orderStatusModal = true"
      v-if="order?.order_status_code == ORDER_STATUS[2].value"
    >
      Xác nhận hoàn thành
    </a-button>

    <!-- Order status modal -->
    <a-modal v-model:open="orderStatusModal">
      <div class="mt-6 flex flex-col items-center">
        <i class="far fa-info-circle text-[70px] text-primary-500"></i>
        <h4 class="mt-2 text-[16px]">Cập nhập trạng thái đơn hàng</h4>
      </div>
      <p class="mb-4">
        Bạn đang thực hiện thao tác xác nhận đơn hàng này. Lưu ý kiểm tra kĩ khi thực hiện thao tác.
      </p>

      <template #footer>
        <a-button @click="orderStatusModal = false" class="mr-1">Hủy bỏ</a-button>
        <a-button
          @click="handleUpdateStatus('order_status', order?.order_status_code_next)"
          type="primary"
          >Xác nhận</a-button
        >
      </template>
    </a-modal>
  </div>
  <a-divider />
  <div class="flex items-center justify-between">
    <span class="uppercase">
      <i
        class="fas fa-credit-card-front mr-2"
        v-if="order?.payment_status_code == PAYMENT_STATUS[0].value"
      ></i>
      <i class="fas fa-check mr-2 text-green-500" v-else></i>
      {{ order?.payment_status }}
    </span>

    <a-button
      size="large"
      type="primary"
      @click="paymentStatusModal = true"
      v-if="order?.payment_status_code != PAYMENT_STATUS[1].value"
    >
      Xác nhận thanh toán
    </a-button>

    <!-- Payment status modal -->
    <a-modal v-model:open="paymentStatusModal">
      <div class="mt-6 flex flex-col items-center">
        <i class="far fa-info-circle text-[70px] text-primary-500"></i>
        <h4 class="mt-2 text-[16px]">Xác nhận thanh toán</h4>
      </div>
      <p class="mb-4">
        Đã xử lý bằng . Bạn có nhận được thanh toán bên ngoài hệ thống không? Thanh toán này sẽ
        không được lưu vào hệ thống và không thể hoàn lại.
      </p>

      <template #footer>
        <a-button @click="paymentStatusModal = false" class="mr-1">Hủy bỏ</a-button>
        <a-button
          @click="handleUpdateStatus('payment_status', PAYMENT_STATUS[1].value)"
          type="primary"
          >Xác nhận</a-button
        >
      </template>
    </a-modal>
  </div>
  <a-divider />
</template>
<script setup>
import { AleartError } from '@/components/backend';
import axios from '@/configs/axios';
import { ORDER_STATUS, PAYMENT_STATUS } from '@/static/order';
import { formatCurrency } from '@/utils/format';
import { getErrorMsg } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import { computed, ref, watch } from 'vue';

const errors = ref({});
const paymentStatusModal = ref(false);
const orderStatusModal = ref(false);

const props = defineProps({
  order: Object
});
const note = ref(props.order?.note);
const emits = defineEmits(['update:status']);

const orderStatusValue = computed(() => {
  let value = 1;
  return value;
});

const handleUpdateNote = async () => {
  try {
    const response = await axios.post(`/orders/${props.order.id}?_method=PUT`, {
      note: note.value
    });

    if (response.status == 'success') {
      message.success(response.messages);
      return emits('update:status');
    }
  } catch (error) {
    const msg = getErrorMsg(error);

    message.error(msg);
    errors.value = { msg };
  }
};

const handleUpdateStatus = async (field, value) => {
  try {
    orderStatusModal.value = false;
    paymentStatusModal.value = false;

    const payload = {
      [field]: value
    };

    const response =
      field == 'order_status'
        ? await axios.post(`/orders/${props.order.id}/update/order?_method=PUT`, payload)
        : await axios.post(`/orders/${props.order.id}/update/payment?_method=PUT`, payload);

    errors.value = {};
    if (response.status == 'success') {
      message.success(response.messages);
      return emits('update:status');
    }
  } catch (error) {
    const msg = getErrorMsg(error);

    message.error(msg);
    errors.value = { msg };
  }
};

watch(
  () => props.order,
  (newValue) => {
    note.value = newValue?.note;
  },
  {
    immediate: true
  }
);
</script>

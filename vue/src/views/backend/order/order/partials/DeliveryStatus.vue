<template>
  <div class="mt-10">
    <a-row :gutter="[16, 30]">
      <a-col span="8">
        <p class="text-[12px] font-bold text-gray-500">Mã đơn hàng</p>
        <span class="text-[16px]"> {{ order?.code }}</span>
      </a-col>
      <a-col span="8">
        <p class="text-[12px] font-bold text-gray-500">Hình thức thanh toán</p>
        <span class="text-[16px]"> {{ order?.additional_details?.payment_method?.name }}</span>
      </a-col>
      <a-col span="8">
        <p class="text-[12px] font-bold text-gray-500">Hình thức vận chuyển</p>
        <span class="text-[16px]"> {{ order?.additional_details?.shipping_method?.name }}</span>
      </a-col>
      <a-col span="8">
        <p class="text-[12px] font-bold text-gray-500">Cân nặng</p>
        <span class="text-[16px]">{{ weight }} g</span>
      </a-col>
      <a-col span="8">
        <p class="text-[12px] font-bold text-gray-500">Cập nhập lần cuối</p>
        <span class="text-[16px]"> {{ order?.ordered_at }}</span>
      </a-col>
      <a-col span="8" v-if="order?.payment_method_id == 1">
        <p class="text-[12px] font-bold text-gray-500">Số tiền phải trả</p>
        <span class="text-[16px]"> {{ formatCurrency(order?.final_price) }}</span>
      </a-col>
    </a-row>
    <!-- <a-divider /> -->

    <!-- <a-button size="large"> Cập nhập trạng thái vận chuyển </a-button> -->
  </div>
</template>
<script setup>
import { formatCurrency } from '@/utils/format';

defineProps({
  order: Object,
  weight: [Number, String]
});
</script>

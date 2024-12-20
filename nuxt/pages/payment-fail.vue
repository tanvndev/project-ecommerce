<script setup>
useSeoMeta({
  title: 'Đơn hàng thất bại',
  ogTitle: 'Đơn hàng thất bại',
  description: 'Đơn hàng thất bại.',
  ogDescription: 'Đơn hàng thất bại.',
})
</script>
<template>
  <v-empty-state icon="$error" class="mt-6">
    <template v-slot:media>
      <v-icon color="error"></v-icon>
    </template>

    <template v-slot:headline>
      <div class="text-h4 mt-4">
        Thanh toán đơn hàng thất bại vui lòng thử lại!
      </div>
    </template>

    <template v-slot:title>
      <div class="text-h6">
        Đơn hàng của bạn đã đặt thành công hãy thanh toán.
      </div>
    </template>

    <template v-slot:text>
      <div class="text-medium-emphasis text-caption">
        Cảm ơn bạn đã đặt hàng thành công! Chúng tôi xin xác nhận rằng đơn hàng
        của bạn đã được xử lý. Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ
        với chúng tôi. Một lần nữa, cảm ơn bạn đã lựa chọn dịch vụ của chúng
        tôi!
      </div>
    </template>

    <template v-slot:actions>
      <NuxtLink to="/" class="btn btn-dark btn-rounded btn-icon-left">
        <i class="w-icon-long-arrow-left"></i>
        Quay lại trang chủ
      </NuxtLink>
    </template>
  </v-empty-state>
</template>

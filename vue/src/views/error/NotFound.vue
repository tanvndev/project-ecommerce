<template>
  <!-- <div class="mx-auto mt-20 h-screen max-w-screen-xl items-center px-4 md:px-8">
    <div class="mx-auto max-w-lg space-y-3 text-center">
      <img class="mx-auto" :src="`${LARAVEL_URL}/images/2024/07/notfoundpng_669bcb5612264.webp`" />
      <p class="text-4xl font-semibold text-gray-800 sm:text-5xl">Page not found</p>
      <p class="text-gray-600">
        Xin lỗi, trang bạn đang tìm kiếm không thể tìm thấy hoặc đã bị xóa.
      </p>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <RouterLink
          :to="{ name: 'dashboard' }"
          class="block rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white duration-150 hover:bg-indigo-500 active:bg-indigo-700"
        >
          Quay lại
        </RouterLink>
        <a
          href="javascript:void(0)"
          class="block rounded-lg border px-4 py-2 font-medium text-gray-700 duration-150 hover:bg-gray-50 active:bg-gray-100"
        >
          Contact support
        </a>
      </div>
    </div>
  </div> -->

  <a-result
    status="404"
    title="404"
    sub-title=" Xin lỗi, trang bạn đang tìm kiếm không thể tìm thấy hoặc đã bị xóa."
  >
    <template #extra>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <RouterLink
          :to="{ name: 'dashboard' }"
          class="block rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white duration-150 hover:bg-indigo-500 hover:text-white active:bg-indigo-700"
        >
          Quay lại
        </RouterLink>
      </div>
    </template>
  </a-result>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { LARAVEL_URL } from '@/static/constants';
</script>

<template>
  <a-result
    status="403"
    title="403"
    sub-title="Xin lỗi, bạn không có quyền truy cập trang này vui lòng liên hệ quản trị viên để được hỗ trợ."
  >
    <template #extra>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <RouterLink
          :to="{ name: 'dashboard' }"
          class="block rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white duration-150 hover:bg-indigo-500 hover:text-white active:bg-indigo-700"
        >
          Quay lại
        </RouterLink>
        <RouterLink
          :to="{ name: 'login' }"
          class="block rounded-lg bg-primary-600 px-4 py-2 font-medium text-white duration-150 hover:bg-primary-500 hover:text-white active:bg-primary-700"
        >
          Đăng nhập
        </RouterLink>
      </div>
    </template>
  </a-result>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>

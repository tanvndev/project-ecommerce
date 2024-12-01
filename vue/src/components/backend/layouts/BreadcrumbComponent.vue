<script setup>
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();
const removeSave = () => {
  const routeHide = ['evaluate.index', 'order.change-status-request'];
  if (routeHide.includes(route.name)) {
    return false;
  }
  return true;
};

const emits = defineEmits(['onSave']);
defineProps({
  titlePage: {
    type: String,
    required: true
  },
  routeCreate: {
    type: String,
    default: ''
  }
});
</script>
<template>
  <a-card class="mb-2 mt-4">
    <a-page-header class="p-0" @back="() => router.back()">
      <template #title>
        <span class="text-[18px] uppercase">
          {{ titlePage }}
        </span>
      </template>

      <template #extra>
        <a-button
          v-if="route.name == 'user.catalogue.index'"
          size="large"
          class="btn-warning"
          @click="() => router.push({ name: 'user.catalogue.permission' })"
        >
          <i class="fas fa-key-skeleton mr-2 text-[13px]"></i>
          <span>Phân Quyền</span>
        </a-button>

        <a-button
          v-if="routeCreate && routeCreate != ''"
          size="large"
          type="primary"
          class="btn-success"
          @click="() => router.push({ name: routeCreate })"
        >
          <i class="far fa-plus mr-2 text-[14px]"></i>
          Thêm mới
        </a-button>

        <a-button
          size="large"
          type="primary"
          @click="() => emits('onSave')"
          v-if="!routeCreate && removeSave()"
        >
          <i class="fas fa-save mr-2 text-[14px]"></i>
          Lưu thông tin
        </a-button>
      </template>
    </a-page-header>
  </a-card>
</template>

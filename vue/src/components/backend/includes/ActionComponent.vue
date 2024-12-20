<template>
  <a-space>
    <RouterLink
      class="rounded-[6px] bg-primary-500 px-[8px] py-[7px] text-white hover:bg-primary-400 hover:text-white"
      :to="{ name: routeUpdate, params: { id: props.id } }"
    >
      <i class="fas fa-edit"></i
    ></RouterLink>
    <a-popconfirm title="Bạn có chắc chắn muốn xóa?" @confirm="handleDelete(props.id)">
      <button
        class="rounded-[6px] bg-red-500 px-[10px] py-[4px] text-white hover:bg-red-400"
      >
        <i class="fas fa-trash-alt"></i>
      </button>
    </a-popconfirm>
  </a-space>
</template>

<script setup>
import { BaseService } from '@/services';
import { message } from 'ant-design-vue';

const emits = defineEmits(['onDelete']);
const props = defineProps({
  id: [Number, String],
  routeUpdate: {
    type: String,
    required: true
  },
  endpoint: {
    type: String,
    required: true
  }
});

const handleDelete = async (id) => {
  const response = await BaseService.deleteOne(props.endpoint, id);

  const type = response.success ? 'success' : 'error';

  message[type](response.messages || 'Có lỗi từ máy chủ, vui lòng liên hệ quản trị viên.');
  emits('onDelete', id);
};
</script>

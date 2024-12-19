<template>
  <div class="text-center">
    <label class="switch">
      <input
        type="checkbox"
        @change="handleChangeStatus($event)"
        :checked="props.record[props.field] == 1 ? true : false"
      />
      <span class="slider"></span>
    </label>
  </div>
</template>

<script setup>
import BaseService from '@/services/BaseService';
import { message } from 'ant-design-vue';

const props = defineProps({
  record: Object,
  field: String,
  modelName: String
});

const handleChangeStatus = async (event) => {
  const payload = {
    modelName: props.modelName,
    modelId: props.record?.id,
    field: props.field,
    value: event.target.checked ? 1 : 2
  };
  const response = await BaseService.changeStatus(payload);
  const type = response.success ? 'success' : 'error';

  message[type](response.messages || 'Có lỗi từ máy chủ vui lòng liên hệ với quản trị viên.');
};
</script>

<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-card class="mt-3" title="Dữ liệu thuộc tính">
            <AleartError :errors="state.errors" />
            <a-row :gutter="[16, 10]">
              <a-col :span="16">
                <InputComponent
                  name="name"
                  label="Tên giá trị thuộc tính"
                  :required="true"
                  placeholder="Thêm nhiều cùng lúc ví dụ: Màu hồng|Màu đen|..."
                />
              </a-col>
              <a-col :span="8">
                <SelectComponent
                  name="attribute_id"
                  label="Thuộc tính"
                  :options="state.attributes"
                  placeholder="Chọn thuộc tính"
                  :required="true"
                />
              </a-col>
            </a-row>
          </a-card>

          <div class="fixed bottom-0 right-[19px] p-10">
            <a-button html-type="submit" type="primary" size="large">
              <i class="fas fa-save mr-2"></i>
              <span>Lưu thông tin</span>
            </a-button>
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
  InputComponent,
  MasterLayout,
  SelectComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { formatDataToSelect, formatMessages } from '@/utils/format';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';

// STATE
const state = reactive({
  endpoint: 'attributes/values',
  pageTitle: 'Thêm mới giá trị thuộc tính',
  attributes: [],
  errors: {}
});

const store = useStore();
const { getOne, getAll, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên giá trị thuộc tính không được để trống.'),
    attribute_id: yup.string().required('Vui lòng chọn thuộc tính.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  const response =
    id.value && id.value > 0
      ? await update(state.endpoint, id.value, values)
      : await create(state.endpoint, values);
  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  store.dispatch('antStore/showMessage', { type: 'success', message: messages.value });
  state.errors = {};
  router.push({ name: 'attribute.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  setValues({ name: data.value.name, attribute_id: data.value.attribute_id });
};

const getAttributes = async () => {
  await getAll('attributes');
  state.attributes = formatDataToSelect(data.value, 'id', 'name');
};

onMounted(() => {
  getAttributes();
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập giá trị thuộc tính.';
    fetchOne();
  }
});
</script>

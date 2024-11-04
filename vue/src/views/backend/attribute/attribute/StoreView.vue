<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen pb-24">
        <form>
          <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
          <a-card class="mt-3" title="Dữ liệu thuộc tính">
            <AleartError :errors="state.errors" />
            <a-row :gutter="[16, 16]">
              <a-col :span="12">
                <InputComponent
                  name="name"
                  label="Tên thuộc tính"
                  :required="true"
                  placeholder="Tên thuộc tính"
                />
              </a-col>
              <a-col :span="12">
                <InputComponent
                  name="code"
                  label="Mã thuộc tính"
                  :required="true"
                  placeholder="Mã thuộc tính"
                />
              </a-col>
              <a-col :span="24">
                <InputComponent
                  typeInput="textarea"
                  name="description"
                  label="Mô tả thuộc tính"
                  placeholder="Mô tả thuộc tính"
                />
              </a-col>
            </a-row>
          </a-card>
        </form>

        <a-card class="mt-3" v-if="id">
          <ValueView :attribute_id="id || ''" />
        </a-card>
      </div>
      <div class="fixed bottom-0 right-[19px] p-10">
        <a-button @click="onSubmit" type="primary" size="large">
          <i class="far fa-save mr-2"></i>
          <span>Lưu thông tin</span>
        </a-button>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  AleartError,
  BreadcrumbComponent,
  InputComponent,
  MasterLayout
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { formatMessages } from '@/utils/format';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';
import ValueView from './partials/ValueView.vue';

// STATE

const state = reactive({
  endpoint: 'attributes',
  pageTitle: 'Thêm mới thuộc tính',
  errors: {}
});

const store = useStore();
const { getOne, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên thuộc tính không được để trống.'),
    code: yup.string().required('Mã thuộc tính không được để trống.')
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
  setValues({ name: data.value.name, description: data.value.description, code: data.value.code });
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập thuộc tính.';
    fetchOne();
  }
});
</script>

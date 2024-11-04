<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row>
            <a-col :span="16" class="mx-auto">
              <a-card title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="12">
                    <InputComponent
                      name="name"
                      label="Tên nhóm thành viên"
                      :required="true"
                      placeholder="Tên nhóm thành viên"
                    />
                  </a-col>
                  <a-col :span="12">
                    <InputComponent
                      name="code"
                      label="Mã nhóm thành viên"
                      :required="true"
                      placeholder="Mã nhóm thành viên"
                    />
                  </a-col>
                  <a-col :span="24">
                    <InputComponent
                      type-input="textarea"
                      name="description"
                      label="Mô tả nhóm thành viên"
                      placeholder="Mô tả nhóm thành viên"
                    />
                  </a-col>
                </a-row>
              </a-card>
            </a-col>
          </a-row>

          <div class="fixed bottom-0 right-[19px] p-10">
            <a-button html-type="submit" :loading="loading" type="primary" size="large">
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
  MasterLayout
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { formatMessages } from '@/utils/format';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';

// STATE
const state = reactive({
  endpoint: 'users/catalogues',
  pageTitle: 'Thêm mới nhóm thành viên',
  errors: {}
});

const store = useStore();
const { getOne, create, update, messages, data, loading } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên nhóm thành viên không được để trống.'),
    code: yup.string().required('Mã nhóm thành viên không được để trống.').min(3)
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
  router.push({ name: 'user.catalogue.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  setValues({
    name: data.value.name,
    code: data.value.code,
    description: data.value.description
  });
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập nhóm thành viên.';
    fetchOne();
  }
});
</script>

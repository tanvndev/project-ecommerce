<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <AleartError :errors="state.errors" />
          <a-row>
            <a-col :span="16" class="mx-auto">
              <a-card class="mt-3">
                <a-row :gutter="[16, 16]">
                  <a-col :span="12">
                    <InputComponent
                      name="name"
                      label="Tên quyền người dùng"
                      :required="true"
                      placeholder="Nếu tạo nhanh CRUD thì điền tên kiểu gì cũng được."
                    />
                  </a-col>
                  <a-col :span="12">
                    <InputComponent
                      name="canonical"
                      label="Canonical"
                      :required="true"
                      placeholder="Tạo nhanh CRUD ví dụ: users:CRUD:thành viên, users.catalogue:CRU:giáo viên, ..."
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
  pageTitle: 'Thêm mới quyền người dùng',
  endpoint: 'permissions',
  errors: {}
});

const store = useStore();
const { getOne, create, update, messages, data, loading } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên quyền người dùng không được để trống.'),
    canonical: yup.string().required('Canonical không được để trống.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  console.log(values);
  const response =
    id.value && id.value > 0
      ? await update(state.endpoint, id.value, values)
      : await create(state.endpoint, values);
  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  store.dispatch('antStore/showMessage', { type: 'success', message: messages.value });
  state.errors = {};
  router.push({ name: 'permission.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  setValues({ name: data.value.name, canonical: data.value.canonical });
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập quyền người dùng.';
    fetchOne();
  }
});
</script>

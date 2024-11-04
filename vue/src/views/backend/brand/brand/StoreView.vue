<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="16">
              <a-card class="mt-3" title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <InputComponent
                      name="name"
                      label="Tên thương hiệu"
                      :required="true"
                      placeholder="Tên thương hiệu"
                    />
                  </a-col>
                  <a-col :span="24">
                    <InputComponent
                      typeInput="textarea"
                      name="description"
                      label="Mô tả thương hiệu"
                      placeholder="Tạo mô tả cho thương hiệu"
                    />
                  </a-col>
                </a-row>
              </a-card>
              <!-- SEO -->
              <SEOComponent />
            </a-col>

            <a-col :span="8">
              <a-card class="mt-3" title="Ảnh thương hiệu">
                <InputFinderComponent name="image" />
              </a-card>
            </a-col>
          </a-row>

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
  InputFinderComponent,
  MasterLayout,
  SEOComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive } from 'vue';
import * as yup from 'yup';

// VARIABLES

const { getOne, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

// STATE
const state = reactive({
  endpoint: 'brands',
  pageTitle: 'Thêm mới thương hiệu',
  errors: {}
});

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên thương hiệu không được để trống.')
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

  message.success(messages.value);
  state.errors = {};
  router.push({ name: 'brand.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  setValues({
    name: data.value.name,
    description: data.value.description,
    canonical: data.value.canonical,
    image: data.value.image
  });
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập thương hiệu.';
    fetchOne();
  }
});
</script>

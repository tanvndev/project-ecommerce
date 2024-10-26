<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 h-screen min-h-screen pb-10">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="16">
              <a-card class="mt-3" title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="20">
                    <InputComponent
                      name="name"
                      label="Tên nhóm sản phẩm"
                      :required="true"
                      placeholder="Tên nhóm sản phẩm"
                    />
                  </a-col>
                  <a-col :span="4">
                    <InputNumberComponent
                      name="order"
                      label="Vị trí nhóm sản phẩm"
                      placeholder="Vị trí"
                    />
                  </a-col>
                  <a-col :span="24">
                    <InputComponent
                      typeInput="textarea"
                      name="description"
                      label="Mô tả nhóm sản phẩm"
                      placeholder="Tạo mô tả cho nhóm sản phẩm"
                    />
                  </a-col>
                </a-row>
              </a-card>

              <!-- SEO -->
              <SEOComponent />

              <!-- ProductList -->
              <ProductListView />
            </a-col>

            <a-col :span="8">
              <a-card class="mt-3" title="Ảnh danh mục">
                <InputFinderComponent name="image" />
              </a-card>

              <a-card class="mt-3">
                <template #title>
                  <span>
                    Danh mục cha
                    <small class="text-red-500">(Không chọn mặc định là danh mục cha)</small>
                  </span>
                </template>
                <TreeSelectComponent
                  name="parent_id"
                  :options="state.productCatalogues"
                  :placeholder="'Chọn danh mục cha'"
                />
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
  MasterLayout,
  BreadcrumbComponent,
  AleartError,
  InputComponent,
  InputFinderComponent,
  TreeSelectComponent,
  InputNumberComponent,
  SEOComponent
} from '@/components/backend';
import { computed, onMounted, reactive } from 'vue';
import { useForm } from 'vee-validate';
import { formatDataToTreeSelect, formatMessages } from '@/utils/format';
import * as yup from 'yup';
import router from '@/router';
import { useCRUD } from '@/composables';
import ProductListView from './partials/ProductListView.vue';
import { message } from 'ant-design-vue';

const { getOne, create, update, getAll, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

// STATE
const state = reactive({
  endpoint: 'products/catalogues',
  pageTitle: 'Thêm mới nhóm sản phẩm',
  errors: {},
  productCatalogues: []
});

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên nhóm sản phẩm không được để trống.')
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
  router.push({ name: 'product.catalogue.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  setValues({
    name: data.value.name,
    description: data.value.description,
    canonical: data.value.canonical,
    order: data.value.order,
    parent_id: data.value.parent_id,
    image: data.value.image
  });
};

const getProductCatalogues = async () => {
  await getAll('products/catalogues');
  state.productCatalogues = formatDataToTreeSelect(data.value);
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập nhóm sản phẩm.';
    fetchOne();
  }
  getProductCatalogues();
});
</script>

<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-24 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="17">
              <!-- Thông tin chung -->
              <a-card class="mt-3" title="Thông tin sản phẩm">
                <AleartError :errors="state.error" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <InputComponent
                      label="Tiêu đề sản phẩm"
                      :required="true"
                      name="name"
                      placeholder="Tiêu đề sản phẩm"
                      :showAI="true"
                    />
                  </a-col>
                  <a-col :span="24">
                    <EditorComponent name="description" label="Mô tả sản phẩm" :showAI="true" />
                  </a-col>
                </a-row>
              </a-card>

              <!-- Album -->
              <a-card class="mt-3" title="Thư viện sản phẩm">
                <InputFinderComponent :multipleFile="true" name="album" />
              </a-card>

              <!-- Du lieu san pham -->
              <MainView />

              <!-- Mo ta ngan san pham -->
              <a-card class="mt-3" title="Mô tả ngắn của sản phẩm">
                <InputComponent
                  name="excerpt"
                  typeInput="textarea"
                  placeholder="Mô tả ngắn của sản phẩm"
                  label="Mô tả ngắn của sản phẩm"
                  :showAI="true"
                />
              </a-card>

              <!-- SEO -->
              <SEOComponent />
            </a-col>

            <div class="hidden">
              <!-- Attribute -->
              <InputComponent name="attributes" />
              <InputComponent name="variants" />
            </div>

            <!-- Sidebar right -->
            <SidebarView />
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
  EditorComponent,
  InputFinderComponent,
  SEOComponent
} from '@/components/backend';
import _ from 'lodash';
import MainView from './partials/MainView.vue';
import SidebarView from './partials/SidebarView.vue';
import { computed, onMounted, reactive, watch, watchEffect } from 'vue';
import { useForm } from 'vee-validate';
import { useStore } from 'vuex';
import { formatMessages } from '@/utils/format';
import router from '@/router';
import { useCRUD } from '@/composables';
import { validationSchema } from './validationSchema';
import { message } from 'ant-design-vue';

// STATE
const state = reactive({
  endpoint: 'products',
  pageTitle: 'Thêm mới sản phẩm',
  error: {}
});

const store = useStore();
const { create, messages } = useCRUD();

const attributes = computed(() => store.getters['productStore/getAttributes']);
const variants = computed(() => store.getters['productStore/getVariants']);
const productType = computed(() => store.getters['productStore/getProductType']);

const { handleSubmit, setFieldValue, errors } = useForm({
  validationSchema
});

const onSubmit = handleSubmit(async (values) => {
  if (productType.value == 'variable' && _.isEmpty(variants.value)) {
    return (state.error = { variants: 'Vui lòng tạo ít nhất một biến thể sản phẩm.' });
  }

  state.error = {};
  const response = await create(state.endpoint, values);
  if (!response) {
    return (state.error = formatMessages(messages.value));
  }

  state.error = {};
  message.success(messages.value);
  store.commit('productStore/removeAll');
  router.push({ name: 'product.index' });
});

watch(errors, (newErrors) => {
  state.error = newErrors;
});

watchEffect(() => {
  if (!_.isEmpty(attributes.value)) {
    setFieldValue('attributes', JSON.stringify(attributes.value));
  }
  if (!_.isEmpty(variants.value)) {
    setFieldValue('variants', JSON.stringify(variants.value));
  }
});
onMounted(() => {
  store.commit('productStore/removeAll');
});
</script>

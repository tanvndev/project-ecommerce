<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-24 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit" v-if="data">
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
                  <a-col :span="24">
                    <InputComponent
                      name="excerpt"
                      typeInput="textarea"
                      placeholder="Mô tả ngắn của sản phẩm"
                      label="Mô tả ngắn của sản phẩm"
                      :showAI="true"
                    />
                  </a-col>
                  <a-col span="24" class="-mt-5">
                    <SelectComponent
                      name="shipping_ids"
                      label="Lớp giao hàng"
                      mode="multiple"
                      placeholder="Chọn lớp giao hàng"
                      :options="state.paymentMethods"
                    />
                  </a-col>
                </a-row>
              </a-card>
            </a-col>
            <!-- Sidebar right -->
            <a-col :span="7">
              <a-card class="mt-3" title="Thương hiệu">
                <SelectComponent
                  name="brand_id"
                  :options="state.brands"
                  placeholder="Chọn thương hiệu sản phẩm"
                />
              </a-card>

              <a-card class="mt-3" title="Nhóm sản phẩm">
                <TreeSelectComponent
                  name="product_catalogue_id"
                  :treeDefaultExpandAll="true"
                  :options="state.productCatalogues"
                  placeholder="Chọn nhóm sản phẩm"
                />
              </a-card>
              <a-card class="mt-3" title="Loại sản phẩm">
                <div>
                  <SelectComponent
                    name="product_type"
                    label="Loại sản phẩm"
                    :required="true"
                    :options="PRODUCT_TYPE"
                    :showSearch="false"
                    tooltip-text="Lưu ý bạn chỉ có thể cập nhập từ sản phẩm ''đơn sản'' sang sản phẩm ''biến thể'' không để cập nhập ngược lại, các phiên bản cũ sẽ bị xóa vĩnh viễn nếu bạn cập nhập từ sản phẩm ''đơn giản'' sang ''biến thể''."
                    placeholder="Chọn loại sản phẩm"
                    @on-change="handleProductType"
                  />
                </div>
              </a-card>
            </a-col>

            <a-col span="24" class="mt-3">
              <!-- Main data -->
              <ProductVariantView
                :variants="state.variants"
                :attribute-data="state.attributeData"
                :product-type="state.productType"
                :attribute-enable-old="state.attributeEnableOld"
                :attribute-enable-ids="state.attributeEnableIds"
                @onReload="fetchOne"
              />
            </a-col>

            <!-- Upsell -->

            <a-col span="24" class="mt-3">
              <a-card title="Sản phẩm được liên kết">
                <UpsellView :old-value="state.upsellIds" />
              </a-card>
            </a-col>

            <a-col span="9">
              <!-- ProductAttribute -->
              <ProductAttributeView
                :attribute-data="state.attributeData"
                :attribute-not-enable-old="state.attributeNotEnableOld"
                :attribute-not-enable-ids="state.attributeNotEnableIds"
              />
            </a-col>

            <!-- SEO -->
            <a-col span="15">
              <SEOComponent />
            </a-col>
          </a-row>

          <div class="fixed bottom-0 right-[19px] p-10">
            <a-button html-type="submit" type="primary" size="large">
              <i class="fas fa-save mr-2"></i>
              <span>Lưu thông tin</span>
            </a-button>
          </div>
        </form>
        <div class="mb-24 mt-10" v-else>
          <a-row :gutter="[20, 20]">
            <a-col span="17">
              <a-skeleton active />
              <a-skeleton active />
            </a-col>
            <a-col span="7">
              <a-skeleton active />
              <a-skeleton active />
            </a-col>
          </a-row>
        </div>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import {
  AleartError,
  BreadcrumbComponent,
  EditorComponent,
  InputComponent,
  MasterLayout,
  SEOComponent,
  SelectComponent,
  TreeSelectComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { PRODUCT_TYPE } from '@/static/constants';
import { formatDataToSelect, formatDataToTreeSelect, formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import _ from 'lodash';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive, watch } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';
import ProductAttributeView from './partials/ProductAttributeView.vue';
import ProductVariantView from './partials/ProductVariantView.vue';
import UpsellView from './partials/UpsellView.vue';

// STATE
const state = reactive({
  endpoint: 'products',
  pageTitle: 'Cập nhập sản phẩm.',
  error: {},
  productCatalogues: [],
  brands: [],
  variants: [],
  attributeData: [],
  attributeNotEnableOld: [],
  attributeNotEnableIds: [],
  attributeEnableOld: [],
  attributeEnableIds: [],
  productType: '',
  upsellIds: [],
  paymentMethods: []
});

const store = useStore();
const { getOne, getAll, update, messages, data } = useCRUD();

const id = computed(() => router.currentRoute.value.params.id || null);
const { handleSubmit, setValues, errors } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tiêu đề sản phẩm không được để trống.'),
    product_type: yup.string().required('Loại sản phẩm không được để trống.'),
    product_catalogue_id: yup
      .mixed()
      .test(
        'is-string-or-array',
        'Vui lòng chọn nhóm sản phẩm.',
        (value) => typeof value === 'string' || (Array.isArray(value) && !_.isEmpty(value))
      )
      .required('Vui lòng chọn nhóm sản phẩm.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  state.error = {};
  const response = await update(state.endpoint, id.value, values);
  if (!response) {
    return (state.error = formatMessages(messages.value));
  }

  state.error = {};
  message.success(messages.value);
  store.commit('productStore/removeAll');
  //   router.push({ name: 'product.index' });
});

watch(errors, (newErrors) => {
  state.error = newErrors;
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  const {
    product_type,
    name,
    description,
    excerpt,
    meta_title,
    meta_description,
    canonical,
    brand_id,
    product_catalogue_ids,
    attribute_not_enabled_ids,
    attribute_not_enabled,
    attribute_enabled,
    attribute_enabled_ids,
    upsell_ids,
    shipping_ids
  } = data.value;

  setValues({
    name,
    description,
    excerpt,
    meta_title,
    meta_description,
    canonical,
    product_type,
    brand_id,
    product_catalogue_id: product_catalogue_ids,
    attribute_id: attribute_not_enabled_ids,
    upsell_ids,
    shipping_ids
  });

  if (!_.isEmpty(data.value?.variants)) {
    state.variants = data.value?.variants;
  }

  if (!_.isEmpty(data.value?.upsell_ids)) {
    state.upsellIds = data.value?.upsell_ids;
  }

  if (!_.isEmpty(product_type)) {
    state.productType = product_type;
    store.commit('productStore/setProductType', product_type);
  }

  if (!_.isEmpty(attribute_not_enabled_ids)) {
    state.attributeNotEnableIds = attribute_not_enabled_ids;
  }

  if (!_.isEmpty(attribute_not_enabled)) {
    state.attributeNotEnableOld = attribute_not_enabled;
  }

  if (!_.isEmpty(attribute_enabled)) {
    state.attributeEnableOld = attribute_enabled;
  }

  if (!_.isEmpty(attribute_enabled_ids)) {
    state.attributeEnableIds = attribute_enabled_ids;
  }
};

// LAY RA TOAN BO PRODUCT CATALOGUE
const getProductCatalogues = async () => {
  await getAll('products/catalogues');
  state.productCatalogues = formatDataToTreeSelect(data.value);
};

// LAY RA TOAN BO BRAND
const getBrands = async () => {
  await getAll('brands');
  state.brands = formatDataToSelect(data.value);
};

const handleProductType = (value) => {
  state.productType = value;
};

const getAttributes = async () => {
  await getAll('attributes');
  state.attributeData = data.value;
};

const getAllShippingMethods = async () => {
  await getAll('shipping-methods', { list: true });
  state.paymentMethods = formatDataToSelect(data.value);
};

onMounted(async () => {
  getProductCatalogues();
  getBrands();
  getAttributes();
  getAllShippingMethods();
  if (id.value) {
    fetchOne();
  }
});
</script>

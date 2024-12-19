<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen pb-[100px]">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="24">
              <a-card class="mt-3" title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="12">
                    <InputComponent
                      name="name"
                      label="Tên widget"
                      :required="true"
                      placeholder="Tên widget"
                    />
                  </a-col>
                  <a-col :span="12">
                    <InputComponent
                      typeInput="textarea"
                      name="description"
                      label="Mô tả widget"
                      placeholder="Nhập mô tả cho widget"
                    />
                  </a-col>
                </a-row>
              </a-card>
            </a-col>

            <a-col :span="24">
              <a-card class="mt-3" title="Cấu hình nội dung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <SelectComponent
                      name="type"
                      label="Loại widget"
                      :required="true"
                      :options="WIDGET_TYPE"
                      @on-change="handleType"
                      placeholder="Chọn kiểu widget"
                    />
                  </a-col>
                </a-row>
                <a-row :gutter="[16, 16]" class="mt-6" v-if="state.type == 'product'">
                  <a-col span="24">
                    <SearchProductView :old-value="state.modelIds" />
                  </a-col>
                </a-row>

                <!-- Advertisement -->
                <AdvertisementView
                  v-if="state.type == 'advertisement'"
                  :advertisementBanners="state.advertisementBanners"
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
  AleartError,
  BreadcrumbComponent,
  InputComponent,
  MasterLayout,
  SelectComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { WIDGET_TYPE } from '@/static/constants';
import { formatMessages } from '@/utils/format';
import { handleBeforeUnload } from '@/utils/helpers';
import { useForm } from 'vee-validate';
import { computed, onMounted, onUnmounted, reactive } from 'vue';
import { useStore } from 'vuex';
import * as yup from 'yup';
import AdvertisementView from './partials/AdvertisementView.vue';
import SearchProductView from './partials/SearchProductView.vue';

// VARIABLES

const store = useStore();
const { getOne, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

// STATE
const state = reactive({
  endpoint: 'widgets',
  pageTitle: 'Thêm mới widget',
  errors: {},
  type: 'product',
  advertisementBanners: [],
  modelIds: []
});

const { handleSubmit, setValues, setFieldValue } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên widget không được để trống.'),
    type: yup.string().required('Loại widget không được để trống.')
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
  //   router.push({ name: 'brand.index' });
});

const handleType = (value) => {
  state.type = value;
};

const handleModel = (value) => {
  state.model = value;
};

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);

  const { name, description, type, model, model_ids, advertisement_banners } = data.value;

  setValues({
    name,
    description,
    type,
    model,
    model_ids
  });

  state.advertisementBanners = advertisement_banners;
  state.modelIds = model_ids;

  setOldValueForAdvertisementBanner();
  handleType(type);
  handleModel(model);
};

const setOldValueForAdvertisementBanner = () => {
  if (state.advertisementBanners.length > 0) {
    state.advertisementBanners.forEach((advertisementBanner, index) => {
      setFieldValue(`alt[][${index}]`, advertisementBanner.alt || '');
      setFieldValue(`image[][${index}]`, advertisementBanner.image || '');
      setFieldValue(`url[][${index}]`, advertisementBanner.url || '');
      setFieldValue(`content[][${index}]`, advertisementBanner.content || '');
      setFieldValue(`button[][${index}]`, advertisementBanner.button || '');
    });
  }
};

onMounted(() => {
  window.addEventListener('beforeunload', handleBeforeUnload);
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập widget.';
    fetchOne();
  }
});

onUnmounted(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>

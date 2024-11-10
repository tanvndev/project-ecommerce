<template>
  <MasterLayout>
    <template #template>
      <div class="mb-4 px-4">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <!-- {{ errors }} -->
          <a-row :gutter="16">
            <AleartError :errors="state.errors" />
            <!-- Left Column (Cấu hình cơ bản, Cấu hình nâng cao, Shortcode) -->
            <a-col :span="8">
              <!-- Basic Configuration Section -->
              <a-card title="Cấu hình cơ bản">
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <InputComponent
                      label="Tiêu đề"
                      :required="true"
                      name="name"
                      placeholder="Tiêu đề "
                    />
                  </a-col>

                  <a-col :span="24">
                    <InputComponent label="Code" :required="true" name="code" placeholder="Mã trình chiếu" />
                  </a-col>

                  <a-col :span="24">
                    <InputComponent
                      label="Chiều rộng"
                      name="width"
                      placeholder="Chiều rộng"
                      suffix="px"
                    />
                  </a-col>

                  <a-col :span="24">
                    <InputComponent
                      label="Chiều dài"
                      name="height"
                      placeholder="Chiều dài"
                      suffix="px"
                    />
                  </a-col>

                  <a-col :span="24">
                    <SelectComponent
                      name="effect"
                      label="Hiệu ứng"
                      :options="EFFECT"
                      :showSearch="false"
                      placeholder="Chọn hiệu ứng"
                    />
                  </a-col>

                  <a-col :span="24">
                    <a-form-item label="Mũi tên">
                      <SwitchComponent name="showArrow" />
                    </a-form-item>
                  </a-col>
                </a-row>

                <a-form-item label="Điều hướng">
                  <a-radio-group>
                    <RadioComponent
                      name="navigation"
                      :options="[
                        { label: 'Dấu chấm', value: 'dots' },
                        { label: 'Ảnh thumbnails', value: 'thumbnails' }
                      ]"
                      option-type="button"
                      old-value="unlimited"
                    />
                  </a-radio-group>
                </a-form-item>
              </a-card>

              <!-- Advanced Configuration Section -->
              <a-card title="Cấu hình nâng cao" bordered style="margin-top: 16px">
                <a-form-item label="Tự động chạy">
                  <SwitchComponent name="autoPlay" />
                </a-form-item>

                <a-form-item label="Dừng khi di chuột">
                  <SwitchComponent name="pauseOnHover" />
                </a-form-item>

                <InputComponent
                  label="Chuyển cảnh"
                  name="transitionSpeed"
                  placeholder="Chuyển cảnh"
                  suffix="ms"
                />

                <div class="mt-4">
                  <InputComponent
                    label="Tốc độ hiệu ứng"
                    name="effectSpeed"
                    placeholder="Tốc độ hiệu ứng"
                    suffix="ms"
                  />
                </div>
              </a-card>
            </a-col>

            <!-- Right Column (Danh sách slides) -->
            <a-col :span="16">
              <a-card title="Danh sách trình chiếu" bordered>
                <template #extra>
                  <a-button type="dashed" @click="addSlide">Thêm slide</a-button>
                </template>

                <VueDraggable ref="el" v-model="slides" group="slides" @end="onEnd">
                  <div v-for="(element, index) in slides" :key="element.id">
                    <div class="slide-block">
                      <a-row align="top" class="slide-row">
                        <a-col :span="5" class="d-flex align-items-center justify-center">
                          <InputFinderComponent :name="`items[${index}]image`" />
                        </a-col>
                        <a-col :span="17" class="d-flex align-items-center justify-center">
                          <a-form-item>
                            <InputComponent
                              label=""
                              :name="`items[${index}]description`"
                              placeholder="Mô tả"
                            />
                          </a-form-item>
                          <div class="hidden">
                            <InputComponent
                              label=""
                              :name="`items[${index}]id`"
                              placeholder="Mô tả"
                              :oldValue="index + 1"
                            />
                          </div>

                          <a-form-item class="mb-0">
                            <InputComponent
                              label=""
                              :name="`items[${index}]url`"
                              placeholder="URL"
                            />
                          </a-form-item>
                        </a-col>

                        <a-col
                          :span="1"
                          class="d-flex align-items-center ms-7 justify-center text-center"
                        >
                          <button
                            type="button"
                            @click="removeSlide(index)"
                            class="rounded bg-red-600 px-2 py-1"
                          >
                            <i class="fas fa-trash text-white"></i>
                          </button>
                        </a-col>
                      </a-row>
                    </div>
                  </div>
                </VueDraggable>

                <!-- <draggable v-model="slides"> -->

                <!-- </draggable> -->
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
  RadioComponent,
  SelectComponent,
  SwitchComponent
} from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { EFFECT } from '@/static/constants';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive, ref } from 'vue';
import { VueDraggable } from 'vue-draggable-plus';
import { validationSchema } from './validationSchema';

const { getOne, create, update, messages, data } = useCRUD();
const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, errors, setValues, setFieldValue } = useForm({
  validationSchema
});

const slides = ref([]);

const state = reactive({
  endpoint: 'sliders',
  pageTitle: 'Thêm mới trình chiếu',
  errors: {},
  items: []
});

onMounted(() => {
  if (slides.value.length === 0) {
    addSlide();
  }

  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập trình chiếu.';
    fetchOne();
  }
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);

  setValues({
    name: data.value.name,
    code: data.value.code,
    width: data.value.setting.width,
    height: data.value.setting.height,
    effect: data.value.setting.effect,
    showArrow: data.value.setting.showArrow,
    navigation: data.value.setting.navigation,
    autoPlay: data.value.setting.autoPlay,
    pauseOnHover: data.value.setting.pauseOnHover,
    transitionSpeed: data.value.setting.transitionSpeed,
    effectSpeed: data.value.setting.effectSpeed
  });

  state.items = data.value.items;

  setOldValueItems();

  slides.value = data.value.items.map((item, index) => ({
    id: index + 1,
    image: item.image,
    description: item.description,
    url: item.url
  }));
};

const setOldValueItems = () => {
  if (state.items.length > 0) {
    state.items.forEach((item, index) => {
      setFieldValue(`items[${index}]image`, item.image || '');
      setFieldValue(`items[${index}]description`, item.description || '');
      setFieldValue(`items[${index}]url`, item.url || '');
    });
  }
};

const addSlide = () => {
  slides.value.push({
    id: slides.value.length + 1,
    image: '',
    description: '',
    url: ''
  });
};

const removeSlide = (index) => {
  slides.value.splice(index, 1);

  if (slides.value.length === 0) {
    addSlide();
  }
};

// Handle form submission
const onSubmit = handleSubmit(async (values) => {
  console.log(values);

  state.error = {};

  const response =
    id.value && id.value > 0
      ? await update(state.endpoint, id.value, values)
      : await create(state.endpoint, values);

  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  message.success(messages.value);
  state.errors = {};
  router.push({ name: 'slider.index' });
});
</script>

<style scoped>
.slide-block {
  border: 1px solid #e8e8e8;
  padding: 16px;
  margin-top: 16px;
  border-radius: 8px;
  background-color: #fff;
}

.status-text {
  font-size: 14px;
  margin-left: 10px;
}

.switch label {
  font-weight: bold;
  font-size: 16px;
}
</style>

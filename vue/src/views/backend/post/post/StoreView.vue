<template>
  <MasterLayout>
    <template #template>
      <div class="mx-10 mb-5 min-h-screen">
        <BreadcrumbComponent :titlePage="state.pageTitle" @on-save="onSubmit" />
        <form @submit.prevent="onSubmit">
          <a-row :gutter="16">
            <a-col :span="17">
              <a-card class="mt-3" title="Thông tin chung">
                <AleartError :errors="state.errors" />
                <a-row :gutter="[16, 16]">
                  <a-col :span="24">
                    <InputComponent
                      name="name"
                      label="Tiêu đề bài viết"
                      :required="true"
                      :showAI="true"
                      placeholder="Tiêu đề bài viết"
                    />
                  </a-col>
                  <a-col :span="24">
                    <InputComponent
                      typeInput="textarea"
                      name="description"
                      label="Mô tả bài viết"
                      :showAI="true"
                      placeholder="Tạo mô tả cho bài viết"
                    />
                  </a-col>

                  <a-col :span="24" class="-mt-3">
                    <EditorComponent
                      typeInput="textarea"
                      name="content"
                      :required="true"
                      label="Nội dung bài viết"
                      :showAI="true"
                      placeholder="Tạo nội dung cho bài viết"
                    />
                  </a-col>
                </a-row>
              </a-card>
              <!-- SEO -->
              <SEOComponent />
            </a-col>

            <a-col :span="6">
              <a-card class="mt-3" title="Ảnh bài viết">
                <InputFinderComponent name="image" />
              </a-card>

              <a-card class="mt-3" title="Nhóm bài viết">
                <SelectComponent
                  name="post_catalogue_id"
                  :options="[]"
                  placeholder="Chọn nhóm bài viết"
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
  EditorComponent,
  InputComponent,
  InputFinderComponent,
  MasterLayout,
  SelectComponent,
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
  endpoint: 'posts',
  pageTitle: 'Thêm mới bài viết',
  errors: {}
});

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    name: yup.string().required('Tên bài viết không được để trống.'),
    image: yup.string().required('Vui lòng chọn ảnh bài viết.'),
    content: yup.string().required('Nội dung bài viết không được để trống.')
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
  router.push({ name: 'post.index' });
});

const fetchOne = async () => {
  await getOne(state.endpoint, id.value);
  const { name, description, canonical, image, meta_title, meta_description, content } = data.value;

  setValues({
    name,
    description,
    canonical,
    meta_title,
    meta_description,
    image,
    content
  });
};

onMounted(() => {
  if (id.value && id.value > 0) {
    state.pageTitle = 'Cập nhập bài viết';
    fetchOne();
  }
});
</script>

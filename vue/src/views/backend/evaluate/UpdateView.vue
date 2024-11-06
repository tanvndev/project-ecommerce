<template>
  <MasterLayout>
    <template #template>
      <div class="content-wrapper">
        <a-row :gutter="16">
          <!-- Left side: User review information -->
          <a-col :span="16">
            <a-card>
              <a-row :gutter="16" align="middle">
                <!-- Star Rating and Reviewer Name -->
                <a-col :span="12">
                  <div class="items-cente flex flex-col">
                    <div class="flex">
                      <span
                        v-for="n in 5"
                        :key="n"
                        :class="n <= review.rating ? 'text-yellow-500' : 'text-gray-300'"
                        class="text-xl"
                      >
                        ★
                      </span>
                    </div>

                    <div class="mt-1">
                      <span class="me-2"
                        ><strong>{{ review.userName }}</strong></span
                      >
                      <span>
                        <i class="fas fa-clock text-gray-400"></i>
                        {{ review.date }}</span
                      >
                    </div>
                  </div>
                </a-col>

                <!-- Publish Status -->
                <a-col :span="12" class="text-right">
                  <a-tag :color="getStatusColor(review.isPublished)">
                    {{ review.isPublished !== 1 ? 'Hiển thị' : 'Không hiển thị' }}
                  </a-tag>
                </a-col>
              </a-row>

              <!-- User's Review Text -->
              <a-row :gutter="16">
                <a-divider />
                <a-col :span="24" >
                  <p class="text-gray-700 mb-4">{{ review.comment }}</p>
                </a-col>
                <!-- Review Images -->
                <a-col :span="24" v-if="review.images">
                  <a-space>
                    <img
                      v-for="(image, index) in JSON.parse(review.images)"
                      :key="index"
                      :src="image"
                      alt="review image"
                      width="120"
                      height="120"
                      class="rounded-lg border bg-[#f7f8fb] object-cover p-[2px] shadow-sm"
                    />
                  </a-space>
                </a-col>
              </a-row>
            </a-card>

            <div class="mt-4">
              <a-card>
                <a-row class="border-bottom" type="flex" justify="space-between" align="middle">
                  <a-col :span="24" class="mb-2">
                    <p><strong>Trả lời bình luận:</strong></p>
                  </a-col>
                </a-row>

                <!-- Reply to Review Form -->
                <a-row class="mt-2">
                  <a-col :span="24">
                    <form @submit.prevent="onSubmit">
                      <a-form-item>
                        <EditorComponent
                          name="reply"
                          placeholder="Viết câu trả lời của bạn ở đây..."
                        />
                      </a-form-item>
                      <a-form-item class="text-right">
                        <a-button type="primary" size="large" html-type="submit">Trả lời</a-button>
                      </a-form-item>
                    </form>
                  </a-col>
                </a-row>
              </a-card>
            </div>
          </a-col>

          <!-- Right side: Product information -->
          <a-col :span="8">
            <a-card title="Thông tin sản phẩm">
              <a-row align="top">
                <!-- Left side: Product Image -->
                <a-col :span="7" class="text-center">
                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="w-[100px] rounded-lg bg-[#f7f8fb] object-cover p-[2px] shadow-sm"
                  />
                </a-col>

                <!-- Right side: Product Name and Rating (Name on top) -->
                <a-col :span="15">
                  <div>
                    <h3>{{ product.name }}</h3>
                    <a-rate v-model:value="product.average_rating" disabled /> ({{
                      product.rating_count
                    }})
                  </div>
                </a-col>
              </a-row>
            </a-card>
          </a-col>
        </a-row>
      </div>
    </template>
  </MasterLayout>
</template>

<script setup>
import { MasterLayout } from '@/components/backend';
import EditorComponent from '@/components/backend/includes/EditorComponent.vue';
import { useCRUD } from '@/composables';
import router from '@/router';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { computed, onMounted, reactive, ref } from 'vue';
import * as yup from 'yup';

const review = ref({});
const product = ref({});

// Reactive state for API endpoint
const state = reactive({
  endpoint: 'product-reviews/replies',
  replyExists: false // New state to track if reply exists
});

const { getOne, create, update, messages } = useCRUD();

const id = computed(() => router.currentRoute.value.params.id || null);

const { handleSubmit, setValues } = useForm({
  validationSchema: yup.object({
    reply: yup.string().required('Vui lòng nhập câu trả lời.')
  })
});

const onSubmit = handleSubmit(async (values) => {
  const response = state.replyExists
    ? await update(`${state.endpoint}`, id.value, { comment: values.reply })
    : await create(`${state.endpoint}/create`, { review_id: id.value, comment: values.reply });

  if (!response) {
    return (state.errors = formatMessages(messages.value));
  }

  message.success(messages.value);
  router.push({ name: 'evaluate.index' });
});

const fetchOne = async () => {
  const responseData = await getOne(state.endpoint, id.value);

  state.replyExists = responseData.replies && responseData.replies.length > 0;

  if (responseData.replies[0]?.comment) {
    setValues({
      reply: state.replyExists ? responseData.replies[0].comment : ''
    });
  }

  review.value = {
    rating: Number(responseData.rating),
    userName: responseData.fullname,
    status: responseData.publish,
    comment: responseData.comment,
    images: responseData.images,
    isPublished: responseData.publish,
    date: responseData.created_at
  };

  product.value = {
    image: responseData.product_image,
    name: responseData.product_name,
    average_rating: Number(responseData.average_rating),
    rating_count: responseData.rating_count
  };
};

const getStatusColor = (publish) => {
  return publish !== 1 ? 'green' : 'red';
};

// On component mount, fetch review details
onMounted(() => {
  if (id.value && id.value > 0) {
    fetchOne();
  }
});
</script>

<style scoped>
.content-wrapper {
  padding: 16px;
}
</style>

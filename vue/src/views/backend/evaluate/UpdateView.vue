<template>
  <MasterLayout>
    <template #template>
      <div class="content-wrapper">
        <a-row :gutter="16">
          <!-- Left side: User review information -->
          <a-col :span="16">
            <a-card title="User Review" bordered>
              <a-row :gutter="16" align="middle">
                <!-- Star Rating and Reviewer Name -->
                <a-col :span="12">
                  <!-- <a-rate v-model="review.rating" disabled /> -->
                  <!-- <div class="flex"> -->
                    <span
                      v-for="n in 5"
                      :key="n"
                      :class="n <= review.rating ? 'text-yellow-500' : 'text-gray-300'"
                      class="text-xl"
                    >
                      ★
                    </span>
                  <!-- </div> -->

                  <span class="me-2 ml-4"
                    ><strong>{{ review.userName }}</strong></span
                  >
                  <small>{{ review.date }}</small>
                </a-col>

                <!-- Publish Status -->
                <a-col :span="12" class="text-right">
                  <a-tag :color="getStatusColor(review.isPublished)">
                    {{ review.isPublished !== 1 ? 'Unpublish' : 'Publish' }}
                  </a-tag>
                </a-col>
              </a-row>

              <!-- User's Review Text -->
              <a-row :gutter="16" class="mt-2">
                <a-col :span="24">
                  <p>{{ review.comment }}</p>
                </a-col>
                <!-- Review Images -->
                <a-col :span="24" class="mt-2">
                  <a-space>
                    <img
                      v-for="(image, index) in review.images"
                      :key="index"
                      :src="image"
                      alt="review image"
                      style="width: 80px; height: 80px; object-fit: cover"
                    />
                  </a-space>
                </a-col>
              </a-row>

              <!-- Action Buttons: Delete & Unpublish -->
              <a-row justify="end" class="mt-2">
                <a-col>
                  <a-button class="delete-button mr-2" type="primary" danger @click="deleteReview">
                    Delete
                  </a-button>
                </a-col>
              </a-row>
            </a-card>

            <div class="mt-4">
              <a-card bordered>
                <a-row class="border-bottom" type="flex" justify="space-between" align="middle">
                  <a-col :span="24" class="mb-2">
                    <p><strong>Reply to review:</strong></p>
                  </a-col>
                </a-row>

                <!-- Reply to Review Form -->
                <a-row class="mt-2">
                  <a-col :span="24">
                    <form @submit.prevent="onSubmit">
                      <a-form-item>
                        <InputComponent
                          typeInput="textarea"
                          name="reply"
                          label=""
                          placeholder="Viết câu trả lời của bạn ở đây..."
                        />
                      </a-form-item>
                      <a-form-item class="text-right">
                        <a-button type="primary" html-type="submit">Reply</a-button>
                      </a-form-item>
                    </form>
                  </a-col>
                </a-row>
              </a-card>
            </div>
          </a-col>

          <!-- Right side: Product information -->
          <a-col :span="8">
            <a-card title="Product Information" bordered>
              <a-row align="top">
                <!-- Left side: Product Image -->
                <a-col :span="8" class="text-center">
                  <img
                    :src="product.image"
                    alt="product image"
                    style="width: 100px; height: 100px; object-fit: cover"
                  />
                </a-col>

                <!-- Right side: Product Name and Rating (Name on top) -->
                <a-col :span="16">
                  <div>
                    <h3>{{ product.name }}</h3>
                    <a-rate v-model="product.avgRating" disabled /> ({{ product.avgRating }})
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
import { ref, reactive, computed, onMounted } from 'vue';
import { MasterLayout, InputComponent } from '@/components/backend';
import { useCRUD } from '@/composables';
import router from '@/router';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { formatMessages } from '@/utils/format';
import * as yup from 'yup';

const review = ref({});
const product = ref({});

// Reactive state for API endpoint
const state = reactive({
  endpoint: 'product-reviews/replies',
  replyExists: false // New state to track if reply exists
});

const { getOne, create, update , messages, data } = useCRUD();

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

  setValues({
    reply: state.replyExists ? responseData.replies[0].comment : ''
  });

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
    avgRating: Number(responseData.average_rating)
  };
};

const getStatusColor = (publish) => {
  return publish === 1 ? 'green' : 'red';
};

// Handle review deletion
const deleteReview = () => {
  console.log('Review deleted');
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
.mt-2 {
  margin-top: 8px;
}
.mt-4 {
  margin-top: 16px;
}
.text-center {
  text-align: center;
}
</style>

<template>
  <div class="w-full bg-white">
    <div class="flex items-center justify-between">
      <label :for="props.name" :class="props.labelClass"
        >{{ props.label }}
        <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
      </label>

      <div v-if="props.showAI">
        <a href="#" class="text-blue-500" @click.prevent="openModalAI = true">
          <i class="fas fa-robot mr-1"></i>
          <span> Tạo nội dung với AI </span>
        </a>
      </div>
    </div>

    <QuillEditor
      :name="props.name"
      :id="props.name"
      v-model:content="value"
      theme="snow"
      contentType="html"
      toolbar="full"
    />
    <span v-if="errorMessage" class="mt-[6px] block text-[12px] text-red-500">{{
      errorMessage
    }}</span>
  </div>

  <a-modal v-if="showAI" v-model:open="openModalAI" width="800px" style="top: 20px">
    <template #title>
      <h1 class="text-lg font-normal uppercase">Tạo nội dung với AI</h1>
    </template>
    <a-divider class="mb-0 mt-3" />

    <div class="py-5" v-if="!streamingContent">
      <div class="mb-4">
        <label for="industry" class="mb-1 block text-gray-700">Ngành hàng</label>
        <a-select
          id="industry"
          v-model:value="formData.industry"
          placeholder="Lựa chọn ngành hàng"
          size="large"
          style="width: 100%"
          :show-search="true"
          allow-clear
          :options="INDUSTRY"
        ></a-select>
      </div>

      <div class="mb-4">
        <label for="keywords" class="mb-1 block text-gray-700"
          >Từ khóa liên quan đến sản phẩm</label
        >
        <a-textarea
          id="keywords"
          v-model:value="formData.keywords"
          placeholder="Từ khóa liên quan đến sản phẩm"
          size="large"
          allow-clear
          auto-size
        />
      </div>

      <div class="mb-4">
        <label for="keywords" class="mb-1 block text-gray-700">Giọng văn bản</label>
        <a-radio-group v-model:value="formData.tone">
          <a-radio-button :value="toneAi.value" v-for="toneAi in TONE_AI" :key="toneAi.value">
            <i :class="toneAi.icon" class="mr-2"></i>
            <span>{{ toneAi.label }}</span>
          </a-radio-button>
        </a-radio-group>
      </div>
      <div class="mb-4">
        <label for="keywords" class="mb-1 block text-gray-700">Kiểu văn bản</label>
        <a-radio-group v-model:value="formData.textStyle">
          <a-radio-button
            :value="textStyle.value"
            v-for="textStyle in TEXT_STYLE_AI"
            :key="textStyle.value"
          >
            <i :class="textStyle.icon" class="mr-2"></i>
            <span> {{ textStyle.label }}</span>
          </a-radio-button>
        </a-radio-group>
      </div>

      <div class="mb-4">
        <label for="keywords" class="mb-1 block text-gray-700"
          >Yêu cầu khác (Khách hàng mục tiêu, ...)</label
        >
        <a-textarea
          id="keywords"
          v-model:value="formData.extra"
          placeholder="Hướng tới tệp khách hàng trẻ dưới 30 tuổi"
          size="large"
          allow-clear
          auto-size
        />
      </div>
    </div>

    <div v-if="streamingContent" class="text-generate-wrap">
      <div v-html="streamingContent"></div>
    </div>

    <template #footer>
      <a-button size="large" @click="resetContent" v-if="streamingContent"> Làm mới </a-button>

      <a-button size="large" @click="openModalAI = false" v-else> Hủy bỏ </a-button>
      <a-button
        size="large"
        type="primary"
        :loading="isLoading"
        v-if="streamingContent"
        @click="handleApplyGenerateAI"
      >
        Áp dụng ngay
      </a-button>
      <a-button type="primary" :loading="isLoading" size="large" v-else @click="handleGenerateAI">
        Tạo ngay
      </a-button>
    </template>
  </a-modal>
</template>

<script setup>
import { INDUSTRY, TEXT_STYLE_AI, TONE_AI } from '@/static/constants';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { message } from 'ant-design-vue';
import axios from 'axios';
import { marked } from 'marked';
import { useField } from 'vee-validate';
import { computed, reactive, ref } from 'vue';

const props = defineProps({
  required: {
    type: [Boolean, String],
    default: false
  },
  label: {
    type: String,
    required: true
  },
  labelClass: {
    type: String,
    default: 'mb-2 block text-sm font-medium text-gray-900'
  },
  name: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  showAI: {
    type: Boolean,
    default: false
  }
});

const openModalAI = ref(false);
const isLoading = ref(false);
const streamingContent = ref('');
const formData = reactive({
  industry: INDUSTRY[0].value,
  keywords: '',
  tone: TONE_AI[0].value,
  textStyle: TEXT_STYLE_AI[0].value,
  extra: ''
});

const prompt = computed(() => {
  const { industry, keywords, tone, textStyle, extra } = formData;
  const parts = [];

  if (industry) parts.push(`Ngành hàng: ${industry}`);
  if (keywords) parts.push(`Từ khóa chính: ${keywords}`);
  if (tone) parts.push(`Giọng văn bản: ${tone}`);
  if (textStyle) parts.push(`Kiểu văn bản: ${textStyle}`);
  if (extra) parts.push(`Yêu cầu khác: ${extra}`);

  return `Tạo nội dung SEO cho trang web dựa trên thông tin sau:
    ${parts.join('\n')}

    Yêu cầu cụ thể:
    1. Tạo một tiêu đề hấp dẫn (H1) sử dụng từ khóa chính.
    2. Viết một đoạn mô tả meta ngắn gọn (150-160 ký tự) chứa từ khóa.
    3. Tạo cấu trúc nội dung với các tiêu đề phụ (H2, H3) phù hợp.
    4. Viết nội dung chi tiết cho mỗi phần, tối thiểu 300 từ.
    5. Sử dụng từ khóa chính và các biến thể một cách tự nhiên trong nội dung.
    6. Thêm một đoạn kết luận ngắn gọn.
    7. Đề xuất 2-3 internal linking có liên quan.

    Đảm bảo nội dung:
    - Tuân thủ giọng văn và kiểu văn bản đã chỉ định.
    - Cung cấp thông tin có giá trị và phù hợp với ngành hàng.
    - Tối ưu hóa cho SEO nhưng vẫn đọc tự nhiên và hấp dẫn.
    - Đáp ứng các yêu cầu khác đã nêu (nếu có).
    - Tên thương hiệu Wolmart

    Vui lòng tạo nội dung có cấu trúc rõ ràng, dễ đọc và tối ưu cho cả người dùng lẫn công cụ tìm kiếm.`;
});

// Tạo field với VeeValidate
const { value, errorMessage } = useField(props.name);

const handleGenerateAI = async () => {
  const keyApi = import.meta.env.VITE_GEMINI_API_KEY;
  const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=${keyApi}`;

  if (!prompt.value || prompt.value.length === 0) {
    return message.warn('Vui lòng không để trống.');
  }

  try {
    isLoading.value = true;
    streamingContent.value = ''; // Reset content before starting

    const content = {
      contents: [{ parts: [{ text: prompt.value }] }]
    };

    const response = await axios.post(endpoint, content);
    const result = response.data.candidates[0].content.parts[0].text;

    const htmlContent = marked(result);

    await typeWriterEffect(htmlContent);
  } catch (error) {
    console.error('Error:', error);
    message.error('Có lỗi xin vui lòng thử lại.');
  } finally {
    isLoading.value = false;
  }
};

const typeWriterEffect = async (html) => {
  let index = 0;
  while (index < html.length) {
    streamingContent.value += html[index];
    index++;
    await delay(5);
  }
};

const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const handleApplyGenerateAI = () => {
  value.value = streamingContent.value
    .replace(/\n/g, ' ') // Thay thế dấu xuống dòng bằng khoảng trắng
    .replace(/\s+/g, ' ') // Thay thế nhiều khoảng trắng liên tiếp bằng một khoảng trắng
    .replace(/>\s+</g, '><')
    .trim(); // Loại bỏ khoảng trắng ở đầu và cuối

  openModalAI.value = false;
};

const resetContent = () => {
  streamingContent.value = '';
  formData.value.keywords = '';
  formData.value.extra = '';
};
</script>

<style scoped>
.ql-editor {
  min-height: 200px;
  height: 500px;
}
ol,
ul,
menu {
  /* list-style: none; */
  margin: 0;
  padding: 0;
  margin-left: 30px;
}

.text-generate-wrap {
  position: relative;
  max-height: 755px;
  overflow-y: auto;
  background-color: #f3f5f7;
  border-radius: 10px;
  padding: 20px;
}
</style>

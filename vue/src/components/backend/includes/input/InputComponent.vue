<template>
  <div class="flex items-center justify-between">
    <label v-if="props.label" :for="props.name" :class="props.labelClass"
      >{{ props.label }}
      <span v-if="props.required" class="font-semibold text-red-500">(*)</span>
      <TooltipComponent v-if="props.tooltipText" :title="props.tooltipText" />
    </label>

    <div v-if="props.showGenerate">
      <a href="#" class="text-blue-500" @click.prevent="handleGenerate">Tạo mã tự động</a>
    </div>

    <div v-if="props.showAI">
      <a href="#" class="text-blue-500" @click.prevent="openModalAI = true">
        <i class="fas fa-robot mr-1"></i>
        <span> Tạo nội dung với AI </span>
      </a>
    </div>
  </div>
  <div>
    <!-- INPUT TEXT -->
    <a-input
      v-if="props.typeInput == 'text' && props.type != 'password'"
      v-model:value="value"
      :class="className"
      :id="props.name"
      :type="props.type"
      :placeholder="props.placeholder"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :disabled="props.disabled"
      :suffix="props.suffix"
    />

    <!-- INPUT PASSWORD -->
    <a-input-password
      v-if="props.typeInput == 'text' && props.type == 'password'"
      v-model:value="value"
      :class="className"
      :id="props.name"
      :type="props.type"
      :placeholder="props.placeholder"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :disabled="props.disabled"
    />

    <!-- INPUT TEXTAREA -->
    <a-textarea
      v-if="props.typeInput == 'textarea'"
      v-model:value="value"
      :class="className"
      :id="props.name"
      :type="props.type"
      :placeholder="props.placeholder"
      :status="errorMessage ? 'error' : ''"
      :size="props.size"
      :allowClear="true"
      :auto-size="true"
      :show-count="props.showCount"
      :maxlength="props.maxlength"
      :disabled="props.disabled"
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
import { TooltipComponent } from '@/components/backend';
import { INDUSTRY, TEXT_STYLE_AI, TONE_AI } from '@/static/constants';
import { generateRandomString } from '@/utils/helpers';
import { message } from 'ant-design-vue';
import axios from 'axios';
import { marked } from 'marked';
import { useField } from 'vee-validate';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
  typeInput: {
    type: String,
    default: 'text'
  },
  required: {
    type: [Boolean, String],
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  labelClass: {
    type: String,
    default: 'mb-2 block text-sm font-medium text-gray-900'
  },
  name: {
    type: String,
    required: true
  },
  className: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  suffix: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'large'
  },
  maxlength: {
    type: [String, Number, Boolean],
    default: 0
  },
  oldValue: {
    type: [String, Boolean, Number],
    default: ''
  },

  tooltipText: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showGenerate: {
    type: Boolean,
    default: false
  },
  showAI: {
    type: Boolean,
    default: false
  },
  showCount: {
    type: Boolean,
    default: true
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

// Watch for changes in oldValue and set value accordingly
watch(
  () => props.oldValue,
  (newOldValue) => {
    if (newOldValue && newOldValue !== undefined && newOldValue !== value.value) {
      value.value = newOldValue;
    }
  },
  { immediate: true }
);

const handleGenerate = () => {
  let str = generateRandomString(12);
  str = str.toUpperCase();
  if (props.showGenerate) {
    value.value = str;
  }
};

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
  value.value = streamingContent.value;
  openModalAI.value = false;
};

const resetContent = () => {
  streamingContent.value = '';
  formData.value.keywords = '';
  formData.value.extra = '';
};
</script>

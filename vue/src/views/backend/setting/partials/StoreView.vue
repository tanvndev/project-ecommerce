<template>
  <form>
    <div class="flex items-center justify-between">
      <a-typography-title :level="4">Cấu hình chung</a-typography-title>
      <a-button type="primary" @click="onSubmit">
        <i class="far fa-save mr-2"></i>
        Lưu thông tin
      </a-button>
    </div>
    <AleartError :errors="errors" />

    <a-card class="mt-3" title="Thông tin cửa hàng">
      <a-row :gutter="[16, 16]">
        <a-col span="12">
          <InputComponent name="name" label="Tên cửa hàng" placeholder="Nhập tên cửa hàng" />
        </a-col>
        <a-col span="12">
          <InputComponent name="company_name" label="Tên công ty" placeholder="Nhập tên công ty" />
        </a-col>
        <a-col span="12">
          <InputComponent name="slogan" label="Slogan" placeholder="Slogan" />
        </a-col>
        <a-col span="12">
          <InputComponent name="copyright" label="Copyright" placeholder="Copyright" />
        </a-col>
        <a-col span="24">
          <EditorComponent
            name="short_description"
            label="Giới thiệu ngắn"
            placeholder="Nhập giới thiệu ngắn"
          />
        </a-col>
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">Logo</h2>
            <InputFinderComponent name="logo" />
          </div>
        </a-col>
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">Favicon</h2>
            <InputFinderComponent name="favicon" />
          </div>
        </a-col>
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">QR_APP</h2>
            <InputFinderComponent name="qr_app" />
          </div>
        </a-col>
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">Appstore App</h2>
            <InputFinderComponent name="appstore_image" />
          </div>
        </a-col>
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">Playstore App</h2>
            <InputFinderComponent name="playstore_image" />
          </div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="mt-3" title="Thông tin liên hệ">
      <a-row :gutter="[16, 16]">
        <a-col span="12">
          <InputComponent name="address" label="Địa chỉ" placeholder="Địa chỉ" />
        </a-col>
        <a-col span="12">
          <InputComponent name="email" label="Địa chỉ email" placeholder="Nhập địa chỉ email" />
        </a-col>
        <a-col span="12">
          <InputComponent name="hotline" label="Hotline" placeholder="Hotline" />
        </a-col>
        <a-col span="12">
          <InputComponent name="tax_code" label="Mã số thuế" placeholder="Mã số thuế" />
        </a-col>
        <a-col span="24">
          <InputComponent name="map" label="Bản đồ" typeInput="textarea" placeholder="Bản đồ" />
        </a-col>
      </a-row>
    </a-card>

    <SEOComponent />

    <a-card class="mt-3" title="Mạng xã hội">
      <a-row :gutter="[16, 16]">
        <a-col span="12">
          <InputComponent name="facebook" label="Facebook" placeholder="Facebook" />
        </a-col>
        <a-col span="12">
          <InputComponent name="twitter" label="Twitter" placeholder="Twitter" />
        </a-col>
        <a-col span="12">
          <InputComponent name="instagram" label="Instagram" placeholder="Instagram" />
        </a-col>
        <a-col span="12">
          <InputComponent name="youtube" label="Youtube" placeholder="Youtube" />
        </a-col>
        <a-col span="12">
          <InputComponent name="tiktok" label="Tiktok" placeholder="Tiktok" />
        </a-col>
        <a-col span="12">
          <InputComponent name="pinterest" label="Pinterest" placeholder="Pinterest" />
        </a-col>
        <a-col span="12">
          <InputComponent name="linkedin" label="Linkedin" placeholder="Linkedin" />
        </a-col>
        <a-col span="12">
          <InputComponent name="telegram" label="Telegram" placeholder="Telegram" />
        </a-col>
      </a-row>
    </a-card>

    <a-card class="mt-3" title="Tùy chỉnh">
      <a-row :gutter="[16, 16]">
        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <h2 class="-ml-[10px] text-sm">Ảnh nền trang chủ</h2>
            <InputFinderComponent name="bg_home" />
          </div>
        </a-col>

        <a-col span="4">
          <div class="flex flex-col items-center justify-center">
            <InputComponent
              name="snow_effect"
              label="Hiệu ứng tuyết"
              placeholder="Hiệu ứng tuyết (on/off)"
            />
          </div>
        </a-col>
      </a-row>
    </a-card>
  </form>
</template>

<script setup>
import {
  AleartError,
  EditorComponent,
  InputComponent,
  InputFinderComponent,
  SEOComponent
} from '@/components/backend';

import { useCRUD } from '@/composables';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import { onMounted, ref } from 'vue';
const { update, getAll, messages, data } = useCRUD();

const { handleSubmit, setValues, setFieldValue } = useForm();

const errors = ref({});

const onSubmit = handleSubmit(async (values) => {
  const response = await update('system-configs', null, values);

  if (!response) {
    return (errors.value = formatMessages(messages.value));
  }

  message.success(messages.value);
  getSystemConfig();
  errors.value = {};
});

const getSystemConfig = async () => {
  await getAll('system-configs');

  for (const key in data.value) {
    if (Object.prototype.hasOwnProperty.call(data.value, key)) {
      const value = data.value[key];
      setFieldValue(key, value);
    }
  }
};

onMounted(() => {
  getSystemConfig();
});
</script>

<template>
  <a-row>
    <a-col span="10">
      <div
        class="coming-soom-image-container flex h-full w-full items-center justify-center bg-[#0162e84d]"
      >
        <img :src="'/src/assets/images/loginpng.webp'" alt="" class="imig-fluid" />
      </div>
    </a-col>
    <a-col span="14">
      <div class="flex h-screen w-full flex-col items-center justify-center px-4">
        <div class="w-full max-w-sm space-y-8 text-gray-600">
          <div class="text-center">
            <img src="https://floatui.com/logo.svg" width="150" class="mx-auto" />
            <div class="mt-5 space-y-2">
              <h3 class="text-2xl font-bold text-gray-800 sm:text-3xl">Đăng nhập tài khoản</h3>
              <p>
                Bạn chưa có tài khoản?
                <RouterLink
                  :to="{ name: 'register' }"
                  class="font-medium text-blue-600 hover:text-blue-500"
                >
                  Đăng ký
                </RouterLink>
              </p>
            </div>
          </div>
          <form>
            <AleartError :errors="errors" />
            <div>
              <InputComponent
                label="Số điện thoại"
                name="phone"
                type="text"
                placeholder="Nhập số điện thoại của bạn"
              />
            </div>
            <div class="mt-5" v-if="showOtp">
              <InputComponent label="OTP" name="verification_code" type="text" placeholder="OTP" />
            </div>
            <div class="mt-2 flex items-center justify-between">
              <div></div>

              <div>
                <RouterLink
                  class="text-[13px] text-blue-600 hover:text-blue-500"
                  :to="{ name: 'login' }"
                  >Đăng nhập với mật khẩu</RouterLink
                >
              </div>
            </div>

            <!-- Thêm reCAPTCHA ở đây -->
            <RecaptchaComponent />

            <button
              v-if="!showOtp"
              @click.prevent="onSubmitOtp"
              type="submit"
              class="mt-4 w-full rounded-lg bg-primary-600 px-4 py-2 font-medium text-white duration-150 hover:bg-primary-500 active:bg-primary-600"
            >
              Tiếp tục
            </button>
            <button
              v-else
              @click.prevent="onSubmit"
              type="submit"
              class="mt-4 w-full rounded-lg bg-primary-600 px-4 py-2 font-medium text-white duration-150 hover:bg-primary-500 active:bg-primary-600"
            >
              Đăng nhập
            </button>
          </form>

          <div className="relative">
            <span className="block w-full h-px bg-gray-300"></span>
            <p
              className="inline-block w-fit text-sm bg-white px-2 absolute -top-2 inset-x-0 mx-auto"
            >
              Hoặc đăng nhập với
            </p>
          </div>
        </div>
      </div>
    </a-col>
  </a-row>
</template>
<script setup>
import { InputComponent, AleartError, RecaptchaComponent } from '@/components/backend';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import router from '@/router';
import { useStore } from 'vuex';
import { formatMessages } from '@/utils/format';
import { message } from 'ant-design-vue';
import { NUXT_URL } from '@/static/constants';
import axios from '@/configs/axios';

const store = useStore();
const errors = ref({});
const showOtp = ref(false);

// VALIDATION
const { handleSubmit } = useForm({
  validationSchema: yup.object({
    phone: yup
      .string()
      .required('Số điện thoại không được để trống.')
      .matches(/(0)[0-9]{9}/, 'Số điện thoại không đúng định dạng.')
  })
});

const onSubmitOtp = handleSubmit(async (values) => {
  //   eslint-disable-next-line no-undef
  const recaptchaResponse = grecaptcha.getResponse();

  if (!recaptchaResponse) {
    return message.error('Vui lòng xác nhận bạn không phải là robot.');
  }

  errors.value = {};
  try {
    const response = await axios.post(`/auth/send-verification-code`, {
      ...values
    });

    if (response.status == 'success') {
      message.success(response.messages);
      showOtp.value = true;
    }
  } catch (error) {
    message.error(error?.response?.data?.messages || 'Thao tác thất bại');
  }
});

// SUBMIT FORM HANDLE
const onSubmit = handleSubmit(async (values) => {
  errors.value = {};

  //   eslint-disable-next-line no-undef
  const recaptchaResponse = grecaptcha.getResponse();

  if (!recaptchaResponse) {
    showOtp.value = false;
    return message.error('Vui lòng xác nhận bạn không phải là robot.');
  }
  const formData = {
    ...values,
    'g-recaptcha-response': recaptchaResponse
  };

  await store.dispatch('authStore/loginOtp', formData);
  const authState = store.state.authStore;
  if (!authState.status.loggedIn) {
    showOtp.value = false;
    return (errors.value = formatMessages(authState.messages));
  }

  message.success('Đăng nhập thành công.');
  if (authState.user?.user_catalogue === 'admin') {
    return router.push({ name: 'dashboard' });
  }
  window.location.href = NUXT_URL;
});
</script>

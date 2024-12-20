<script setup>
useSeoMeta({
  title: 'Hồ sơ',
  ogTitle: 'Hồ sơ',
  description: 'Hồ sơ.',
  ogDescription: 'Hồ sơ.',
})
import _ from 'lodash'
import { useForm } from 'vee-validate'

definePageMeta({
  middleware: ['is-logged-in-middleware'],
})

const authStore = useAuthStore()
const { $axios, $authService } = useNuxtApp()
const { handleSubmit } = useForm()

const isOpenVerify = ref(false)
const otp = ref(null)
const resendOtp = ref({})
const user = computed(() => authStore.getUser)
const description = ref('')
const errors = ref({})
const isUpdateProfile = ref(false)
const updateField = ref('fullname')

const resendTime = 90
let countdownInterval

const closeEditInfo = () => {
  isOpenVerify.value = false
  otp.value = null
}

const openEditInfo = async (field) => {
  const text = `
        Chúng tôi đã gửi mã xác minh đến địa chỉ <b>${field == 'email' ? user.value?.hint_email : user.value?.hint_phone}</b>.
            <br />
        Vui lòng kiểm tra ${field == 'email' ? 'email' : 'điện thoại'} của bạn và dán mã vào dưới đây.
    `
  description.value = text

  updateField.value = field
  isOpenVerify.value = true
  otp.value = null

  if (!_.isEmpty(resendOtp.value)) {
    return
  }

  await sendOpt()

  countdown(resendTime)
}

const sendOpt = async () => {
  try {
    const response = await $axios.post(`/auth/send-verification-code`)

    setTimeout(() => {
      if (response.status == 'success') {
        return toast(response.messages)
      }
    }, 1000)
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  }
}

const handleVerifyOtp = async () => {
  if (!otp.value || otp.value.length != 6) {
    return (errors.value = {
      otp: 'Vui lòng điền đầy đủ 6 chuỗi',
    })
  }
  errors.value = {}

  try {
    const response = await $axios.post(`/auth/verify-code`, {
      verification_code: otp.value,
    })

    if (response.status == 'success') {
      toast(response.messages)
      isUpdateProfile.value = true
    }
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  }
  isOpenVerify.value = false
}

const handleResendOtp = async () => {
  if (!_.isEmpty(resendOtp.value)) {
    return
  }

  await sendOpt()

  countdown(resendTime)
}

const onSubmit = handleSubmit(async (values) => {
  try {
    const response = await $axios.post(
      `users/update/profile?_method=PUT`,
      values
    )

    if (response.status == 'success') {
      toast(response.messages)

      const user = await $authService.me()
      authStore.setUser(user.data)
    }
  } catch (error) {
    toast(
      formatMessages(error?.response?.data?.messages) || 'Thao tác thất bại',
      'error'
    )
  } finally {
    isUpdateProfile.value = false
  }
})

const countdown = (seconds) => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }

  return new Promise((resolve) => {
    let remainingSeconds = seconds

    countdownInterval = setInterval(() => {
      if (remainingSeconds <= 0) {
        clearInterval(countdownInterval)
        countdownInterval = null
        resolve({
          days: formatTime(0),
          hours: formatTime(0),
          minutes: formatTime(0),
          seconds: formatTime(0),
        })
        return (resendOtp.value = {})
      }

      const days = Math.floor(remainingSeconds / 86400)
      const hours = Math.floor((remainingSeconds % 86400) / 3600)
      const minutes = Math.floor((remainingSeconds % 3600) / 60)
      const secs = remainingSeconds % 60

      resendOtp.value = {
        days: formatTime(days),
        hours: formatTime(hours),
        minutes: formatTime(minutes),
        seconds: formatTime(secs),
      }

      remainingSeconds--
    }, 1000)
  })
}
</script>

<template>
  <div class="page-content pt-2 mt-6">
    <div class="container">
      <div class="tab tab-vertical row gutter">
        <div class="col-lg-3">
          <UserSidebar />
        </div>
        <div class="col-lg-9">
          <v-card
            class="mx-auto pa-2"
            title="Hồ sơ của tôi"
            subtitle="Quản lý thông tin hồ sơ để bảo mật tài khoản"
          >
            <v-divider class="border-opacity-100"></v-divider>

            <v-row class="mt-4 px-2">
              <v-col cols="12" md="8">
                <v-row gutter="10">
                  <v-col cols="12" md="10">
                    <v-text-field
                      :model-value="user?.fullname"
                      label="Họ và tên"
                      hide-details
                      disabled
                      variant="underlined"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="2">
                    <v-btn
                      variant="tonal"
                      size="x-small"
                      icon="mdi-pencil"
                      @click="
                        () => {
                          updateField = 'fullname'
                          isUpdateProfile = true
                        }
                      "
                    ></v-btn>
                  </v-col>

                  <v-col cols="12" md="10">
                    <v-text-field
                      :model-value="user?.birthday"
                      label="Ngày sinh"
                      hide-details
                      disabled
                      type="date"
                      variant="underlined"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="2">
                    <v-btn
                      variant="tonal"
                      size="x-small"
                      icon="mdi-pencil"
                      @click="
                        () => {
                          updateField = 'birthday'
                          isUpdateProfile = true
                        }
                      "
                    ></v-btn>
                  </v-col>

                  <v-col cols="12" md="10">
                    <v-text-field
                      :model-value="user?.hint_email"
                      label="Địa chỉ email"
                      hide-details
                      disabled
                      variant="underlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-btn
                      variant="tonal"
                      size="x-small"
                      icon="mdi-pencil"
                      @click="openEditInfo('email')"
                    ></v-btn>
                  </v-col>
                  <v-col cols="12" md="10">
                    <v-text-field
                      :model-value="user?.hint_phone"
                      label="Số điện thoại"
                      hide-details
                      disabled
                      variant="underlined"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-btn
                      variant="tonal"
                      size="x-small"
                      icon="mdi-pencil"
                      @click="openEditInfo('phone')"
                    ></v-btn>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12" md="4">
                <div class="text-center">
                  <div class="text-center mb-4">
                    <v-avatar
                      :image="resizeImage(user?.image, 200)"
                      size="120"
                    ></v-avatar>
                  </div>

                  <v-btn> Chọn ảnh </v-btn>
                  <p class="text-body-2 mt-4">
                    Dung lượng file tối đa 1 MB Định dạng:.JPEG, .PNG
                  </p>
                </div>
              </v-col>
            </v-row>
          </v-card>

          <v-dialog
            v-model="isOpenVerify"
            max-width="450"
            persistent
            @click:outside="closeEditInfo"
          >
            <v-card
              class="py-6 px-4 text-center mx-auto relative"
              elevation="12"
              max-width="450"
              width="100%"
            >
              <h3 class="text-h6 mb-4">Xác nhận tài khoản</h3>

              <div class="text-body-2" v-html="description"></div>

              <v-sheet color="surface">
                <v-otp-input
                  v-model="otp"
                  :error="!_.isEmpty(errors.otp)"
                ></v-otp-input>
              </v-sheet>

              <v-btn
                class="my-4 mx-auto"
                color="#336699"
                height="40"
                text="Xác nhận"
                variant="flat"
                width="70%"
                :disabled="!otp || otp.length != 6"
                @click="handleVerifyOtp"
              ></v-btn>

              <div class="text-caption">
                Không nhận được mã?
                <span class="mr-1 fw-bold" v-if="!_.isEmpty(resendOtp)">
                  {{ resendOtp.minutes }}:{{ resendOtp.seconds }}
                </span>
                <a v-else href="#" @click.prevent="handleResendOtp">Gửi lại</a>
              </div>
            </v-card>
          </v-dialog>

          <v-dialog v-model="isUpdateProfile" max-width="450" persistent>
            <v-card
              class="py-6 px-4 text-center mx-auto relative"
              elevation="12"
              max-width="450"
              width="100%"
            >
              <form @submit.prevent="onSubmit">
                <h3 class="text-h6 mb-4">Cập nhập thông tin</h3>

                <v-sheet color="surface my-2">
                  <IncludesInputComponent
                    name="email"
                    v-if="updateField == 'email'"
                    label="Địa chỉ email"
                  />
                  <IncludesInputComponent
                    name="phone"
                    v-if="updateField == 'phone'"
                    label="Số điện thoại"
                  />
                  <IncludesInputComponent
                    name="fullname"
                    v-if="updateField == 'fullname'"
                    label="Họ và tên"
                  />

                  <IncludesInputComponent
                    name="birthday"
                    type="date"
                    v-if="updateField == 'birthday'"
                    label="Ngày sinh"
                  />
                </v-sheet>

                <v-btn
                  class="mx-auto"
                  color="#336699"
                  height="40"
                  text="Xác nhận"
                  variant="flat"
                  width="70%"
                  type="submit"
                ></v-btn>
              </form>
            </v-card>
          </v-dialog>
        </div>
      </div>
    </div>
  </div>
</template>

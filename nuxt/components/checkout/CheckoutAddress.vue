<script setup>
import _ from 'lodash'

const emits = defineEmits(['onLocation'])
const { $axios, $authService } = useNuxtApp()
const authStore = useAuthStore()
const isLoggedIn = computed(() => authStore.isSignedIn)
const user = computed(() => authStore.getUser)
const provinces = ref([])
const districts = ref([])
const wards = ref([])
const isLoading = ref(false)
const isOpenChooseAddress = ref(false)
const currentAddress = ref(null)
const isOpenModalAddress = ref(false)
const location = reactive({
  province_id: '',
  district_id: '',
  ward_id: '',
  shipping_address: '',
})

const getGeolocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords
        getCityAndSetShippingAddress(latitude, longitude)
      },
      (err) => {
        console.log(err)

        return alert('Có lỗi khi lấy vị trí vui lòng thử lại.')
      }
    )
  } else {
    return alert('Geolocation is not supported by this browser.')
  }
}

const getCityAndSetShippingAddress = async (lat, lon) => {
  try {
    isLoading.value = true
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
    )

    if (!response.ok) {
      throw new Error('Không thể lấy vị trí từ API.')
    }

    const data = await response.json()

    location.shipping_address = data?.display_name
    await getLocationByAddress(data)
  } catch (err) {
    return alert('Có lỗi khi lấy vị trí vui lòng thử lại.')
  } finally {
    isLoading.value = false
  }
}

const handleLocationChange = async (target, location_id) => {
  const response = await $axios.get('location/getLocation', {
    params: { target, location_id },
  })

  const responseFormat = formatDataToSelect(
    response.data[target],
    'code',
    'name'
  )

  emits('onLocation', target)

  if (target === 'districts') {
    districts.value = responseFormat
  } else if (target === 'wards') {
    wards.value = responseFormat
  }
}

const getProvinces = async () => {
  const response = await $axios.get('location/provinces')
  provinces.value = formatDataToSelect(response.data || [], 'code', 'name')
}

const getLocationByAddress = async (address) => {
  const response = await $axios.post('location/by-address', {
    addressData: address,
  })

  const locations = _.get(response, 'data', [])

  _.forEach(locations, (item) => {
    const { target, code, data } = item
    const responseFormat = formatDataToSelect(data, 'code', 'name')

    switch (target) {
      case 'provinces':
        location.province_id = code
        break
      case 'districts':
        districts.value = responseFormat
        location.district_id = code
        break
      case 'wards':
        wards.value = responseFormat
        location.ward_id = code
        break
    }
  })
}

const fetch = async (endpoint = '', payload = {}) => {
  try {
    let response = await $axios.post(endpoint, payload)

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
  }
}

const handleUpdatePrimaryAddress = async (id) => {
  const endpoint = `users/addresses/${id}?_method=PUT`
  const payload = {
    is_primary: true,
  }
  fetch(endpoint, payload)
}

watch(
  user,
  (newVal) => {
    currentAddress.value = newVal?.addresses?.find((item) => item.is_primary)
  },
  {
    immediate: true,
  }
)

onMounted(async () => {
  getProvinces()
})
</script>
<template>
  <div class="checkout-address">
    <div class="d-flex items-center justify-between pb-4">
      <h3 class="title billing-title text-uppercase ls-10 pt-1 m-0">
        Địa chỉ giao hàng
      </h3>
      <a
        href="#"
        @click.prevent="isOpenChooseAddress = true"
        class="fs-15"
        v-if="isLoggedIn"
        >Thay đổi</a
      >
    </div>
    <div class="customer-address" v-if="!isLoggedIn || !currentAddress">
      <div class="row gutter-sm">
        <div class="col-xs-6">
          <IncludesInputComponent name="customer_name" label="Họ và tên *" />
        </div>
        <div class="col-xs-6">
          <IncludesInputComponent
            name="customer_phone"
            label="Số điện thoại *"
          />
        </div>
        <div class="col-xs-12">
          <IncludesInputComponent
            name="customer_email"
            label="Địa chỉ email *"
          />
        </div>
        <div class="col-xs-12 mb-5 d-none">
          <v-btn
            rounded="xl"
            prepend-icon="mdi-map-marker"
            @click="getGeolocation"
            :loading="isLoading"
          >
            <template v-slot:prepend>
              <v-icon color="warning"></v-icon>
            </template>

            Chọn từ vị trí
          </v-btn>
        </div>
      </div>
      <div class="row gutter-sm">
        <div class="col-md-4">
          <IncludesSelectComponent
            name="province_id"
            label="Tỉnh / Thành phố *"
            :items="provinces"
            :old-value="location.province_id"
            @on-change="handleLocationChange('districts', $event)"
          />
        </div>
        <div class="col-md-4">
          <IncludesSelectComponent
            :items="districts"
            name="district_id"
            label="Quận / Huyện *"
            :old-value="location.district_id"
            @on-change="handleLocationChange('wards', $event)"
          />
        </div>
        <div class="col-md-4">
          <IncludesSelectComponent
            :items="wards"
            name="ward_id"
            label="Phường / Xã *"
            :old-value="location.ward_id"
          />
        </div>

        <div class="col-md-12">
          <IncludesInputComponent
            name="shipping_address"
            label="Địa chỉ giao hàng *"
            typeInput="textarea"
            :old-value="location.shipping_address"
          />
        </div>
      </div>
    </div>

    <div class="customer-user" v-else>
      <div>
        <div class="v2-checkout-address-inner">
          <div>
            <div class="v2-address-title-container">
              <span class="v2-address-title">{{
                currentAddress?.fullname
              }}</span>
              <span class="v2-mobile">{{ currentAddress?.phone }}</span>
            </div>
          </div>
          <div class="v2-address-info-item">
            <span
              class="v2-address-tag-label mr-2"
              style="
                background-image: linear-gradient(
                  -143deg,
                  rgb(255, 123, 83) 0%,
                  rgb(255, 75, 40) 100%
                );
              "
              >NHÀ RIÊNG</span
            >
            <span class="v2-address-info-address">
              {{ currentAddress?.shipping_address }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->

  <v-dialog
    v-model="isOpenChooseAddress"
    max-width="700"
    persistent
    @click:outside="isOpenChooseAddress = false"
  >
    <v-card class="py-3 relative" elevation="12" max-width="700" width="100%">
      <div class="d-flex items-center justify-between px-4">
        <h3 class="title billing-title text-uppercase ls-10 pt-1 m-0">
          Thay đổi Địa chỉ giao hàng
        </h3>
        <v-btn
          icon="mdi-close"
          size="small"
          variant="text"
          @click="isOpenChooseAddress = false"
        ></v-btn>
      </div>
      <v-divider class="border-opacity-100 mb-0"></v-divider>
      <v-row class="mt-4 px-4" v-if="user?.addresses?.length">
        <v-col cols="12" md="12">
          <div
            class="customer-user py-3"
            v-for="item in user?.addresses"
            :key="item.id"
          >
            <div class="row gutter-sm items-center">
              <div class="col-xs-9">
                <div class="v2-checkout-address-inner">
                  <div>
                    <div class="v2-address-title-container">
                      <span class="v2-address-title">{{ item.fullname }}</span>
                      <span class="v2-mobile">{{ item.phone }}</span>
                    </div>
                  </div>
                  <div class="v2-address-info-item">
                    <span
                      class="v2-address-tag-label mr-2"
                      style="
                        background-image: linear-gradient(
                          -143deg,
                          rgb(255, 123, 83) 0%,
                          rgb(255, 75, 40) 100%
                        );
                      "
                      >NHÀ RIÊNG</span
                    >
                    <span class="v2-address-info-address">
                      {{ item.shipping_address }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="mt-1 d-flex justify-end">
                  <v-btn
                    density="compact"
                    @click="handleUpdatePrimaryAddress(item.id)"
                    :color="item.is_primary == 1 ? 'primary' : ''"
                    :disabled="item.is_primary == 1"
                    >Mặc định</v-btn
                  >
                </div>
              </div>
            </div>
          </div>
          <v-btn
            prepend-icon="mdi-plus-circle"
            @click="
              () => {
                isOpenModalAddress = true
                isOpenChooseAddress = false
              }
            "
            >Thêm địa chỉ mới</v-btn
          >
        </v-col>
      </v-row>
    </v-card>
  </v-dialog>

  <IncludesLocationModal :is-open-modal-address="isOpenModalAddress" />
</template>

<script setup>
import _, { set } from 'lodash'
import { useForm } from 'vee-validate'

const emits = defineEmits(['onLocation'])
const props = defineProps({
  isOpenModalAddress: {
    type: Boolean,
    required: true,
  },
})

const authStore = useAuthStore()
const isOpen = ref(props.isOpenModalAddress)
const { $axios, $authService } = useNuxtApp()
const provinces = ref([])
const districts = ref([])
const wards = ref([])
const isLoading = ref(false)
const location = reactive({
  province_id: '',
  district_id: '',
  ward_id: '',
  shipping_address: '',
})

const { handleSubmit, setFieldValue } = useForm({
  validationSchema: {
    fullname(value) {
      if (value) return true
      return 'Vui lòng nhập họ và tên.'
    },
    phone(value) {
      if (!value) return 'Số điện thoại không được để trống.'
      if (/^(0[0-9]{9})$/.test(value)) return true
      return 'Số điện thoại không đúng định dạng.'
    },
    province_id(value) {
      if (value) return true
      return 'Vui lòng chọn Tỉnh / Thành phố.'
    },
    district_id(value) {
      if (value) return true
      return 'Vui lòng chọn Quận / Huyện.'
    },
    ward_id(value) {
      if (value) return true
      return 'Vui lòng chọn Phường / Xã.'
    },
    shipping_address(value) {
      if (value) return true
      return 'Địa chỉ giao hàng không được để trống.'
    },
  },
})

const fetchCreate = async (endpoint = '', payload = {}) => {
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

const onSubmit = handleSubmit(async (values) => {
  fetchCreate(`users/addresses`, values)

  isOpen.value = false
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

  if (target === 'districts') {
    districts.value = responseFormat

    setFieldValue('district_id', '')
    setFieldValue('ward_id', '')
  } else if (target === 'wards') {
    wards.value = responseFormat

    setFieldValue('ward_id', '')
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

watch(
  () => props.isOpenModalAddress,
  (newVal) => {
    isOpen.value = newVal
  }
)

onMounted(async () => {
  getProvinces()
})
</script>
<template>
  <v-dialog
    v-model="isOpen"
    max-width="500"
    persistent
    @click:outside="isOpen = false"
  >
    <v-card
      class="py-6 px-4 relative"
      elevation="12"
      max-width="500"
      width="100%"
    >
      <form @submit.prevent="onSubmit">
        <h3 class="text-h6 mb-4 text-center">Thay đổi thông tin địa chỉ</h3>

        <div class="row my-4 gap-3">
          <div class="col-lg-12">
            <IncludesInputComponent name="fullname" label="Họ và tên *" />
          </div>
          <div class="col-lg-12">
            <IncludesInputComponent name="phone" label="Số điện thoại *" />
          </div>
          <div class="col-lg-12 mb-5 d-none">
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
          <div class="col-lg-12">
            <IncludesSelectComponent
              name="province_id"
              label="Tỉnh / Thành phố *"
              :items="provinces"
              :old-value="location?.province_id"
              @on-change="handleLocationChange('districts', $event)"
            />
          </div>

          <div class="col-lg-12">
            <IncludesSelectComponent
              :items="districts"
              name="district_id"
              label="Quận / Huyện *"
              :old-value="location?.district_id"
              @on-change="handleLocationChange('wards', $event)"
            />
          </div>

          <div class="col-lg-12">
            <IncludesSelectComponent
              :items="wards"
              name="ward_id"
              label="Phường / Xã *"
              :old-value="location?.ward_id"
            />
          </div>

          <div class="col-lg-12">
            <IncludesInputComponent
              name="shipping_address"
              label="Địa chỉ giao hàng *"
              typeInput="textarea"
              :old-value="location?.shipping_address"
            />
          </div>
        </div>

        <div class="text-center">
          <v-btn
            color="#336699"
            height="40"
            text="Xác nhận"
            variant="flat"
            width="70%"
            type="submit"
          ></v-btn>
        </div>
      </form>
    </v-card>
  </v-dialog>
</template>
<style scoped></style>

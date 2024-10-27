<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useForm } from 'vee-validate'
import Cookies from 'js-cookie'

const { $axios } = useNuxtApp()
const cartStore = useCartStore()
const authStore = useAuthStore()
const orderStore = useOrderStore()
const router = useRouter()
const showApplyVoucher = ref(false)
const sidebarStyle = ref({})
const mainContent = ref(null)
const secondaryContent = ref(null)
const stickyOffset = 20
const user = computed(() => authStore.getUser)
const isLoggedIn = computed(() => authStore.isSignedIn)
const carts = computed(() => cartStore.getCartSelected)
const voucherCode = ref('')

const { handleSubmit, setFieldValue, setValues } = useForm({
  validationSchema: {
    customer_name(value) {
      return validateField(value, 'Vui lòng nhập họ và tên.')
    },
    shipping_address(value) {
      return validateField(value, 'Vui lòng nhập địa chỉ.')
    },
    province_id(value) {
      return validateField(value, 'Vui lòng chọn Tỉnh / Thành phố.')
    },
    district_id(value) {
      return validateField(value, 'Vui lòng chọn Quận / Huyện.')
    },
    ward_id(value) {
      return validateField(value, 'Vui lòng chọn Phường / Xã.')
    },
    customer_phone(value) {
      if (!isLoggedIn.value) {
        return validatePhone(value)
      }
      return true
    },
    customer_email(value) {
      if (!isLoggedIn.value) {
        return validateEmail(value)
      }
      return true
    },
  },
})

// Helper functions
function validateField(value, message) {
  if (isLoggedIn.value) return true
  return value ? true : message
}

const validatePhone = (value) => {
  if (!value) return 'Số điện thoại không được để trống.'
  return /^(0[0-9]{9})$/.test(value)
    ? true
    : 'Số điện thoại không đúng định dạng.'
}

const validateEmail = (value) => {
  if (!value) return 'Email không được để trống.'
  return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value)
    ? true
    : 'Email không đúng định dạng.'
}

const onSubmit = handleSubmit(async (values) => {
  try {
    const endpoint = isLoggedIn.value
      ? `/orders`
      : `/orders/?session_id=` + Cookies.get('session_id')

    const response = await $axios.post(endpoint, values)

    if (response.status == 'success') {
      return (location.href = response?.url)
    }
  } catch (error) {
    toast(
      formatMessages(error?.response?.data?.messages) || 'Thao tác thất bại',
      'error'
    )
  }
})

const handleLocationChange = (target) => {
  if (target === 'districts') {
    setFieldValue('ward_id', '')
    setFieldValue('district_id', '')
  } else if (target === 'wards') {
    setFieldValue('ward_id', '')
  }
}

const handleScroll = () => {
  const scrollY = window.scrollY
  const mainRect = mainContent.value?.getBoundingClientRect()
  const secondaryRect = secondaryContent.value?.getBoundingClientRect()

  if (mainRect < secondaryRect + 100) {
    return
  }

  const colRect = document
    .querySelector('.main-content')
    ?.getBoundingClientRect()

  if (scrollY > stickyOffset && colRect) {
    if (scrollY < colRect.bottom + window.scrollY) {
      const newBottom = Math.max(
        window.innerHeight - mainRect.bottom - stickyOffset,
        0
      )
      sidebarStyle.value = {
        position: 'fixed',
        bottom: `${newBottom + 20}px`,
        width: '505px',
        borderBottom: '1px solid rgb(238, 238, 238)',
      }
    } else {
      sidebarStyle.value = {}
    }
  } else {
    sidebarStyle.value = {}
  }
}

const applyVoucher = async () => {
  if (!voucherCode.value) return toast('Vui lòng nhập mã giảm giá.', 'error')
  try {
    const response = await $axios.post(`/vouchers/${voucherCode.value}/apply`)

    if (response.status == 'success') {
      toast(response.messages)
      setFieldValue('voucher_id', response.data.voucher_id)
      orderStore.setDiscount(response.data.discount)
    }
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  }
}

const applyInfoAddress = (currentUser) => {
  const currentAddress = currentUser?.addresses?.find((item) => item.is_primary)

  setFieldValue('shipping_address', currentAddress?.shipping_address)
  setFieldValue('customer_name', currentAddress?.fullname)
  setFieldValue('customer_phone', currentAddress?.phone)
  setFieldValue('customer_email', user.value?.email)
  setFieldValue('province_id', currentAddress?.province_id)
  setFieldValue('district_id', currentAddress?.district_id)
  setFieldValue('ward_id', currentAddress?.ward_id)
}

watch(
  user,
  (newVal) => {
    applyInfoAddress(newVal)
  },
  {
    immediate: true,
    deep: true,
  }
)

onBeforeMount(() => {
  if (!carts.value.length) {
    router.push({ name: 'cart' })
  }
})

onMounted(async () => {
  applyInfoAddress()
  window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
<template>
  <!-- Start of Main -->
  <main class="main checkout">
    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav">
      <div class="container">
        <ul class="breadcrumb shop-breadcrumb bb-no">
          <li class="passed">
            <NuxtLink to="cart">Giỏ hàng</NuxtLink>
          </li>
          <li class="active">
            <NuxtLink to="checkout">Thanh toán</NuxtLink>
          </li>
          <li>
            <a href="#">Hoàn tất đơn hàng</a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End of Breadcrumb -->

    <!-- Start of PageContent -->
    <div class="page-content checkout-content-wrapper">
      <form @submit.prevent="onSubmit">
        <div class="container">
          <div class="coupon-toggle">
            Bạn có mã giảm giá?
            <a
              @click.prevent="showApplyVoucher = !showApplyVoucher"
              href="#"
              class="show-coupon font-weight-bold text-uppercase text-dark"
              >Nhập mã code</a
            >
          </div>
          <div class="coupon-content mb-4" v-show="showApplyVoucher">
            <p>
              Nếu bạn có mã giảm giá, vui lòng áp dụng bên dưới. Bạn có thể xem
              mã giảm giá
              <NuxtLink
                to="/voucher"
                target="_blank"
                title="Xem mã giảm giá tại đây"
                >tại đây</NuxtLink
              >
            </p>
            <div class="input-wrapper-inline">
              <input
                type="text"
                v-model="voucherCode"
                class="form-control form-control-md mr-1 mb-2"
                placeholder="Mã giảm giá"
              />
              <button
                type="button"
                @click="applyVoucher"
                class="btn button btn-rounded btn-coupon mb-2"
              >
                Áp Dụng Mã
              </button>
            </div>
          </div>
          <div class="row mb-9">
            <div class="col-lg-7 pr-lg-4 mb-4 main-content" ref="mainContent">
              <!-- Checkout address -->
              <CheckoutAddress @on-location="handleLocationChange" />

              <!-- Shipping method -->
              <CheckoutShippingMethod />

              <!-- Checkout product -->
              <CheckoutProduct />

              <div class="form-group mt-3">
                <div class="col-md-12">
                  <IncludesInputComponent
                    :row-height="20"
                    :rows="2"
                    type-input="textarea"
                    name="note"
                    label="Lời nhắn"
                    hint="Lời nhắn về đơn hàng của bạn, ví dụ ghi chú đặc biệt về việc giao hàng"
                  />
                </div>
              </div>
            </div>
            <div class="col-lg-5 mb-4">
              <div
                class="order-summary-wrapper sticky-sidebar"
                ref="secondaryContent"
                :style="sidebarStyle"
              >
                <CheckoutSidebar />
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!-- End of PageContent -->
  </main>
  <!-- End of Main -->
</template>

<style scoped></style>

<script setup>

useSeoMeta({
  title: 'Giỏ hàng',
  ogTitle: 'Giỏ hàng',
  description: 'Giỏ hàng.',
  ogDescription: 'Giỏ hàng.',
})

import Cookies from 'js-cookie'
import { onMounted, watch } from 'vue'
import QuantityComponent from '~/components/includes/QuantityComponent.vue'

const { $axios } = useNuxtApp()
const cartStore = useCartStore()
const authStore = useAuthStore()

const carts = computed(() => cartStore.getCart)
const cartSelected = computed(() => cartStore.getCartSelected)
const checkedItems = ref([])
const allChecked = ref(false)
const openClearCart = ref(false)
const totalAmout = computed(() => cartStore.getTotalAmount)

const handleAllCheckboxChange = () => {
  if (allChecked.value) {
    carts.value.forEach((cart, index) => {
      checkedItems.value[index] = cart.product_variant_id
    })
  } else {
    checkedItems.value = []
  }
  updateAllSelectedCarts(allChecked.value)
}

const handleCheckboxChange = (event, index) => {
  if (event.target.checked === false) {
    delete checkedItems.value[index]
  }
  updateOneSelectedCarts(event.target.value)
}

const getCarts = async () => {
  await cartStore.getAllCarts()
  handleSelectCart()
}

const handleSelectCart = () => {
  carts.value?.forEach((cart, index) => {
    if (cart.is_selected) {
      checkedItems.value[index] = cart.product_variant_id
    }
  })
}

const checkSelectedAll = () => {
  if (Object.keys(checkedItems.value)?.length === carts.value.length) {
    allChecked.value = true
  } else {
    allChecked.value = false
  }
}

const updateAllSelectedCarts = async () => {
  const endpoint = getEndpointSelectedCart()

  const response = await $axios.put(endpoint, {
    select_all: allChecked.value,
  })
  setCartToStore(response.data)
}

const updateOneSelectedCarts = async (variantId) => {
  const endpoint = getEndpointSelectedCart()

  const response = await $axios.put(endpoint, {
    product_variant_id: variantId,
  })
  setCartToStore(response.data)
}

const getEndpointSelectedCart = () => {
  const endpoint = authStore.isSignedIn
    ? '/carts/handle-selected'
    : '/carts/handle-selected?session_id=' + Cookies.get('session_id')
  return endpoint
}

const setCartToStore = (data) => {
  cartStore.setTotalAmount(data?.total_amount)
  cartStore.setCarts(data?.items)
}

const handleClearCart = async () => {
  const endpoint = authStore.isSignedIn
    ? `/carts/clean`
    : `/carts/clean?session_id=` + Cookies.get('session_id')

  const response = await $axios.delete(endpoint)
  openClearCart.value = false

  cartStore.removeAllCarts()
  toast(response.messages, response.status)
}

const handleRemove = async (variantId) => {
  const endpoint = authStore.isSignedIn
    ? `/carts/${variantId}`
    : `/carts/${variantId}?session_id=` + Cookies.get('session_id')

  const response = await $axios.delete(endpoint)

  setCartToStore(response.data)
  cartStore.setCartCount(response.data?.items?.length || 0)

  checkedItems.value = []
  handleSelectCart()
}

const debouncedHandleQuantityChange = debounce(async (variantId, quantity) => {
  const endpoint = authStore.isSignedIn
    ? `/carts`
    : `/carts?session_id=` + Cookies.get('session_id')

  const response = await $axios.post(endpoint, {
    product_variant_id: variantId,
    quantity: quantity,
  })

  if (response.status == 'success') {
    setCartToStore(response.data)
  }
}, 1000)

onMounted(() => {
  getCarts()
})

watch(checkedItems, checkSelectedAll, { deep: true })
</script>

<template>
  <!-- Start of Main -->
  <main class="main cart">
    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav">
      <div class="container">
        <ul class="breadcrumb shop-breadcrumb bb-no">
          <li class="active">
            <NuxtLink to="/cart">Giỏ hàng</NuxtLink>
          </li>
          <li>
            <NuxtLink :to="cartSelected?.length ? '/checkout' : '#'"
              >Thanh toán</NuxtLink
            >
          </li>
          <li>
            <a href="#">Hoàn tất đơn hàng</a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End of Breadcrumb -->

    <!-- Start of PageContent -->
    <div class="page-content">
      <div class="container">
        <div class="row gutter-lg mb-10">
          <div class="col-lg-12 pr-lg-12 mb-6 cart-wrapper">
            <table class="shop-table cart-table" v-if="carts?.length > 0">
              <thead>
                <tr>
                  <th>
                    <v-checkbox
                      v-model="allChecked"
                      style="font-size: 18px"
                      @change="handleAllCheckboxChange()"
                    ></v-checkbox>
                  </th>
                  <th class="product-name"><span>Sản phẩm</span></th>
                  <th width="300"></th>
                  <th class="product-price"><span>Giá cả</span></th>
                  <th class="product-quantity"><span>Số lượng</span></th>
                  <th class="product-subtotal"><span>Tạm tính</span></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(cart, index) in carts" :key="cart.id">
                  <td>
                    <v-checkbox
                      v-model="checkedItems[index]"
                      :value="cart.product_variant_id"
                      @change="handleCheckboxChange($event, index)"
                      style="font-size: 18px"
                    ></v-checkbox>
                  </td>
                  <td class="product-thumbnail">
                    <div class="p-relative">
                      <NuxtLink :to="`product/${cart.slug}-${cart.product_id}`">
                        <figure>
                          <img
                            :src="resizeImage(cart.image, 300)"
                            :alt="cart.name"
                          />
                        </figure>
                      </NuxtLink>
                      <button
                        class="btn btn-close"
                        @click="handleRemove(cart.product_variant_id)"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </td>
                  <td class="product-name">
                    <NuxtLink :to="`product/${cart.slug}-${cart.product_id}`">
                      {{ cart.name }}
                    </NuxtLink>
                    <span class="d-block mt-1 fs-13" style="color: #336699">
                      Phân loại:
                      {{ cart.attributes }}
                    </span>
                  </td>
                  <td class="text-right">
                    <div class="product-price">
                      <ins class="new-price">{{
                        formatCurrency(cart.sale_price || cart.price)
                      }}</ins>
                      <del class="old-price" v-if="cart.sale_price">{{
                        formatCurrency(cart.price)
                      }}</del>
                    </div>
                  </td>
                  <td class="product-quantity text-right">
                    <QuantityComponent
                      :old-quantity="cart.quantity"
                      :max="cart.stock - 2"
                      @update:quantity="
                        debouncedHandleQuantityChange(
                          cart.product_variant_id,
                          $event
                        )
                      "
                    />
                  </td>
                  <td class="product-subtotal">
                    <span class="amount">{{
                      formatCurrency(cart.sub_total)
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="cart-action mb-6" v-if="carts?.length > 0">
              <NuxtLink
                to="/"
                class="btn btn-dark btn-rounded btn-icon-left btn-shopping mr-auto"
              >
                <i class="w-icon-long-arrow-left"></i>
                Tiếp tục mua sắm</NuxtLink
              >
              <div class="modal-cart-clear">
                <v-dialog v-model="openClearCart" max-width="400" persistent>
                  <template v-slot:activator="{ props: activatorProps }">
                    <button
                      v-bind="activatorProps"
                      type="button"
                      class="btn btn-rounded btn-default btn-clear"
                    >
                      Xóa giỏ hàng
                    </button>
                  </template>

                  <v-card
                    text="Nếu bạn chấp nhập xóa giỏ hàng các sản phẩm sẽ vĩnh viễn không thể khôi phục lại."
                    title="Bạn có chắc chắn muốn xóa?"
                  >
                    <template v-slot:actions>
                      <v-spacer></v-spacer>

                      <v-btn @click="openClearCart = false"> Hủy bỏ </v-btn>

                      <v-btn @click="handleClearCart"> Đồng ý </v-btn>
                    </template>
                  </v-card>
                </v-dialog>
              </div>
            </div>

            <div v-if="!carts?.length">
              <v-empty-state
                icon="mdi-magnify"
                text="Giỏ hàng đang trống vui lòng chọn quay lại mua những sản phẩm mới nhất của chúng tôi."
                title="Chưa có sản phẩm nào trong giỏ hàng."
              ></v-empty-state>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End of PageContent -->
    <div class="cart-footer" v-if="carts?.length > 0">
      <div class="container">
        <div class="footer-wrap">
          <v-row no-gutters align="center">
            <v-col cols="4">
              <v-checkbox
                v-model="allChecked"
                style="font-size: 18px"
                @change="handleAllCheckboxChange()"
                label="Chọn tất cả sản phẩm"
              ></v-checkbox>
            </v-col>
            <v-col cols="8">
              <div class="d-flex flex-end items-center">
                <div class="d-flex items-center w-100">
                  <div class="w-100 d-flex justify-between items-center">
                    <span class="total-cart-count fs-16">
                      Tổng thanh toán ({{ Object.keys(checkedItems)?.length }}
                      Sản phẩm):
                    </span>

                    <span
                      class="total-cart-price mr-4 total-amout-cart"
                    >
                      {{
                        formatCurrency(totalAmout) == '-'
                          ? 0
                          : formatCurrency(totalAmout)
                      }}
                    </span>
                  </div>

                  <div class="ml-4" style="width: 500px">
                    <NuxtLink
                      :to="cartSelected?.length ? '/checkout' : '#'"
                      class="btn btn-block btn-dark btn-icon-right btn-rounded btn-checkout"
                      :class="{ disabled: !cartSelected?.length }"
                    >
                      Thanh toán
                      <i class="w-icon-long-arrow-right"></i
                    ></NuxtLink>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </div>
      </div>
    </div>
  </main>
  <!-- End of Main -->
</template>

<style scoped>
.btn-checkout.disabled {
  pointer-events: none;
  opacity: 0.5;
  cursor: not-allowed;
}
.product-thumbnail figure {
  background-color: #f5f6f7;
  border-radius: 8px;
}
.product-thumbnail img {
  width: 100px;
  height: 112px;
  object-fit: contain;
  border-radius: 8px;
  background-color: #f5f6f7;
  mix-blend-mode: darken;
}
.shop-table.cart-table .product-price,
.shop-table.cart-table .product-subtotal {
  text-align: right;
}
.product-name a {
  /* Giới hạn số dòng tối đa */
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2; /* Giới hạn số dòng */
  overflow: hidden;
  text-overflow: ellipsis; /* Hiển thị '...' khi văn bản bị cắt bớt */
  white-space: normal; /* Cho phép dòng mới */
}
.shop-table.cart-table .product-price {
  width: auto;
}
.shop-table.cart-table .product-quantity {
  width: auto;
  text-align: right;
}
.shop-table.cart-table .product-quantity .input-group {
  float: right;
}
</style>

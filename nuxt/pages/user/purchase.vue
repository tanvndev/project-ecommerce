<script setup>
useSeoMeta({
  title: 'Đơn hàng',
  ogTitle: 'Đơn hàng',
  description: 'Đơn hàng.',
  ogDescription: 'Đơn hàng.',
})
import { useCartStore, useLoadingStore, useOrderStore } from '#imports'
import _ from 'lodash'
import { ORDER_STATUS, ORDER_STATUS_TABS, PAYMENT_STATUS } from '~/static/order'
import { COD_ID } from '~/static/paymentMethod'
import { RATING_LABLE } from '~/static/rating'

const { $axios } = useNuxtApp()
const page = ref(1)
const pageSize = ref(null)
const tab = ref(ORDER_STATUS_TABS[0].value)
const loadingStore = useLoadingStore()
const orderStore = useOrderStore()
const cartStore = useCartStore()
const orders = ref([])
const search = ref('')
const rating = ref(5)
const isReview = ref(false)
const chooseProductReview = ref([])
const orderItemRating = ref(null)
const confirmCancelOrder = ref(false)
const confirmCompleteOrder = ref(false)
const orderIdToUpdateStatus = ref(null)
const comment = ref('')
const uploadedImages = ref([])
const fileInput = ref(null)

const handleOrderPayment = async (orderCode) => {
  if (!orderCode) {
    return toast('Có lỗi vui lòng tải lại trang.')
  }

  console.log(orderCode)

  try {
    const response = await $axios.get(`/orders/${orderCode}/payment`)

    return (location.href = response?.url)
  } catch (error) {
    return toast('Có lỗi vui lòng tải lại trang.')
  }
}
const openReviewDialog = (orderId) => {
  const order = orders.value.find((order) => order.id === orderId)

  if (!order) {
    return
  }

  const addedProductIds = new Set()

  orderItemRating.value = _.filter(order.order_items, (item) => {
    if (addedProductIds.has(item.product_id)) {
      return false
    }
    addedProductIds.add(item.product_id)
    return !item.is_reviewed
  })

  if (_.isEmpty(orderItemRating.value)) {
    return
  }

  orderIdToUpdateStatus.value = orderId
  isReview.value = true
}
const handleConfirmCancelOrder = async () => {
  if (!orderIdToUpdateStatus.value) {
    return
  }

  try {
    const response = await $axios.put(
      `/orders/${orderIdToUpdateStatus.value}/cancel`
    )

    if (response.status == 'success') {
      toast(response.messages)
      getAllOrder()
    }
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  }

  confirmCancelOrder.value = false
}

const handleConfirmCompleteOrder = async () => {
  if (!orderIdToUpdateStatus.value) {
    return
  }

  try {
    const response = await $axios.put(
      `/orders/${orderIdToUpdateStatus.value}/complete`
    )

    if (response.status == 'success') {
      toast(response.messages)
      getAllOrder()
    }
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  }
  confirmCompleteOrder.value = false
}

const handleProductReview = async () => {
  if (!rating.value) {
    return toast('Vui lý chọn điểm đánh giá.', 'error')
  }
  if (chooseProductReview.value?.length == 0) {
    return toast('Vui lòng chọn sản phẩm cần đánh giá.', 'error')
  }
  if (!orderIdToUpdateStatus.value) {
    return toast('Vui lòng tải lại trang.', 'error')
  }

  const formData = new FormData()
  formData.append('rating', rating.value)
  formData.append('comment', comment.value)
  formData.append('product_id', chooseProductReview.value)
  formData.append('order_id', orderIdToUpdateStatus.value)

  for (let i = 0; i < uploadedImages.value.length; i++) {
    const imageBlob = await fetch(uploadedImages.value[i]).then((res) =>
      res.blob()
    )
    formData.append('images[]', imageBlob, `image_rating_${i}.png`)
  }

  try {
    const response = await $axios.post('/product-reviews', formData)

    if (response.status == 'success') {
      toast(response.messages)
      getAllOrder()
    }
  } catch (error) {
    toast(error?.response?.data?.messages || 'Thao tác thất bại', 'error')
  } finally {
    isReview.value = false
    uploadedImages.value = []
    comment.value = ''
    rating.value = 5
  }
}

const getAllOrder = async () => {
  try {
    loadingStore.setLoading(true)
    const response = await $axios.get('/orders/user', {
      params: {
        order_status: tab.value,
        search: search.value,
        page: page.value,
      },
    })
    orders.value = response.data?.data
    pageSize.value = response.data?.last_page
  } catch (error) {
  } finally {
    loadingStore.setLoading(false)
  }
}

watch(page, (newPage) => {
  debounceHandleSearch()
})
const debounceHandleSearch = debounce(getAllOrder, 500)

const debounceHandleChangeTab = debounce(getAllOrder, 500)

const getOrderDetail = async (orderId) => {
  const order = orders.value.find((order) => order.id === orderId)
  if (!order) {
    return
  }
  orderStore.setOrderDetail(order)
}

const addToCart = async (orderId) => {
  const order = orders.value.find((order) => order.id === orderId)
  const variantIds = order.order_items
    .map((item) => item.product_variant_id)
    .join(',')

  if (!variantIds) {
    return toast('Có lỗi vui lòng thử lại.', 'error')
  }

  const payload = {
    product_variant_id: variantIds,
  }

  const response = await $axios.post('/carts/comming-soon', payload)

  cartStore.setCartCount(response.data?.items.length)
  toast(response.messages, response.status)
}

const handleFileChange = (event) => {
  const files = event.target.files

  if (uploadedImages.value?.length >= 3) {
    return
  }

  if (files.length + uploadedImages.value.length > 3) {
    return toast('Vui lòng chọn tối đa 3 hình ảnh.', 'error')
  }

  const filePromises = Array.from(files).map((file) => {
    return new Promise((resolve) => {
      const reader = new FileReader()

      reader.onload = (e) => {
        resolve(e.target.result)
      }

      reader.readAsDataURL(file)
    })
  })

  Promise.all(filePromises)
    .then((imageData) => {
      uploadedImages.value.push(...imageData)
    })
    .catch((error) => {
      console.error('Error reading files:', error)
    })
}

const removeImage = (index) => {
  uploadedImages.value.splice(index, 1)
}

onMounted(async () => {
  await getAllOrder()
})
</script>
<template>
  <div class="page-content order-purchase-wrapper pt-2 mt-6 pb-50">
    <div class="container">
      <div class="tab tab-vertical row gutter">
        <div class="col-lg-3">
          <UserSidebar />
        </div>
        <div class="col-lg-9">
          <v-card class="mx-auto px-2">
            <v-tabs
              v-model="tab"
              align-tabs="start"
              color="primary"
              center-active
              @update:modelValue="debounceHandleChangeTab"
            >
              <v-tab
                class="text-capitalize"
                v-for="item in ORDER_STATUS_TABS"
                :key="item.value"
                :value="item.value"
                >{{ item.title }}</v-tab
              >
            </v-tabs>
          </v-card>

          <div class="mt-3">
            <v-text-field
              v-model="search"
              @input="debounceHandleSearch"
              prepend-inner-icon="mdi-magnify"
              hint="Bạn có thể tìm kiếm theo ID đơn hàng"
              variant="outlined"
              clearable
              density="comfortable"
              placeholder="Bạn có thể tìm kiếm theo ID đơn hàng"
            ></v-text-field>
          </div>

          <v-row>
            <v-col
              cols="12"
              md="12"
              v-for="(order, index) in orders"
              :key="order.id"
            >
              <v-card hover class="mx-auto pa-3 item-order">
                <div class="d-flex justify-between items-center pb-1">
                  <div>
                    <v-btn
                      variant="tonal"
                      class="text-capitalize"
                      size="small"
                      prepend-icon="mdi-chat"
                      color="blue"
                      @click="$router.push(`/chat`)"
                      >Chat</v-btn
                    >
                  </div>
                  <div>
                    <v-chip
                      prepend-icon="mdi-truck"
                      class="px-3 text-uppercase"
                      :color="order?.order_status_color"
                    >
                      {{ order?.order_status }}
                    </v-chip>
                  </div>
                </div>
                <v-divider class="border-opacity-100"></v-divider>

                <NuxtLink
                  @click.prevent="getOrderDetail(order.id)"
                  :to="`/user/order/${order.code}`"
                >
                  <div
                    class="d-flex w-100 py-2 product-info-wrap"
                    v-for="item in order?.order_items"
                    :key="item.id"
                  >
                    <div class="order-product-image">
                      <img
                        class="object-contain"
                        :src="resizeImage(item.image, 100)"
                        :alt="item.product_variant_name"
                      />
                    </div>

                    <div class="order-product-info">
                      <div class="px-2 pt-1 product-title">
                        <div class="title">
                          <NuxtLink
                            :to="`/product/${item.slug}-${item.product_id}`"
                          >
                            {{ item.product_variant_name }}
                          </NuxtLink>
                        </div>
                        <div class="variant">
                          Phân loại: {{ item.attribute_values }}
                        </div>
                        <div class="quantity">x{{ item.quantity }}</div>
                      </div>
                      <div>
                        <div class="product-price">
                          <ins class="new-price">{{
                            formatCurrency(item.sale_price || item.price)
                          }}</ins>
                          <del class="old-price" v-if="item.sale_price">{{
                            formatCurrency(item.price)
                          }}</del>
                        </div>
                      </div>
                    </div>
                  </div>
                </NuxtLink>

                <v-divider class="border-opacity-100"></v-divider>

                <div class="d-flex w-100 justify-between items-center">
                  <div class="order-footer">
                    <v-row align="center" justify="end" no-gutters>
                      <v-col
                        cols="auto"
                        class="mr-2"
                        v-if="
                          ORDER_STATUS[3].value == order.order_status_code &&
                          order.payment_status_code ==
                            PAYMENT_STATUS[1].value &&
                          order?.order_items?.find(
                            (item) => item.is_reviewed == false
                          )
                        "
                      >
                        <v-btn
                          class="text-capitalize"
                          color="primary"
                          @click="openReviewDialog(order.id)"
                          >Đánh giá</v-btn
                        >
                      </v-col>

                      <v-col
                        cols="auto"
                        class="mr-2"
                        v-if="
                          ORDER_STATUS[0].value == order.order_status_code ||
                          ORDER_STATUS[1].value == order.order_status_code
                        "
                      >
                        <v-btn
                          variant="text"
                          class="text-capitalize border"
                          @click="
                            () => {
                              confirmCancelOrder = true
                              orderIdToUpdateStatus = order.id
                            }
                          "
                          >Hủy đơn hàng</v-btn
                        >
                      </v-col>
                      <v-col
                        cols="auto"
                        class="mr-2"
                        v-if="ORDER_STATUS[3].value == order.order_status_code"
                      >
                        <v-btn
                          @click="addToCart(order.id)"
                          variant="text"
                          class="text-capitalize border"
                          >Mua lại</v-btn
                        >
                      </v-col>
                      <v-col cols="auto" class="mr-2">
                        <v-btn
                          variant="text"
                          class="text-capitalize border"
                          @click="$router.push(`/chat`)"
                          >Liên hệ người bán</v-btn
                        >
                      </v-col>
                      <v-col
                        cols="auto"
                        class="mr-2"
                        v-if="
                          ORDER_STATUS[2].value == order.order_status_code &&
                          PAYMENT_STATUS[1].value == order.payment_status_code
                        "
                      >
                        <v-btn
                          color="primary"
                          class="text-capitalize border"
                          @click="
                            () => {
                              confirmCompleteOrder = true
                              orderIdToUpdateStatus = order.id
                            }
                          "
                          >Đã nhận hàng</v-btn
                        >
                      </v-col>
                      <v-col
                        cols="auto"
                        class="mr-2"
                        v-if="
                          PAYMENT_STATUS[0].value ==
                            order.payment_status_code &&
                          order.payment_method_id != COD_ID
                        "
                      >
                        <v-btn
                          size="large"
                          color="primary"
                          class="text-capitalize border"
                          @click="handleOrderPayment(order.code)"
                          >Thanh toán</v-btn
                        >
                      </v-col>
                    </v-row>
                  </div>
                  <div class="order-middle">
                    <div class="total-amount">
                      <label>Thành tiền:</label>
                      <div class="text">
                        {{ formatCurrency(order.final_price) }}
                      </div>
                    </div>
                  </div>
                </div>
              </v-card>
            </v-col>

            <v-dialog v-model="confirmCompleteOrder" max-width="500" persistent>
              <v-card
                text="Nếu đã nhận được hàng và thanh toán hàng thì vui lòng nhấn xác nhận."
                title="Bạn có chắc chắn muốn thực hiện thao tác?"
              >
                <template v-slot:actions>
                  <v-spacer></v-spacer>

                  <v-btn @click="confirmCompleteOrder = false"> Hủy bỏ </v-btn>

                  <v-btn @click="handleConfirmCompleteOrder"> Đồng ý </v-btn>
                </template>
              </v-card>
            </v-dialog>

            <v-dialog v-model="confirmCancelOrder" max-width="500" persistent>
              <v-card
                text="Nếu bạn nhấn hủy đơn hàng bạn và bạn không thể hoàn tác lại đơn hàng đã đặt."
                title="Bạn có chắc chắn muốn thực hiện thao tác?"
              >
                <template v-slot:actions>
                  <v-spacer></v-spacer>

                  <v-btn @click="confirmCancelOrder = false"> Hủy bỏ </v-btn>

                  <v-btn @click="handleConfirmCancelOrder"> Đồng ý </v-btn>
                </template>
              </v-card>
            </v-dialog>

            <!-- Modal review -->
            <v-dialog v-model="isReview" max-width="800" persistent>
              <v-card v-if="orderItemRating">
                <v-card-title class="d-flex justify-space-between align-center">
                  <div class="text-h5 text-medium-emphasis ps-2">
                    Đánh giá sản phẩm
                  </div>

                  <v-btn
                    icon="mdi-close"
                    variant="text"
                    @click="isReview = false"
                  ></v-btn>
                </v-card-title>

                <v-divider class="mb-4 border-opacity-100"></v-divider>

                <v-card-text>
                  <div>
                    <h4 class="mb-5">CHỌN SẢN PHẨM ĐÁNH GIÁ</h4>

                    <div
                      class="order-purchase-wrapper d-flex border-b mb-3 pb-3"
                      v-for="item in orderItemRating"
                      :key="item"
                    >
                      <div class="order-product-image">
                        <img
                          class="object-contain"
                          :src="resizeImage(item.image, 100)"
                        />
                      </div>

                      <div class="order-product-info">
                        <div class="px-2 pt-1 product-title">
                          <div class="title text-left">
                            <NuxtLink
                              :title="item.product_variant_name"
                              :to="`/product/${item.slug}-${item.product_id}`"
                            >
                              {{ item.product_variant_name }}
                            </NuxtLink>
                          </div>
                          <div class="variant">
                            Phân loại: {{ item.attribute_values }}
                          </div>
                        </div>

                        <div>
                          <v-checkbox
                            v-model="chooseProductReview"
                            label="Chọn sản phẩm"
                            :value="item.product_id"
                          ></v-checkbox>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-center">
                    <v-rating
                      v-model="rating"
                      :item-labels="RATING_LABLE"
                      color="yellow-darken-3"
                      class="mx-auto"
                      item-label-position="bottom"
                      size="x-large"
                    ></v-rating>
                  </div>

                  <div class="mt-5">
                    <v-textarea
                      v-model="comment"
                      clearable
                      label="Cảm nhận của bạn"
                      variant="outlined"
                      auto-grow
                      counter
                      autofocus
                      maxlength="255"
                    ></v-textarea>
                  </div>
                  <div>
                    <a href="#" @click="fileInput.click()" class="lh-1">
                      <input
                        type="file"
                        ref="fileInput"
                        accept="image/*"
                        @change="handleFileChange"
                        style="display: none"
                        multiple
                      />
                      <i class="mdi mdi-camera fs-19 lh-1"></i> Gửi ảnh thực tế
                      <span class="text-black">(Tối đa 3 ảnh)</span>
                    </a>

                    <div
                      class="mt-1 d-flex align-items-center"
                      v-if="uploadedImages?.length"
                    >
                      <div
                        class="position-relative me-3"
                        v-for="image in uploadedImages"
                        :key="image"
                      >
                        <v-img
                          :src="image"
                          class="img-thumnail"
                          style="width: 50px; height: 50px; border-radius: 5px"
                          width="50px"
                          height="50px"
                          alt=""
                        />
                        <div class="image-icon">
                          <v-btn
                            icon="mdi-close"
                            @click="removeImage(image)"
                            size="x-small"
                          >
                          </v-btn>
                        </div>
                      </div>
                    </div>
                  </div>
                </v-card-text>

                <template v-slot:actions>
                  <v-spacer></v-spacer>

                  <v-btn @click="isReview = false"> Hủy bỏ </v-btn>

                  <v-btn @click="handleProductReview"> Hoàn thành </v-btn>
                </template>
              </v-card>
            </v-dialog>

            <!-- pagination -->
            <div class="text-center d-flex mx-auto mt-3">

              <v-pagination
                v-model="page"
                :length="pageSize"
                rounded="circle"
              ></v-pagination>
            </div>
          </v-row>
          <div v-if="!orders?.length">
            <v-empty-state
              icon="mdi-magnify"
              text="Chúng tôi không thể tìm thấy đơn hàng của bạn vui lòng thử lại."
              title="Không có dữ liệu."
            ></v-empty-state>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-title a {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: normal;
}
.image-icon {
  position: absolute;
  top: -10px;
  left: 40px;
}
.image-icon .v-btn--icon.v-btn--density-default {
  width: 18px;
  height: 18px;
  font-size: 8px;
}
</style>

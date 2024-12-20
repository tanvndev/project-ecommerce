<script setup>

useSeoMeta({
  title: 'Đơn hàng chi tiết',
  ogTitle: 'Đơn hàng chi tiết',
  description: 'Đơn hàng chi tiết.',
  ogDescription: 'Đơn hàng chi tiết.',
})

import { useOrderStore } from '#imports'

const orderStore = useOrderStore()
const { $axios } = useNuxtApp()
const route = useRoute()
const order = ref([])
const orderDetail = computed(() => orderStore.getOrderDetail)

const getOrderByCode = async () => {
  const response = await $axios.get(`/orders/${route.params.code}/detail`)
  order.value = response.data
}

watch(orderDetail, () => {})

onMounted(() => {
  order.value = orderDetail.value
  if (!orderDetail.value?.length) {
    getOrderByCode()
  }
})
</script>
<template>
  <div class="page-content order-purchase-wrapper pt-2 mt-6">
    <div class="container">
      <div class="tab tab-vertical row gutter">
        <div class="col-lg-3">
          <UserSidebar />
        </div>
        <div class="col-lg-9">
          <v-card class="pa-2">
            <div class="d-flex justify-between items-center">
              <v-btn
                variant="text"
                @click="$router.back()"
                prepend-icon="mdi-chevron-left"
              >
                Trở lại
              </v-btn>

              <div class="text-header">
                <span class="text-uppercase mr-2"
                  >MÃ ĐƠN HÀNG. {{ order?.code }}</span
                >
                <span class="text-uppercase mr-2 text-primary">{{
                  order?.order_status
                }}</span>
              </div>
            </div>
          </v-card>
          <v-card class="pa-4 mt-3 shadow">
            <div class="order-details-wrapper">
              <div class="d-flex justify-between items-center">
                <h4 class="title text-uppercase mb-2 ls-25">
                  Địa chỉ giao hàng
                </h4>
                <v-chip
                  prepend-icon="mdi-truck"
                  variant="text"
                  class="text-capitalize"
                >
                  {{ order?.additional_details?.shipping_method?.name }}
                </v-chip>
              </div>
              <div class="mt-2">
                <span class="fs-16 mb-1"> {{ order?.customer_name }} </span>
                <div style="color: #0000008a">
                  <div>{{ order?.customer_phone }}</div>
                  <span>
                    {{ order?.shipping_address }}
                  </span>
                </div>
              </div>
            </div>
          </v-card>

          <v-row class="mt-1">
            <v-col cols="12" md="12">
              <v-card hover class="mx-auto pa-3 item-order shadow">
                <h4 class="title text-uppercase mb-2 ls-25">
                  Sản phẩm ({{ order?.order_items?.length }})
                </h4>
                <div>
                  <div
                    class="d-flex w-100 py-2 product-info-wrap"
                    v-for="item in order?.order_items"
                  >
                    <div class="order-product-image">
                      <img
                        class="object-contain"
                        :src="item.image"
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
                        <div class="quantity">x1</div>
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
                </div>
              </v-card>
            </v-col>
          </v-row>

          <v-card class="pa-4 mt-3 mb-10 shadow">
            <div class="order-details-wrapper">
              <h4 class="title text-uppercase mb-2 ls-25">Chi tiết hóa đơn</h4>
              <table class="order-table">
                <thead></thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th>Tạm tính:</th>
                    <td>{{ formatCurrency(order?.total_price) }}</td>
                  </tr>
                  <tr>
                    <th>Phí vận chuyển:</th>
                    <td>{{ formatCurrency(order?.shipping_fee) }}</td>
                  </tr>
                  <tr v-if="order?.discount">
                    <th>Mã giảm giá:</th>
                    <td>- {{ formatCurrency(order?.shipping_fee) }}</td>
                  </tr>
                  <tr>
                    <th>Phương thức thanh toán:</th>
                    <td>
                      {{ order?.additional_details?.payment_method.name }}
                    </td>
                  </tr>
                  <tr class="total border-b-none">
                    <th class="fs-20 fw-bold text-primary pt-4">Thành tiền:</th>
                    <td class="fs-20 fw-bold text-primary pt-4">
                      {{ formatCurrency(order?.final_price) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </v-card>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fw-bold {
  font-weight: bold !important;
}
.border-b-none {
  border-bottom: none;
}
.title {
  font-weight: normal;
  font-size: 16px;
}
th {
  font-weight: normal;
}
.text-header span::after {
  content: '|';
  opacity: 0.3;
  padding-left: 10px;
}
.text-header span:last-child:after {
  display: none;
}
.shadow {
  box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
}
</style>

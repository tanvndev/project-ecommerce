<script setup>
const route = useRoute()
const code = computed(() => route.query.code || '')
const order = ref(null)
const search = ref(code.value)

const { $axios } = useNuxtApp()

const getOrderByCode = async () => {
  if (!search.value) {
    return
  }

  const response = await $axios.get(`/orders/${search.value}/detail`)
  order.value = response.data
}

const debounceHandleSearch = debounce(getOrderByCode, 500)
</script>
<template>
  <div class="page-content mb-10 pb-2 mt-10">
    <div class="container">
      <h2 class="text-uppercase text-center mb-5">Tìm kiếm đơn hàng</h2>
      <div class="mt-3">
        <v-text-field
          v-model="search"
          @input="debounceHandleSearch"
          prepend-inner-icon="mdi-magnify"
          hint="Bạn có thể tìm kiếm theo ID đơn hàng"
          variant="outlined"
          clearable
          autofocus
          density="comfortable"
          placeholder="Bạn có thể tìm kiếm theo ID đơn hàng"
        ></v-text-field>
      </div>
      <div v-if="order">
        <ul class="order-view list-style-none">
          <li>
            <label>Mã đơn hàng</label>
            <strong>{{ order.code }}</strong>
          </li>
          <li>
            <label>Trạng thái đơn hàng</label>
            <strong>{{ order?.order_status }}</strong>
          </li>
          <li>
            <label>Trạng thái thanh toán</label>
            <strong>{{ order?.payment_status }}</strong>
          </li>
          <li>
            <label>Ngày đặt</label>
            <strong>{{ order?.ordered_at }}</strong>
          </li>
          <li>
            <label>Tổng tiền</label>
            <strong>{{ formatCurrency(order?.final_price) }}</strong>
          </li>
          <li>
            <label>Phương thức thanh toán</label>
            <strong>{{
              order.additional_details?.payment_method?.name
            }}</strong>
          </li>
        </ul>
        <!-- End of Order View -->

        <div class="order-details-wrapper mb-5">
          <h4 class="title text-uppercase ls-25 mb-5">Chi tiết đơn hàng</h4>
          <table class="order-table">
            <thead>
              <tr>
                <th class="text-dark">Sản phẩm</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in order?.order_items" :key="item.id">
                <td>
                  <div class="d-flex items-center">
                    <div class="order-product-image mr-3">
                      <img
                        class="object-contain"
                        :src="item.image"
                        :alt="item.product_variant_name"
                      />
                    </div>
                    <div>
                      <NuxtLink
                        :to="`/product/${item.slug}-${item.product_id}`"
                      >
                        {{ item.product_variant_name }}
                      </NuxtLink>
                      &nbsp;<strong>x {{ item.quantity }}</strong>
                      <br />
                      <span class="fs-13"
                        >Phân loại: {{ item.attribute_values }}</span
                      >
                    </div>
                  </div>
                </td>
                <td>
                  <div class="product-price">
                    <ins class="new-price">{{
                      formatCurrency(item.sale_price || item.price)
                    }}</ins>
                    <del class="old-price" v-if="item.sale_price">{{
                      formatCurrency(item.price)
                    }}</del>
                  </div>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th>Tạm tính:</th>
                <td>{{ formatCurrency(order.total_price) }}</td>
              </tr>
              <tr>
                <th>Phí vận chuyển:</th>
                <td>{{ formatCurrency(order.shipping_fee) }}</td>
              </tr>
              <tr v-if="order.discount">
                <th>Mã giảm giá:</th>
                <td>- {{ formatCurrency(order.discount) }}</td>
              </tr>
              <tr>
                <th>Phương thức thanh toán:</th>
                <td>{{ order.additional_details?.payment_method?.name }}</td>
              </tr>
              <tr class="total">
                <th class="border-no">Total:</th>
                <td class="border-no">
                  {{ formatCurrency(order.final_price) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- End of Order Details -->

        <div class="sub-orders mb-10" v-if="order?.note">
          <div class="alert alert-icon alert-inline mb-5">
            <i class="w-icon-exclamation-triangle"></i>
            <strong>Lời nhắn: </strong>
            {{ order.note }}
          </div>
        </div>
        <!-- End of Sub Orders-->

        <div class="row">
          <div class="col-sm-12 mb-8">
            <div class="ecommerce-address billing-address">
              <h4 class="title title-underline ls-25 font-weight-bold">
                Địa chỉ giao hàng
              </h4>
              <address class="mb-4">
                <div class="customer-user">
                  <div>
                    <div class="v2-checkout-address-inner">
                      <div>
                        <div class="v2-address-title-container">
                          <span class="v2-address-title">{{
                            order.customer_name
                          }}</span>
                          <span class="v2-mobile">{{
                            order.customer_phone
                          }}</span>
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
                          {{ order.shipping_address }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </address>
            </div>
          </div>
        </div>
        <!-- End of Account Address -->
      </div>

      <div v-else>
        <v-empty-state
          icon="mdi-magnify"
          text="Chúng tôi không thể tìm thấy đơn hàng của bạn vui lòng thử lại."
          title="Không có dữ liệu."
        ></v-empty-state>
      </div>

      <NuxtLink
        to="/"
        class="btn btn-dark btn-rounded btn-icon-left btn-back mt-6"
      >
        <i class="w-icon-long-arrow-left"></i>
        Quay lại trang chủ
      </NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.product-price .new-price {
  font-weight: normal;
}
.order-product-image {
  flex-shrink: 0;
  width: 85px;
  height: 85px;
  border-radius: 4px;
  overflow: hidden;
}

.order-product-image img {
  width: 85px;
  height: 85px;
  object-fit: contain;
}
</style>

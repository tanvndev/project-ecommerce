<script setup>

useSeoMeta({
  title: 'Đơn hàng thành công',
  ogTitle: 'Đơn hàng thành công',
  description: 'Đơn hàng thành công.',
  ogDescription: 'Đơn hàng thành công.',
})

const { $axios } = useNuxtApp()
const route = useRoute()

const order = ref(null)
const orderCode = route.query.code

const getOrder = async () => {
  const response = await $axios.get(`/orders/${orderCode}/detail`)
  order.value = response.data
}

onMounted(async () => {
  if (!orderCode) {
    return navigateTo('/')
  }

  await getOrder()

  if (!order.value) {
    return navigateTo('/')
  }
})
</script>
<template>
  <!-- Start of Main -->
  <main class="main order" v-if="order">
    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav">
      <div class="container">
        <ul class="breadcrumb shop-breadcrumb bb-no">
          <li class="passed">
            <NuxtLink to="/cart">Giỏ hàng</NuxtLink>
          </li>
          <li class="passed">
            <NuxtLink to="/checkout">Thanh toán</NuxtLink>
          </li>
          <li class="active">
            <a href="#">Hoàn tất đơn hàng</a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End of Breadcrumb -->

    <!-- Start of PageContent -->
    <div class="page-content mb-10 pb-2">
      <div class="container">
        <div class="order-success text-center font-weight-bolder text-dark">
          <i class="fas fa-check" style="color: green"></i>
          Cảm ơn bạn. Đơn hàng của bạn đã được nhận.
        </div>
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

        <NuxtLink
          to="/"
          class="btn btn-dark btn-rounded btn-icon-left btn-back mt-6"
        >
          <i class="w-icon-long-arrow-left"></i>
          Quay lại trang chủ
        </NuxtLink>
      </div>
    </div>
    <!-- End of PageContent -->
  </main>
  <!-- End of Main -->
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

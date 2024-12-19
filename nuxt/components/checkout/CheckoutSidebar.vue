<script setup>
import { useCartStore, useOrderStore } from '#imports'

const cartStore = useCartStore()
const orderStore = useOrderStore()

const carts = computed(() => cartStore.getCartSelected)
const totalAmount = computed(() => cartStore.getTotalAmount)
const shippingFee = computed(() => orderStore.getShippingFee)
const discount = computed(() => orderStore.getDiscount)
const finalTotal = computed(() => {
  const total = +totalAmount.value + +shippingFee.value - +discount.value
  if (total < 0) {
    return 0
  }
  return total
})
</script>

<template>
  <div>
    <!-- Payment method -->
    <CheckoutPaymentMethod />

    <div class="order-summary-inner">
      <div class="summary-section mb-4">
        <h3 class="title text-uppercase ls-10 mb-3 mt-5">Thông tin đơn hàng</h3>
        <div class="checkout-summary">
          <div class="checkout-summary-row">
            <div class="checkout-summary-main">
              <div class="checkout-summary-label">
                Tạm tính ({{ carts?.length }} Sản phẩm)
              </div>
              <div class="checkout-summary-value">
                <span class="checkout-summary-noline-value">{{
                  formatCurrency(totalAmount)
                }}</span>
              </div>
            </div>
          </div>
          <div class="checkout-summary-row">
            <div class="checkout-summary-main">
              <div class="checkout-summary-label">Phí vận chuyển</div>
              <div class="checkout-summary-value">
                <span class="checkout-summary-noline-value">{{
                  formatCurrency(shippingFee)
                }}</span>
              </div>
            </div>
          </div>
          <div class="checkout-summary-row" v-if="discount">
            <div class="checkout-summary-main">
              <div class="checkout-summary-label">Mã giảm giá</div>
              <div class="checkout-summary-value">
                <span class="checkout-summary-noline-value"
                  >- {{ formatCurrency(discount ?? 0) }}</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="order-summary-footer border-t pt-4">
      <div class="d-flex justify-between items-center">
        <div class="checkout-order-total-title">Tổng cộng:</div>
        <div class="checkout-order-total-fee">
          {{ formatCurrency(finalTotal) }}
        </div>
      </div>

      <div class="form-group place-order pt-5">
        <button type="submit" class="btn btn-dark btn-block btn-rounded">
          Đặt hàng
        </button>
      </div>
    </div>
  </div>
</template>

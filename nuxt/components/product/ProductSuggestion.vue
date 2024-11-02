<script setup>
import _ from 'lodash'

const { $axios } = useNuxtApp()
const comboItem = ref({})
const suggestedProducts = ref([])
const cartStore = useCartStore()
const router = useRouter()

const props = defineProps({
  variant: {
    type: Object,
    default: () => {},
  },
})

const getSuggestedProducts = async () => {
  if (!props.variant?.id) {
    return
  }
  try {
    const response = await $axios.get(`/products/${props.variant?.id}/suggest`)

    suggestedProducts.value = response.data

    suggestedProducts.value.forEach((item) => {
      comboItem.value[item.id] = true
    })
  } catch (error) {
    console.log(error)
  }
}

const totalPrice = computed(() => {
  return _.sumBy(
    _.filter(_.toPairs(comboItem.value), ([, isSelected]) => isSelected),
    ([id]) => {
      const product = _.find(
        suggestedProducts.value,
        (item) => item.id === parseInt(id)
      )
      return product ? parseFloat(product.sale_price || product.price) : 0
    }
  )
})

const handleBuyNow = async () => {
  if (totalPrice.value) {
    const productVariantIds = _.toPairs(comboItem.value)
      .map(([id, isSelected]) => {
        if (isSelected) {
          return parseInt(id)
        }
      })
      .filter((item) => item)

    productVariantIds.push(props.variant.id)

    await cartStore.buyNowMulti({ product_variant_id: productVariantIds })

  }
}

watch(
  () => props.variant,
  () => {
    getSuggestedProducts()
  },
  { immediate: true }
)
</script>
<template>
  <section
    class="vendor-product-section combo-detail"
    v-if="suggestedProducts.length"
  >
    <div class="title-link-wrapper mb-4">
      <h4 class="title text-left">Sản phẩm gợi ý</h4>
    </div>

    <div>
      <div class="buy-access ba-col2">
        <div class="row align-items-center d-flex">
          <div class="col-lg-2">
            <div class="d-flex items-center">
              <div class="bunbled">
                <div class="img-sp">
                  <v-img height="150" :src="resizeImage(variant.image)" />
                </div>
                <h3>{{ variant.name }}</h3>
                <strong>{{
                  formatCurrency(variant.sale_price || variant.price)
                }}</strong>
                <small v-if="variant.sale_price">
                  <del>{{ formatCurrency(variant.price) }}</del>
                </small>
              </div>

              <div class="plus">
                <i class="w-icon-plus fs-50"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <ul class="list-access">
              <li
                class="active"
                v-for="item in suggestedProducts"
                :key="item.id"
              >
                <div class="img-access">
                  <v-img height="300" :src="resizeImage(item.image)" />
                </div>
                <h3>
                  <a href="#">{{ item.name }}</a>
                </h3>
                <strong>{{
                  formatCurrency(item.sale_price || item.price)
                }}</strong>
                <small v-if="item.sale_price">
                  <del>{{ formatCurrency(item.price) }}</del>
                </small>

                <div class="checkbox-wrapper">
                  <v-checkbox
                    v-model="comboItem[item.id]"
                    color="primary"
                  ></v-checkbox>
                </div>
              </li>
            </ul>
          </div>

          <div class="col-lg-3">
            <div class="total-bill">
              <div>
                <strong>{{
                  formatCurrency(
                    totalPrice + Number(variant.sale_price || variant.price)
                  )
                }}</strong>
              </div>
              <a
                href="buy-now"
                @click.prevent="handleBuyNow"
                class="btn border-none btn-rounded btn-icon-right"
                :class="{ disabled: totalPrice == 0 }"
                >Mua <b>{{ comboItem?.length }}</b> sản phẩm</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<style scoped>
.btn.disabled {
  cursor: not-allowed;
  opacity: 0.5;
  pointer-events: none;
}
</style>

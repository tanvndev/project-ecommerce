<script setup>
import 'swiper/css'
import { Autoplay, Grid, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'

const productStore = useProductStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const modules = [Navigation, Autoplay, Grid]
const slider = ref(null)
const products = computed(() => productStore.getProductRecommendations)
const { $axios } = useNuxtApp()
const isReload = computed(() => productStore.getIsReload)

const onSwiper = (swiper) => {
  slider.value = swiper
}

const getProductRecommendation = async () => {
  try {
    const response = await $axios.get('/products/recommendation')

    productStore.setProductRecommendations(response.data)
  } catch (error) {}
}

const addToCart = async (variantId) => {
  if (!variantId) {
    return toast('Có lỗi vui lòng thử lại.', 'error')
  }

  const payload = {
    product_variant_id: variantId,
  }

  await cartStore.addToCart(payload)
}

const addToWishlist = async (variantId) => {
  if (!variantId) {
    return toast('Có lỗi vui lòng thử lại.', 'error')
  }

  const payload = {
    product_variant_id: variantId,
  }

  await wishlistStore.addToWishlist(payload)
}
onMounted(() => {
  if (isReload.value || products.value.length == 0) {
    getProductRecommendation()
  }
})
</script>
<template>
  <section class="vendor-product-section" v-if="products?.length">
    <div class="title-link-wrapper mb-4">
      <h4 class="title text-left">Sản phẩm dành cho bạn</h4>
      <NuxtLink
        to="/category"
        class="btn btn-dark btn-link btn-slide-right btn-icon-right"
        >Xem tất cả<i class="w-icon-long-arrow-right"></i
      ></NuxtLink>
    </div>
    <div class="swiper-theme">
      <Swiper
        @swiper="onSwiper"
        :modules="modules"
        :slides-per-view="4"
        :navigation="false"
        :loop="true"
        :space-between="20"
        :grid="{ rows: 2, fill: 'row' }"
        :autoplay="{
          delay: 5000,
          pauseOnMouseEnter: true,
          disableOnInteraction: false,
        }"
      >
        <SwiperSlide v-for="product in products" :key="product.id">
          <div class="swiper-slide product">
            <figure class="product-media">
              <NuxtLink
                :to="`/product/${product.slug}-${product.product_id}`"
                :title="product.name"
              >
                <img
                  :src="resizeImage(product.image)"
                  alt="Product"
                  width="300"
                  height="338"
                />
              </NuxtLink>
              <div class="product-action-vertical">
                <a
                  @click.prevent="addToCart(product?.id)"
                  :href="product.slug"
                  class="btn-product-icon btn-cart w-icon-cart"
                  title="Thêm vào giỏ hàng"
                ></a>
                <a
                  @click.prevent="addToWishlist(product?.id)"
                  :href="product.slug"
                  class="btn-product-icon btn-wishlist w-icon-heart"
                  title="Thêm vào ưa thích"
                ></a>
              </div>
              <div class="product-label-group" v-if="product?.discount">
                <label class="product-label label-discount"
                  >Giảm {{ product?.discount }}%</label
                >
              </div>
            </figure>
            <div class="product-details">
              <h4 class="product-name">
                <NuxtLink
                  :to="`/product/${product.slug}-${product.product_id}`"
                  :title="product.name"
                >
                  {{ product.name }}
                </NuxtLink>
              </h4>
              <div class="ratings-container">
                <div class="ratings-full">
                  <span
                    class="ratings"
                    :style="`width: ${product?.reviews?.avg_percent}%`"
                  ></span>
                  <span class="tooltiptext tooltip-top">{{
                    product?.reviews?.avg
                  }}</span>
                </div>
                <NuxtLink
                  :to="`/product/${product.slug}-${product.product_id}`"
                  class="rating-reviews"
                  >({{ product?.reviews?.count }} đánh giá)</NuxtLink
                >
              </div>
              <div v-html="handleRenderPrice(product)"></div>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
      <div>
        <button class="button-slide prev" @click.stop="slider.slidePrev()">
          <i class="w-icon-angle-left"></i>
        </button>
        <button class="button-slide next" @click.stop="slider.slideNext()">
          <i class="w-icon-angle-right"></i>
        </button>
      </div>
    </div>
  </section>
</template>
<style scoped>
.product-media {
  background-color: #f5f6f7;
  border-radius: 10px !important;
  transform: translateY(0);
  transition: all 0.3s linear;
  padding: 0 20px;
}
.product-media img {
  object-fit: contain !important;
  width: 300px;
  height: 330px;
  border-radius: 10px !important;
  mix-blend-mode: darken;
  transition: all 0.2s ease-in-out;
}

.product-media:hover img {
  transform: translateY(-12px);
}
</style>

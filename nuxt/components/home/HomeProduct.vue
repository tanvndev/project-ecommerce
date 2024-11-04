<script setup>
import {
  handleRenderPrice,
  resizeImage,
  toast,
  useCartStore,
  useWishlistStore,
} from '#imports'
import 'swiper/css'
import { Autoplay, Grid, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { ref } from 'vue'

const props = defineProps({
  items: {
    type: [Array, Object],
    default: () => [],
  },
  title: {
    type: String,
    default: () => '',
  },
})

const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const modules = [Navigation, Grid, Autoplay]
const slider = ref(null)
const onSwiper = (swiper) => {
  slider.value = swiper
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
</script>
<template>
  <v-lazy
    :min-height="500"
    :options="{ threshold: 0.5 }"
    transition="fade-transition"
  >
    <div class="product-wrapper-1 mt-5 mb-5">
      <div class="title-link-wrapper pb-1 mb-4">
        <h2 class="title ls-normal mb-0">{{ title }}</h2>
        <NuxtLink
          to="category"
          class="font-size-normal font-weight-bold ls-25 mb-0"
          >Xem tất cả <i class="w-icon-long-arrow-right"></i
        ></NuxtLink>
      </div>
      <div class="row">
        <!-- End of Banner -->
        <div class="col-lg-12 col-sm-8">
          <div class="swiper-theme slider-wrapper">
            <swiper
              @swiper="onSwiper"
              :modules="modules"
              :slides-per-view="4"
              :loop="true"
              :navigation="false"
              :grid="{ rows: 2, fill: 'row' }"
              :space-between="20"
              :autoplay="{
                delay: 5000,
                pauseOnMouseEnter: true,
                disableOnInteraction: false,
              }"
            >
              <!-- <div class="row cols-xl-4 cols-lg-3 cols-2"> -->
              <swiper-slide v-for="item in items" :key="item.id">
                <div class="product-col">
                  <div class="product-wrap product text-center">
                    <figure class="product-media">
                      <NuxtLink
                        :title="item?.name"
                        :to="`product/${item.slug}-${item.product_id}`"
                      >
                        <img
                          :src="resizeImage(item.image, 400, 300)"
                          :alt="item.name"
                          loading="lazy"
                        />
                      </NuxtLink>
                      <div class="product-action-vertical">
                        <a
                          @click.prevent="addToCart(item?.id)"
                          :href="item.slug"
                          class="btn-product-icon btn-cart w-icon-cart"
                          title="Thêm vào giỏ hàng"
                        ></a>
                        <a
                          @click.prevent="addToWishlist(item?.id)"
                          :href="item.slug"
                          class="btn-product-icon btn-wishlist w-icon-heart"
                          title="Thêm vào ưa thích"
                        ></a>
                      </div>
                      <div class="product-label-group" v-if="item?.discount">
                        <label class="product-label label-discount"
                          >Giảm {{ item?.discount }}%</label
                        >
                      </div>
                    </figure>
                    <div class="product-details">
                      <h4 class="product-name">
                        <NuxtLink
                          :title="item?.name"
                          :to="`product/${item.slug}-${item.product_id}`"
                          >{{ item.name }}</NuxtLink
                        >
                      </h4>
                      <div class="ratings-container">
                        <div class="ratings-full">
                          <span
                            class="ratings"
                            :style="`width: ${item?.reviews?.avg_percent}%`"
                          ></span>
                          <span class="tooltiptext tooltip-top">{{
                            item?.reviews?.avg
                          }}</span>
                        </div>
                        <NuxtLink
                          :to="`product/${item.slug}-${item.product_id}`"
                          class="rating-reviews"
                          >({{ item?.reviews?.count }} đánh giá)</NuxtLink
                        >
                      </div>
                      <div v-html="handleRenderPrice(item)"></div>
                    </div>
                  </div>
                </div>
              </swiper-slide>
              <!-- </div> -->
            </swiper>
            <div>
              <button
                class="button-slide prev"
                @click.stop="slider.slidePrev()"
              >
                <i class="w-icon-angle-left"></i>
              </button>
              <button
                class="button-slide next"
                @click.stop="slider.slideNext()"
              >
                <i class="w-icon-angle-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-lazy>
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

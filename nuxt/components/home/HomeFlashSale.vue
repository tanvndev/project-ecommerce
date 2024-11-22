<script setup>
import { resizeImage } from '#imports'
import 'swiper/css'
import { Autoplay, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'

const productCatalogueStore = useProductCatalogueStore()
const modules = [Navigation, Autoplay]
const productCatalogues = computed(
  () => productCatalogueStore.getProductCatalogues
)
const slider = ref(null)

const onSwiper = (swiper) => {
  slider.value = swiper
}
</script>
<template>
  <!-- Categories -->
  <v-lazy
    :min-height="200"
    :options="{ threshold: 0.5 }"
    transition="fade-transition"
  >
    <section
      class="category-section top-category pt-10 pb-10"
      v-if="productCatalogues.length"
    >
      <div class="container pb-2">
        <div class="title-link-wrapper pb-1 mb-4">
          <div class="flash-sale-title">
            <div class="flash-sale-title-text">
              <img src="/assets/images/flash_sale.png" alt="" />
            </div>
            <div class="time">
              <span>01</span>
              <span>20</span>
              <span>30</span>
            </div>
          </div>
          <NuxtLink
            to="category"
            class="font-size-normal font-weight-bold ls-25 mb-0"
            >Xem tất cả <i class="w-icon-long-arrow-right"></i
          ></NuxtLink>
        </div>
        <div class="category-wrapper">
          <div class="swiper-theme pg-show">
            <swiper
              @swiper="onSwiper"
              :modules="modules"
              :slides-per-view="6"
              :loop="true"
              :infinite="true"
              :navigation="false"
              :space-between="20"
              :autoplay="{
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
              }"
            >
              <swiper-slide v-for="item in productCatalogues" :key="item?.id">
                <div class="product-col">
                  <div class="product-wrap product text-center">
                    <figure class="product-media">
                      <NuxtLink
                        :title="item?.name"
                        :to="`product/${item?.slug}-${item?.product_id}`"
                      >
                        <img
                          :src="resizeImage(item?.image)"
                          :alt="item?.name"
                          loading="lazy"
                        />
                      </NuxtLink>
                      <div class="product-action-vertical">
                        <a
                          @click.prevent="addToCart(item?.id)"
                          :href="item?.slug"
                          class="btn-product-icon btn-cart w-icon-cart"
                          title="Thêm vào giỏ hàng"
                        ></a>
                        <a
                          @click.prevent="addToWishlist(item?.id)"
                          :href="item?.slug"
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
                          :to="`/product/${item?.slug}-${item?.product_id}`"
                          >{{ item?.name }}</NuxtLink
                        >
                      </h4>
                      <div v-html="handleRenderPrice(item)"></div>
                    </div>

                    <div class="product-process" style="height: 16px">
                      <div class="text">ĐANG BÁN CHẠY</div>
                      <div
                        class="process-percent"
                        style="width: 100%; border-radius: 8px 0px 0px 8px"
                      ></div>
                      <div
                        class="process-percent-bg"
                        style="border-radius: 8px"
                      ></div>
                    </div>
                  </div>
                </div>
              </swiper-slide>
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
    </section>
  </v-lazy>
</template>
<style scoped>
.flash-sale-title {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}
.flash-sale-title-text {
  margin-right: 13px;
  display: flex;
  align-items: center;
}
.flash-sale-title .time {
  height: 28px;
}
.flash-sale-title .time span {
  font-size: 14px;
  color: #fff;
  margin-right: 5px;
  background-color: #000;
  padding: 3px 6px;
  font-weight: 600;
  border-radius: 4px;
}

.product-media {
  background-color: #f5f6f7;
  border-radius: 10px !important;
  transform: translateY(0);
  transition: all 0.3s linear;
  padding: 0 10px;
}
.product-media img {
  object-fit: contain !important;
  width: 300px;
  height: 200px;
  border-radius: 10px !important;
  mix-blend-mode: darken;
  transition: all 0.2s ease-in-out;
}

.product-media:hover img {
  transform: translateY(-12px);
}
.product-process {
  height: 16px;
  position: relative;
  width: 100%;
}
.product-process .text {
  align-items: center;
  color: #fff;
  display: flex;
  font-size: 12px;
  font-weight: 700;
  height: inherit;
  justify-content: center;
  left: 0;
  position: absolute;
  text-shadow: 0 0 8px rgba(0, 0, 0, 0.12);
  text-transform: uppercase;
  top: 0;
  width: inherit;
  z-index: 3;
}
.product-process .process-percent,
.product-process .process-percent-bg {
  position: absolute;
  height: inherit;
  left: 0;
  top: 0;
}
.product-process .process-percent {
  background: linear-gradient(270deg, #ffb000, #eb1717);
  z-index: 2;
}
.product-process .process-percent-bg {
  background: #ffbda6;
  width: inherit;
  z-index: 1;
}
</style>

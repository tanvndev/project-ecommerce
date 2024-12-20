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
  <v-lazy :options="{ threshold: 0.5 }" transition="fade-transition">
    <div class="container">
      <div class="icon-box-wrapper br-sm mt-6 mb-6">
        <div class="row cols-md-4 cols-sm-3 cols-1">
          <div class="icon-box icon-box-side icon-box-primary">
            <span class="icon-box-icon icon-shipping">
              <i class="w-icon-truck"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title font-weight-bold mb-1">
                Miễn phí vận chuyển và trả hàng
              </h4>
              <p class="text-default">Cho tất cả các đơn hàng đặc biệt</p>
            </div>
          </div>
          <div class="icon-box icon-box-side icon-box-primary">
            <span class="icon-box-icon icon-payment">
              <i class="w-icon-bag"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title font-weight-bold mb-1">
                Thanh toán an toàn
              </h4>
              <p class="text-default">Chúng tôi đảm bảo thanh toán an toàn</p>
            </div>
          </div>
          <div class="icon-box icon-box-side icon-box-primary icon-box-money">
            <span class="icon-box-icon icon-money">
              <i class="w-icon-money"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title font-weight-bold mb-1">
                Đảm bảo hoàn tiền
              </h4>
              <p class="text-default">Hoàn tiền nào trong vòng 30 ngày</p>
            </div>
          </div>
          <div class="icon-box icon-box-side icon-box-primary icon-box-chat">
            <span class="icon-box-icon icon-chat">
              <i class="w-icon-chat"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title font-weight-bold mb-1">
                Hỗ trợ khách hàng
              </h4>
              <p class="text-default">Hỗ trợ khách hàng 24/7</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-lazy>
  <!-- Categories -->
  <v-lazy
    :min-height="200"
    :options="{ threshold: 0.5 }"
    transition="fade-transition"
  >
    <section
      class="category-section top-category bg-grey pt-10 pb-10"
      v-if="productCatalogues.length"
    >
      <div class="container pb-2">
        <h2
          class="title justify-content-center pt-1 ls-normal mb-5 text-uppercase"
        >
          Danh Mục Sản Phẩm
        </h2>
        <div class="category-wrapper">
          <div class="swiper-theme pg-show">
            <swiper
              @swiper="onSwiper"
              :modules="modules"
              :slides-per-view="6"
              :loop="true"
              :infinite="true"
              :navigation="false"
              :autoplay="{
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
              }"
            >
              <swiper-slide
                v-for="productCatalogue in productCatalogues"
                :key="productCatalogue?.id"
              >
                <div
                  class="category category-classic category-absolute overlay-zoom br-xs mx-2"
                >
                  <NuxtLink
                    :to="`/category?catalogues=${productCatalogue.id}`"
                    class="category-media"
                    :title="productCatalogue.name"
                  >
                    <img
                      :src="resizeImage(productCatalogue.image, 300)"
                      :alt="productCatalogue.name"
                    />
                  </NuxtLink>
                  <div class="category-content">
                    <h4 class="category-name">{{ productCatalogue.name }}</h4>
                    <NuxtLink
                      :title="productCatalogue.name"
                      :to="`/category?catalogues=${productCatalogue.id}`"
                      class="btn btn-primary btn-link btn-underline"
                      >Xem ngay</NuxtLink
                    >
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

.category-media {
  background-color: #fff;
  display: block;
  padding: 14px 5px;
}
.category-media img {
  margin-top: -30px;
  height: 186px;
  width: 186px;
  object-fit: contain;
  background-color: #fff;
  border-radius: 6px;
  mix-blend-mode: darken;
}
</style>

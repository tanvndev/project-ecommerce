<script setup>
import { handleRenderPrice, resizeImage } from '#imports'
import 'swiper/css'
import { Autoplay, Grid, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'
const modules = [Navigation, Autoplay, Grid]
const slider = ref(null)

const props = defineProps({
  product: {
    type: [Object, Array],
    default: () => [],
  },
})

const onSwiper = (swiper) => {
  slider.value = swiper
}
</script>

<template>
  <aside
    class="sidebar product-sidebar sidebar-fixed right-sidebar sticky-sidebar-wrapper"
  >
    <div class="sidebar-overlay"></div>
    <a class="sidebar-close" href="#"><i class="close-icon"></i></a>
    <div class="sidebar-content scrollable">
      <div class="sticky-sidebar">
        <div class="widget widget-icon-box mb-6">
          <div class="icon-box icon-box-side">
            <span class="icon-box-icon text-dark">
              <i class="w-icon-truck"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title">Hoàn trả miễn phí</h4>
              <p>Với tất cả đơn hàng</p>
            </div>
          </div>
          <div class="icon-box icon-box-side">
            <span class="icon-box-icon text-dark">
              <i class="w-icon-bag"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title">Thanh toán an toàn</h4>
              <p>Đảm bảo thanh toán an toàn</p>
            </div>
          </div>
          <div class="icon-box icon-box-side">
            <span class="icon-box-icon text-dark">
              <i class="w-icon-money"></i>
            </span>
            <div class="icon-box-content">
              <h4 class="icon-box-title">Bảo đảm hoàn tiền</h4>
              <p>Hoàn tiền trong vòng 30 ngày</p>
            </div>
          </div>
        </div>
        <!-- End of Widget Icon Box -->

        <div class="widget widget-products" v-if="product?.upsells?.length > 0">
          <div
            class="title-link-wrapper mb-2 d-flex justify-between items-center"
          >
            <h4 class="title title-link font-weight-bold">
              Có thể bạn sẽ thích
            </h4>
            <div>
              <button
                class="button-slide-2 prev"
                @click.stop="slider.slidePrev()"
              >
                <i class="w-icon-angle-left"></i>
              </button>
              <button
                class="button-slide-2 next"
                @click.stop="slider.slideNext()"
              >
                <i class="w-icon-angle-right"></i>
              </button>
            </div>
          </div>

          <div class="nav-top">
            <div class="swiper-theme nav-top">
              <Swiper
                @swiper="onSwiper"
                :modules="modules"
                :slides-per-view="1"
                :navigation="false"
                :grid="{ rows: 4, fill: 'row' }"
                :loop="true"
              >
                <SwiperSlide v-for="item in product?.upsells" :key="item.id">
                  <div class="widget-col">
                    <div class="product product-widget">
                      <figure class="product-media">
                        <NuxtLink
                          :to="`/product/${item.slug}-${item.product_id}`"
                          :title="item?.name"
                        >
                          <img
                            :src="resizeImage(item?.image, 200, 300)"
                            :alt="item?.name"
                            class="product-image"
                          />
                        </NuxtLink>
                      </figure>
                      <div class="product-details">
                        <h4 class="product-name">
                          <NuxtLink
                            :title="item?.name"
                            :to="`/product/${item.slug}-${item.product_id}`"
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
                              item?.reviews?.count
                            }}</span>
                          </div>
                        </div>
                        <div v-html="handleRenderPrice(item)"></div>
                      </div>
                    </div>
                  </div>
                </SwiperSlide>
              </Swiper>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.product-image {
  width: 100px;
  height: 112px;
  object-fit: cover;
  border-radius: 4px;
  background-color: #f5f6f7;
}
.product-widget .product-price {
  font-size: 1.4rem;
}
.product-widget .product-media {
  background-color: #f5f6f7;
  padding: 8px 12px !important;
}
.product-widget .product-media img {
  object-fit: contain;
  mix-blend-mode: darken;
  background-color: #f5f6f7;
}
</style>

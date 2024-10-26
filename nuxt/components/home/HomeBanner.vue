<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination, Navigation } from 'swiper/modules'
import 'swiper/css'

const modules = [Autoplay, Pagination, Navigation]

const banners = ref([
  {
    id: 1,
    image: 'http://127.0.0.1:8000/images/2024/10/banner1jpg_671baa5a674b8.webp',
    link: '/product/airpods-pro-2nd-gen-usb-c-mau-trang-13',
  },
  {
    id: 2,
    image: 'http://127.0.0.1:8000/images/2024/10/banner2jpg_671baa59f3d0a.webp',
    link: '/product/iphone-16-plus-mau-hong-256gb-2',
  },
  {
    id: 3,
    image:
      'http://127.0.0.1:8000/images/2024/10/bananer3png_671baa597f2cc.webp',
    link: '/product/macbook-pro-16-inch-m3-max-mau-den-128gb-512gb-3',
  },
  {
    id: 4,
    image: 'http://127.0.0.1:8000/images/2024/10/banner4jpg_671baa590dd44.webp',
    link: '/product/apple-watch-series-9-gps-cellular-vien-nhom-day-the-thao-mau-bac-42mm-9',
  },
  {
    id: 5,
    image: 'http://127.0.0.1:8000/images/2024/10/banner5png_671baa5826c14.webp',
    link: '/product/ipad-pro-m4-11-inch-wifi-mau-den-256gb-6',
  },
])

const slider = ref(null)
const onSwiper = (swiper) => {
  slider.value = swiper
}
</script>

<template>
  <v-lazy
    :min-height="200"
    :options="{ threshold: 0.5 }"
    transition="fade-transition"
  >
    <section class="intro-section position-relative">
      <div class="nav-inner pg-inner pg-xxl-hide nav-xxl-show nav-hide">
        <swiper
          @swiper="onSwiper"
          :modules="modules"
          :slides-per-view="1"
          :autoplay="true"
          :loop="true"
          :infinite="true"
          :pagination="{ clickable: true }"
          :navigation="false"
        >
          <swiper-slide v-for="banner in banners" :key="banner.id">
            <div class="banner" style="background-color: #ebeef2">
              <figure class="slide-image skrollable">
                <NuxtLink :to="banner.link">
                  <v-img :src="banner.image" alt="Banner" />
                </NuxtLink>
              </figure>
            </div>
          </swiper-slide>
        </swiper>

        <div>
          <button class="button-slide prev" @click.stop="slider.slidePrev()">
            <i class="w-icon-angle-left"></i>
          </button>
          <button class="button-slide next" @click.stop="slider.slideNext()">
            <i class="w-icon-angle-right"></i>
          </button>
        </div>
      </div>
      <!-- End of . -->
    </section>
  </v-lazy>
</template>
<style scoped>
.intro-section:hover .button-slide {
  visibility: visible;
  opacity: 1;
}

.button-slide.next {
  right: 5px;
  left: auto;
}
.button-slide.prev {
  left: 5px;
  right: auto;
}
</style>

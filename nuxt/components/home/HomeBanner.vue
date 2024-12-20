<script setup>
import 'swiper/css'
import { Autoplay, Navigation, Pagination } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'

const modules = [Autoplay, Pagination, Navigation]
const { $axios } = useNuxtApp()
const banners = ref([])


const slider = ref(null)
const onSwiper = (swiper) => {
  slider.value = swiper
}
const getAllBanners = async () => {
  try {
    const response = await $axios.get('/sliders/home-banner/get')

    banners.value = response.data?.items
  } catch (error) {
    console.error('Error fetching banners:', error)
  }
}

onMounted(async () => {
  await getAllBanners()
})
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
                <NuxtLink :to="banner?.url">
                  <v-img :src="banner.image" :alt="banner?.description" />
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

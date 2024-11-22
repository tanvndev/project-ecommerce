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
        <div>
          <h2 class="title">Danh Mục Sản Phẩm</h2>
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

<script setup>
import '~/assets/css/main.min.css'
import { useLoadingStore } from '#imports'

useSeoMeta({
  title: 'Trang chủ',
  ogTitle: 'Trang chủ',
  description: 'This is my amazing site, let me tell you all about it.',
  ogDescription: 'This is my amazing site, let me tell you all about it.',
  ogImage: 'https://example.com/image.png',
  twitterCard: 'summary_large_image',
})

const widgetCodes = ref([])
const widgets = ref([])
const { $axios } = useNuxtApp()
const loadingStore = useLoadingStore()
const currentWidgetIndex = ref(0)
const isLoading = ref(false)

const getAllWidgetCode = async () => {
  loadingStore.setLoading(true)
  try {
    const response = await $axios.get('/widgets/codes')
    widgetCodes.value = response.data
  } catch (error) {
    console.error('Error fetching widget codes:', error)
  } finally {
    loadingStore.setLoading(false)
  }
}

const getWidgetByCode = async (code) => {
  if (isLoading.value) return
  isLoading.value = true
  try {
    const response = await $axios.get(`widgets/${code}/detail`)
    widgets.value = [...widgets.value, ...response.data]
  } catch (error) {
    console.error('Error fetching widget:', error)
  } finally {
    isLoading.value = false
  }
}

const handleScroll = () => {
  const mainElement = document.querySelector('.main')
  if (!mainElement) return

  const mainElementHeight = mainElement.clientHeight
  const mainElementTop =
    mainElement.getBoundingClientRect().top + window.scrollY
  const scrollPosition = window.innerHeight + window.scrollY

  if (scrollPosition + 500 >= mainElementTop + mainElementHeight) {
    if (
      currentWidgetIndex.value < widgetCodes.value.length &&
      widgets.value.length < widgetCodes.value.length &&
      !isLoading.value
    ) {
      getWidgetByCode(widgetCodes.value[currentWidgetIndex.value].code)
      currentWidgetIndex.value++
    } else if (currentWidgetIndex.value >= widgetCodes.value.length) {
      window.removeEventListener('scroll', handleScroll)
    }
  }
}

onMounted(async () => {
  await getAllWidgetCode()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <!-- Home Banner -->
  <HomeBanner />

  <!-- Home Category -->
  <HomeCategory />

  <div class="container">
    <div v-for="widget in widgets" :key="widget.id">
      <HomeAdvertisement
        :items="widget.items"
        v-if="widget.type == 'advertisement'"
      />

      <!-- End of Category Banner Wrapper -->
      <HomeProduct
        :items="widget.items"
        :title="widget.name"
        v-if="widget.type == 'product' && widget.items.length > 0"
      />
      <!-- End of Category Banner Wrapper -->
    </div>

    <div class="mx-auto d-flex justify-center mb-5 mt-5" v-if="isLoading">
      <span class="loader"></span>
    </div>

    <!-- <v-lazy
      :min-height="200"
      :options="{ threshold: 0.5 }"
      transition="fade-transition"
    >
      <div class="brand-container-wrap">
        <h2 class="title title-underline mb-4 ls-normal">
          Danh sách thương hiệu
        </h2>
        <div class="swiper-theme brands-wrapper mb-9">
          <div
            class="row gutter-no cols-xl-6 cols-lg-5 cols-md-4 cols-sm-3 cols-2"
          >
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/1.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/2.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/3.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/4.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/5.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/6.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/7.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/8.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/9.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/10.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
            <div class="brand-col">
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/11.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
              <figure class="brand-wrapper">
                <img
                  src="~/assets/images/demos/demo1/brands/12.png"
                  alt="Brand"
                  width="410"
                  height="186"
                />
              </figure>
            </div>
          </div>
        </div>
      </div>
    </v-lazy> -->
  </div>
</template>



<style scoped>
.loader {
  display: block;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  position: relative;
  animation: rotate 1s linear infinite;
}
.loader::before,
.loader::after {
  content: '';
  box-sizing: border-box;
  position: absolute;
  inset: 0px;
  border-radius: 50%;
  border: 5px solid #fff;
  animation: prixClipFix 2s linear infinite;
}
.loader::after {
  transform: rotate3d(90, 90, 0, 180deg);
  border-color: #ff3d00;
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes prixClipFix {
  0% {
    clip-path: polygon(50% 50%, 0 0, 0 0, 0 0, 0 0, 0 0);
  }
  50% {
    clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 0, 100% 0, 100% 0);
  }
  75%,
  100% {
    clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 100% 100%, 100% 100%);
  }
}
</style>

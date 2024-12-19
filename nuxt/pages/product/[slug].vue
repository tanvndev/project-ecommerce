<script setup>
import 'swiper/css'
import { Autoplay, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { useRouter } from 'vue-router'
import QuantityComponent from '~/components/includes/QuantityComponent.vue'
import ProductRecent from '~/components/product/ProductRecent.vue'

const modules = [Navigation, Autoplay]

const cartStore = useCartStore()
const productStore = useProductStore()
const loadingStore = useLoadingStore()
const wishlistStore = useWishlistStore()
const { $axios } = useNuxtApp()
const route = useRoute()
const router = useRouter()

const visibleRef = ref(false)
const indexRef = ref(0)
const attributeEnables = ref([])
const attributeNotEnables = ref([])
const attributeSelected = ref([])
const product = computed(() => productStore.getProduct)
const variant = ref({})
const prices = ref({})
const images = ref([])
const slider = ref(null)
const quantity = ref(1)

const onSwiper = (swiper) => {
  slider.value = swiper
}

const showImg = (index) => {
  indexRef.value = index
  visibleRef.value = true
}

const getProduct = async () => {
  if (product.value) {
    return updateVariantAndAttributes()
  }

  try {
    loadingStore.setLoading(true)
    const response = await $axios(`/products/${route.params.slug}/detail`)

    if (response.data) {
      productStore.setProduct(response.data)
      productStore.setIsReload(false)
      updateVariantAndAttributes()
    }
  } catch (error) {
  } finally {
    loadingStore.setLoading(false)
  }
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

const updateVariantAndAttributes = () => {
  const variantCurrent = product.value?.variants.find(
    (variant) => variant.slug === removeLastSegment(route.params.slug)
  )
  variant.value = variantCurrent

  attributeEnables.value = product.value?.attribute_enabled
  attributeNotEnables.value = product.value?.attribute_not_enabled

  if (variant.value?.attributes.length > 0) {
    triggerSelectedAttribute(variant.value.attributes)
  }
}

const triggerSelectedAttribute = (attributes) => {
  attributes.forEach((attribute) => {
    attributeSelected.value[attribute.attribute_id] = attribute.id
  })
}

const handleSelectedAttribute = (attributeId, AttributeValueId) => {
  attributeSelected.value[attributeId] = AttributeValueId

  if (isChooseAttribute) {
    const attributeIdCombine = sortAndConcatenate(attributeSelected.value)

    variant.value = product.value?.variants.find(
      (variant) => variant?.attribute_value_combine == attributeIdCombine
    )

    if (variant.value) {
      const newSlug = variant.value?.slug
      const currentPath = router.currentRoute.value.path

      const newUrl = `${currentPath.split('/').slice(0, -1).join('/')}/${newSlug}-${variant.value.product_id}`
      router.replace(newUrl)
    }
  }
}

const isChooseAttribute = () =>
  attributeEnables.value.length === Object.keys(attributeSelected.value).length
const isSelected = (attributeId, AttributeValueId) =>
  attributeSelected.value[attributeId] === AttributeValueId

const handleAlbum = () => {
  if (!variant.value) {
    return
  }
  const album = JSON.parse(variant.value?.album || '[]')

  if (album.length > 0) {
    images.value = album?.map((image) => resizeImage(image))
  }
}

const handleUpdateQuantity = (qty) => (quantity.value = qty)

const addToCart = async () => {
  if (
    !variant.value ||
    !quantity.value ||
    quantity.value < 1 ||
    !isChooseAttribute
  ) {
    return
  }

  const payload = {
    product_variant_id: variant.value.id,
    quantity: quantity.value,
  }
  cartStore.addToCart(payload)
}

watch(
  variant,
  (newVariant) => {
    prices.value = handlePrice(newVariant)
    handleAlbum()
  },
  { immediate: true }
)

watch(
  () => route.params.slug,
  (newSlug) => {
    const lastSegment = newSlug.split('/').pop()
    const productId = getLastPartOfSlug(lastSegment)
    const currentProductId = product.value?.id

    if (currentProductId != productId) {
      productStore.setProduct(null)
      productStore.setProductReviews([])
    }
  },
  { immediate: true }
)

onMounted(() => {
  getProduct()
})

const onHide = () => (visibleRef.value = false)
</script>

<template>
  <!-- Start of Page Content -->
  <div class="page-content mt-5" v-if="product">
    <div class="container">
      <div class="row gutter-lg">
        <div class="main-content">
          <div class="product product-single mb-4">
            <div class="mb-4 position-relative">
              <!-- Start of Product Gallery -->
              <div class="product-single-swiper swiper-theme nav-inner -mx-2">
                <swiper
                  @swiper="onSwiper"
                  :modules="modules"
                  :slides-per-view="2"
                  :loop="true"
                  :navigation="false"
                  :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                  }"
                >
                  <swiper-slide v-for="(image, index) in images" :key="image">
                    <figure
                      class="product-image mx-2"
                      @click="() => showImg(index)"
                    >
                      <img
                        :src="image"
                        class="product-images"
                        :alt="variant?.name"
                      />
                    </figure>
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
                <VueEasyLightbox
                  :visible="visibleRef"
                  :imgs="images"
                  :index="indexRef"
                  @hide="onHide"
                />
                <a
                  href="#"
                  @click="() => showImg(0)"
                  class="product-gallery-btn product-image-full"
                  ><i class="w-icon-zoom"></i
                ></a>
              </div>
              <!-- End of Product Gallery -->
            </div>
            <!-- Start of Product Details -->
            <div class="product-details row pl-0">
              <div class="col-md-6">
                <h1 class="product-title">{{ variant?.name }}</h1>
                <div class="product-bm-wrapper">
                  <div class="product-meta">
                    <div
                      class="product-categories"
                      v-if="product?.catalogues?.length"
                    >
                      Nhóm sản phẩm:
                      <span
                        class="product-category"
                        v-for="catalogue in product?.catalogues"
                        :key="catalogue.id"
                      >
                        <a href="#" style="margin-left: 3px">{{
                          catalogue.name
                        }}</a></span
                      >
                    </div>
                    <div class="product-sku mb-2">
                      SKU: <span>{{ variant?.sku }}</span>
                    </div>
                    <div class="product-sku">
                      Tồn kho:
                      <span
                        >{{ variant?.stock }}
                        <span class="ml-1 w3-tag" v-if="variant?.stock == 0"
                          >Hết hàng</span
                        ></span
                      >
                    </div>
                  </div>
                </div>

                <div class="product-price">
                  <ins class="new-price">{{
                    prices.sale_price || prices.price
                  }}</ins>
                  <del class="old-price" v-if="prices.sale_price">{{
                    prices.price
                  }}</del>
                </div>

                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 80%"></span>
                    <span class="tooltiptext tooltip-top"></span>
                  </div>
                  <a href="#product-tab-reviews" class="rating-reviews"
                    >(3 Reviews)</a
                  >
                </div>

                <div class="product-short-desc">
                  {{ product?.excerpt }}
                </div>
              </div>
              <div class="col-md-6 mt-1">
                <div
                  class="product-form product-variation-form product-size-swatch"
                  v-if="attributeEnables.length > 0"
                  v-for="attribute in attributeEnables"
                >
                  <label class="mb-1">{{ attribute.attribute_name }}:</label>
                  <div
                    class="product-variations flex-wrap d-flex align-items-center"
                  >
                    <span
                      v-for="value in attribute.attribute_value_ids"
                      class="size"
                      :class="{
                        active: isSelected(attribute.attribute_id, value.id),
                      }"
                      @click="
                        handleSelectedAttribute(value.attribute_id, value.id)
                      "
                      >{{ value.name }}</span
                    >
                  </div>
                </div>
                <hr class="product-divider mb-5" />
                <div class="fix-bottom product-sticky-content sticky-content">
                  <div class="product-form container">
                    <QuantityComponent
                      @update:quantity="handleUpdateQuantity"
                      :max="variant?.stock - 2"
                    />
                    <button
                      class="btn btn-primary btn-cart"
                      :class="{
                        disabled: !isChooseAttribute || variant?.stock == 0,
                      }"
                      @click="addToCart"
                    >
                      <i class="w-icon-cart"></i>
                      <span v-if="variant?.stock > 0">Thêm vào giỏ hàng</span>
                      <span v-else>Tạm hết hàng</span>
                    </button>
                  </div>
                </div>

                <div class="social-links-wrapper">
                  <div class="social-links">
                    <div
                      class="social-icons social-no-color border-thin"
                      style="border-color: transparent !important"
                    >
                      <a
                        href="https://www.facebook.com/"
                        class="social-icon social-facebook w-icon-facebook"
                        data-platform="facebook"
                        @click.prevent="handleSocialIconClick"
                      ></a>
                      <a
                        href="https://twitter.com/"
                        class="social-icon social-twitter w-icon-twitter"
                        data-platform="twitter"
                        @click.prevent="handleSocialIconClick"
                      ></a>
                      <a
                        href="https://www.pinterest.com/"
                        class="social-icon social-pinterest fab fa-pinterest-p"
                        data-platform="pinterest"
                        @click.prevent="handleSocialIconClick"
                      ></a>
                      <a
                        href="https://www.whatsapp.com/"
                        class="social-icon social-whatsapp fab fa-whatsapp"
                        data-platform="whatsapp"
                        @click.prevent="handleSocialIconClick"
                      ></a>
                      <a
                        href="https://www.linkedin.com/"
                        class="social-icon social-youtube fab fa-linkedin-in"
                        data-platform="linkedin"
                        @click.prevent="handleSocialIconClick"
                      ></a>
                    </div>
                  </div>
                  <span class="divider d-xs-show"></span>
                  <div class="product-link-wrapper d-flex">
                    <a
                      @click.prevent="addToWishlist"
                      href="#"
                      class="btn-product-icon btn-wishlist w-icon-heart"
                      ><span></span
                    ></a>
                  </div>
                </div>
              </div>
            </div>

            <!-- End of Product Details -->
          </div>
        </div>
        <!-- End of Main Content -->

        <!-- Start of Sidebar -->
        <ProductAside :product="product" />
        <!-- End of Sidebar -->
      </div>
      <!-- Product Suggestions -->
      <ProductSuggestion :variant="variant" />

      <!-- Start of MoreInfomation -->
      <ProductMoreInfomation :product="product" />
      <!-- End of MoreInfomation -->

      <!-- Product recommendations -->
      <ProductRecommendation :product="product" />
      <!-- End of Product recommendations -->
      <!-- Start of MoreInfomation -->
      <!-- <ProductRecent :product="product" /> -->
      <!-- End of MoreInfomation -->
    </div>
  </div>
  <!-- End of Page Content -->
</template>

<style scoped>
.size {
  margin-right: 6px;
  cursor: pointer;
  user-select: none;
}
.product-image {
  background-color: #f5f6f7;
}
.product-images {
  background-color: #f5f6f7;
  width: 100%;
  height: 512px;
  border-radius: 4px;
  object-fit: contain;
  mix-blend-mode: darken;
}
</style>

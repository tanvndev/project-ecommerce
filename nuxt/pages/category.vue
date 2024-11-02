<script setup>
import _ from 'lodash'
import 'swiper/css'
import { Autoplay, Navigation } from 'swiper/modules'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { STARS } from '~/static/rating'

const modules = [Navigation, Autoplay]
const { $axios } = useNuxtApp()
const productCatalogueStore = useProductCatalogueStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const productCatalogues = computed(
  () => productCatalogueStore.getProductCatalogues
)
const route = useRoute()
const queryParams = computed(() => route.query)
const slider = ref(null)
const is_collapsed = ref({})
const isLoading = ref(false)
const products = ref([])
const attributes = ref([])
const priceRange = reactive({
  min: null,
  max: null,
})
const searchTerm = ref('')
const selectedValues = ref([])
const selectedStars = ref([])
const router = useRouter()
const totalPage = ref(10)
const currentPage = ref(Number(queryParams.value.page) || 1)
const sortBy = ref(queryParams.value.sort || 'asc')

const filteredProducts = computed(() => {
  if (!searchTerm.value) products.value
  return products.value.filter((product) =>
    product.name.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

const formatNumber = (type, event) => {
  if (type == 'min') {
    priceRange.min = numberWithCommas(event.target.value)
  } else {
    priceRange.max = numberWithCommas(event.target.value)
  }
}
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

const toggleCollapse = (id) => {
  if (is_collapsed.value[id] === undefined) {
    is_collapsed.value[id] = false
  }
  is_collapsed.value[id] = !is_collapsed.value[id]
}

const handleFilterWithPrice = () => {
  const query = { ...route.query }

  if (priceRange.min !== null) {
    query.min_price = removeDots(priceRange.min)
  }

  if (priceRange.max !== null) {
    query.max_price = removeDots(priceRange.max)
  }

  if (priceRange.min === null) delete query.min_price
  if (priceRange.max === null) delete query.max_price

  router.push({ query })
}

const removeDots = (value) => value.replace(/\./g, '')

const toggleAttributeValue = (valueId) => {
  const index = selectedValues.value.indexOf(valueId)
  if (index === -1) {
    selectedValues.value.push(valueId)
  } else {
    selectedValues.value.splice(index, 1)
  }
  console.log(selectedValues.value)
}
const updateAttributeQuery = () => {
  const values = encodeURIComponent(selectedValues.value.join(',') || '')
  const query = { ...route.query, values }

  router.push({ query })
}

const toggleRatingValue = (valueId) => {
  const index = selectedStars.value.indexOf(valueId)
  if (index === -1) {
    selectedStars.value.push(valueId)
  } else {
    selectedStars.value.splice(index, 1)
  }
  console.log(selectedStars.value)
}
const updateRatingQuery = () => {
  const stars = encodeURIComponent(selectedStars.value.join(',') || '')
  const query = { ...route.query, stars }

  router.push({ query })
}

const getProducts = async () => {
  const query = { ...route.query, page: currentPage.value, sort: sortBy.value }
  router.push({ query })

  try {
    isLoading.value = true
    const response = await $axios.get(`/products/filter`, {
      params: { ...queryParams.value },
    })
    products.value = response?.data?.product_variants?.data
    attributes.value = response?.data?.attributes
    totalPage.value = response?.data?.product_variants?.last_page
  } catch (error) {
    console.log(error)
  } finally {
    isLoading.value = false
  }
}

const debounceGetProducts = debounce(getProducts, 400)

watch(selectedValues, updateAttributeQuery, { deep: true })
watch(selectedStars, updateRatingQuery, { deep: true })
watch(
  [queryParams, currentPage, sortBy],

  () => {
    debounceGetProducts()
  },
  { deep: true, immediate: true }
)

onMounted(() => {
  const { stars, values } = queryParams.value

  if (stars) {
    const decodeStar = decodeURIComponent(stars)
    const starArrs = decodeStar.split(',').map(Number)
    selectedStars.value = starArrs
  }
  if (values) {
    const decodeValues = decodeURIComponent(values)
    const valuesArrs = decodeValues.split(',').map(Number)

    selectedValues.value = valuesArrs
  }
})
</script>
<template>
  <div class="page-content mt-7">
    <div class="container">
      <!-- Start of Shop Category -->
      <div class="shop-default-category category-ellipse-section mb-6">
        <div class="swiper-theme shadow-swiper">
          <div
            class="row gutter-lg cols-xl-6 cols-lg-6 cols-md-6 cols-sm-4 cols-xs-3 cols-2"
          >
            <div
              class="category-wrap"
              v-for="item in productCatalogues"
              :key="item.id"
            >
              <div class="category category-ellipse">
                <figure class="category-media">
                  <NuxtLink
                    :title="item.name"
                    :to="`/category?catalogues=${item.id}`"
                  >
                    <img
                      :src="resizeImage(item.image, 300)"
                      :alt="item.name"
                      width="190"
                      height="190"
                      class="category-image"
                    />
                  </NuxtLink>
                </figure>
                <div class="category-content">
                  <h4 class="category-name">
                    <NuxtLink
                      :title="item.name"
                      :to="`/category?catalogues=${item.id}`"
                      >{{ item.name }}</NuxtLink
                    >
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End of Shop Category -->

      <!-- Start of Shop Content -->
      <div class="shop-content row gutter-lg mb-10 mt-5">
        <!-- Start of Sidebar, Shop Sidebar -->
        <aside
          class="sidebar shop-sidebar sticky-sidebar-wrapper sidebar-fixed"
        >
          <!-- Start of Sidebar Overlay -->
          <div class="sidebar-overlay"></div>
          <a class="sidebar-close" href="#"><i class="close-icon"></i></a>

          <!-- Start of Sidebar Content -->
          <div class="sidebar-content scrollable">
            <!-- Start of Sticky Sidebar -->
            <div class="sticky-sidebar">
              <div class="filter-actions">
                <label>
                  <i class="fal fa-filter fs-14 mr-1"></i>
                  BỘ LỌC TÌM KIẾM</label
                >
                <a
                  href="clean-all"
                  @click.prevent="$router.push('/category')"
                  v-if="!_.isEmpty(queryParams)"
                >
                  Xóa tất cả
                </a>
              </div>
              <!-- Start of Collapsible widget -->
              <div class="widget widget-collapsible">
                <h3
                  class="widget-title"
                  :class="is_collapsed['catalogue'] ? 'collapsed' : ''"
                  @click="toggleCollapse('catalogue')"
                >
                  <label>Danh mục</label>
                  <span class="toggle-btn"></span>
                </h3>
                <ul class="widget-body filter-items item-check mt-1">
                  <li
                    v-for="item in productCatalogues"
                    :key="item.id"
                    class="active"
                  >
                    <NuxtLink
                      :title="item.name"
                      :to="`/category?catalogues=${item.id}`"
                      >{{ item.name }}</NuxtLink
                    >
                  </li>
                </ul>
              </div>
              <!-- End of Collapsible Widget -->

              <!-- Start of Collapsible Widget -->
              <div
                class="widget widget-collapsible"
                v-for="attribute in attributes"
                :key="`attribute-${attribute.id}`"
              >
                <h3
                  class="widget-title"
                  :class="
                    is_collapsed[`attribute-${attribute.id}`] ? 'collapsed' : ''
                  "
                  @click="toggleCollapse(`attribute-${attribute.id}`)"
                >
                  <label>{{ attribute.name }}</label>
                  <span class="toggle-btn"></span>
                </h3>
                <ul class="widget-body filter-items item-check mt-1">
                  <li
                    v-for="value in attribute.values"
                    :key="`values-${value.id}`"
                    :class="{ active: selectedValues.includes(value.id) }"
                  >
                    <input
                      type="checkbox"
                      class="d-none"
                      :value="value.id"
                      :checked="selectedValues.includes(value.id)"
                    />
                    <a
                      href="#"
                      class="label-text"
                      @click.prevent="toggleAttributeValue(value.id)"
                      >{{ value.name }}</a
                    >
                  </li>
                </ul>
              </div>
              <!-- End of Collapsible Widget -->

              <!-- Start of Collapsible Widget -->
              <div class="widget widget-collapsible">
                <h3
                  class="widget-title"
                  :class="is_collapsed[`rating`] ? 'collapsed' : ''"
                  @click="toggleCollapse(`rating`)"
                >
                  <label>Đánh giá</label>
                  <span class="toggle-btn"></span>
                </h3>
                <ul class="widget-body filter-items item-check mt-1">
                  <li
                    v-for="item in STARS"
                    :key="item.star"
                    :class="{ active: selectedStars.includes(item.star) }"
                  >
                    <input
                      type="checkbox"
                      class="d-none"
                      :value="item.star"
                      :checked="selectedStars.includes(item.star)"
                    />
                    <a
                      href="#"
                      class="label-text"
                      @click.prevent="toggleRatingValue(item.star)"
                    >
                      <div class="ratings-full d-inline-block">
                        <span
                          class="ratings"
                          :style="`width: ${item.percent}`"
                        ></span>
                        <span class="tooltiptext tooltip-top">{{
                          item.star
                        }}</span>
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- End of Collapsible Widget -->

              <!-- Start of Collapsible Widget -->
              <div class="widget widget-collapsible">
                <h3
                  class="widget-title"
                  :class="is_collapsed['price'] ? 'collapsed' : ''"
                  @click="toggleCollapse('price')"
                >
                  <label>Khoảng Giá</label>
                  <span class="toggle-btn"></span>
                </h3>
                <div class="widget-body">
                  <div class="price-range">
                    <input
                      type="text"
                      class="min_price text-center"
                      :value="priceRange.min"
                      @input="formatNumber('min', $event)"
                      placeholder="Tối thiểu ₫"
                    />
                    <span class="delimiter">
                      <i class="fal fa-minus fs-13"></i>
                    </span>
                    <input
                      type="text"
                      class="max_price text-center"
                      :value="priceRange.max"
                      @input="formatNumber('max', $event)"
                      placeholder="Tối đa ₫"
                    />
                  </div>
                  <v-btn
                    html-type="button"
                    @click="handleFilterWithPrice"
                    width="100%"
                    color="#336699"
                    >Xác nhận</v-btn
                  >
                </div>
              </div>
              <!-- End of Collapsible Widget -->
            </div>
            <!-- End of Sidebar Content -->
          </div>
          <!-- End of Sidebar Content -->
        </aside>
        <!-- End of Shop Sidebar -->

        <!-- Start of Shop Main Content -->
        <div class="main-content">
          <nav class="toolbox sticky-toolbox sticky-content fix-top">
            <div class="toolbox-left justify-between w-100">
              <div>
                <div style="width: 300px; font-size: 12px">
                  <v-text-field
                    style="font-size: 12px"
                    v-model="searchTerm"
                    prepend-inner-icon="mdi-magnify"
                    variant="underlined"
                    clearable
                    density="comfortable"
                    placeholder="Bạn có thể tìm kiếm nhanh ở đây"
                  ></v-text-field>
                </div>
                <a
                  href="#"
                  class="btn btn-primary btn-outline btn-rounded left-sidebar-toggle btn-icon-left d-block d-lg-none"
                  ><i class="w-icon-category"></i><span>Lọc sản phẩm</span></a
                >
              </div>
              <div class="d-flex align-items-center">
                <label>Sắp xếp theo </label>
                <v-select
                  :items="[
                    {
                      title: 'Giá từ thấp đến cao',
                      value: 'asc',
                    },
                    {
                      title: 'Giá từ cao đến thấp',
                      value: 'desc',
                    },
                  ]"
                  v-model="sortBy"
                  density="compact"
                  variant="outlined"
                  placeholder="Sắp xếp theo"
                  hide-details="true"
                  style="width: 200px"
                ></v-select>
              </div>
            </div>
          </nav>
          <div
            class="product-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2 border-b pb-5"
            v-if="!isLoading && filteredProducts?.length"
          >
            <!--  -->
            <div
              class="product-wrap"
              v-for="item in filteredProducts"
              :key="item.id"
            >
              <div class="product text-center">
                <figure class="product-media">
                  <NuxtLink
                    :title="item?.name"
                    :to="`product/${item.slug}-${item.product_id}`"
                  >
                    <img
                      :src="resizeImage(item.image, 500)"
                      alt="Product"
                      width="300"
                      height="338"
                    />
                  </NuxtLink>
                  <div class="product-action-horizontal">
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
                  <h3 class="product-name">
                    <NuxtLink
                      :title="item?.name"
                      :to="`product/${item.slug}-${item.product_id}`"
                    >
                      {{ item.name }}
                    </NuxtLink>
                  </h3>
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
            <!--  -->
          </div>

          <v-row v-if="isLoading">
            <v-col
              v-for="item in filteredProducts"
              :key="item.id"
              cols="12"
              sm="6"
              md="4"
              lg="3"
            >
              <v-skeleton-loader type="card"></v-skeleton-loader>
            </v-col>
          </v-row>

          <div v-if="!filteredProducts?.length">
            <v-empty-state
              icon="mdi-magnify"
              text="Sản phẩm đang trống vui lòng chọn quay lại mua những sản phẩm mới nhất của chúng tôi."
              title="Sản phẩm không có sẵn."
            ></v-empty-state>
          </div>

          <v-row justify="center">
            <v-col cols="8" class="p-0">
              <v-pagination
                v-model="currentPage"
                :length="totalPage"
                class="my-2"
              ></v-pagination>
            </v-col>
          </v-row>
        </div>
        <!-- End of Shop Main Content -->
      </div>
      <!-- End of Shop Content -->
    </div>
  </div>
</template>
<style scoped>
.category-media {
  background-color: #fff;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}
.category-media .category-image {
  padding: 15px;
}

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
  height: 230px;
  border-radius: 10px !important;
  mix-blend-mode: darken;
  transition: all 0.2s ease-in-out;
}

.product-media:hover img {
  transform: translateY(-12px);
}
</style>

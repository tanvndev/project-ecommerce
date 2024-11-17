<script setup>
import _ from 'lodash'
import { ref } from 'vue'

const { $axios } = useNuxtApp()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const productStore = useProductStore()
const isLoading = ref(false)
const products = ref([])
const searchTerm = ref('')

const imageSearch = computed(() => productStore.getImageSearch)
const filteredProducts = computed(() => {
  if (!searchTerm.value) products.value
  return products.value.filter((product) =>
    product.name.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

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

const getProducts = async () => {
  if (!imageSearch.value) {
    return toast('Vui lòng nhập hình ảnh để tìm kiếm.', 'error')
  }
  try {
    isLoading.value = true

    let formData = new FormData()
    formData = appendBase64ToFormData(
      formData,
      imageSearch.value,
      'image-search.png'
    )

    const response = await $axios.post(`/products/search/image`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    console.log(response)

    products.value = response?.data
  } catch (error) {
    toast('Có lỗi vui lòng thử lại.', 'error')
  } finally {
    isLoading.value = false
  }
}

watch(
  imageSearch,
  (newVal) => {
    getProducts()
  },
  { immediate: true }
)
</script>
<template>
  <div class="page-content mt-7">
    <div class="container">
      <!-- Start of Shop Content -->
      <div class="shop-content">
        <div class="image-search-wrap">
          <div class="image-search">
            <img :src="imageSearch" alt="image-search" />
          </div>
          <div class="triangle"></div>
        </div>
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
              </div>
            </div>
          </nav>
          <div
            class="product-wrapper row cols-lg-5 cols-md-4 cols-sm-3 cols-2"
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
                    :to="`/product/${item.slug}-${item.product_id}`"
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
                      :to="`/product/${item.slug}-${item.product_id}`"
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
                      :to="`/product/${item.slug}-${item.product_id}`"
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
              lg="2"
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
        </div>
        <!-- End of Shop Main Content -->
      </div>
      <!-- End of Shop Content -->
    </div>
  </div>
</template>
<style scoped>
.image-search {
  align-items: center;
  background-color: #fff;
  cursor: pointer;
  display: flex;
  height: 80px;
  justify-content: center;
  margin-right: 16px;
  min-height: 80px;
  min-width: 80px;
  position: relative;
  width: 80px;
  border-radius: 12px;
  padding: 5px;
  margin: auto;
}
.triangle::before {
  content: '';
  position: absolute;
  top: 88px;
  left: 38px;
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 9px solid #336699;
  border-radius: 4px;
}
.image-search-wrap {
  position: relative;
  height: 90px;
  min-height: 90px;
  min-width: 90px;
  width: 90px;
  margin: auto;
  border-radius: 12px;
  border: 2px solid #336699;
  display: grid;
  place-items: center;
}
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

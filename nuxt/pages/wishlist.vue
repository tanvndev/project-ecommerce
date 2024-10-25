<script setup>
const loadingStore = useLoadingStore()
const wishlistStore = useWishlistStore()
const { $axios } = useNuxtApp()
const wishlists = computed(() => wishlistStore.getWishlists)
const page = ref(1)

const handleRemove = async (id) => {
  await wishlistStore.removeWishlist(id)
}
const debounceHandleChangePage = debounce(async () => {
  await wishlistStore.getAllWishlists(page.value)
}, 300)

const addWishlistToCart = async (product_variant_id) => {
  await wishlistStore.addWishlistToCart(product_variant_id)
}

watch(page, () => {
  debounceHandleChangePage()
})

onMounted(async () => {
  await wishlistStore.getAllWishlists(page.value)
})
</script>

<template>
  <!-- Start of PageContent -->
  <main class="main wishlist-page">
    <div class="page-content">
      <div class="container">
        <h3 class="wishlist-title mt-7">Sản phẩm ưa thích</h3>
        <table
          class="shop-table wishlist-table mb-5"
          v-if="wishlists?.items?.length > 0"
        >
          <thead>
            <tr>
              <th class="product-name"><span>Sản phẩm</span></th>
              <th></th>
              <th class="text-right">
                <span>Giá cả</span>
              </th>
              <th class="text-right">
                <span>Tồn kho</span>
              </th>
              <th class="text-right">Thực thi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="wishlist in wishlists?.items" :key="wishlist">
              <td class="product-thumbnail">
                <div class="p-relative">
                  <NuxtLink
                    :to="`product/${wishlist.slug}-${wishlist.product_id}`"
                  >
                    <figure>
                      <img
                        :src="wishlist.image"
                        :alt="wishlist.name"
                        width="300"
                        height="338"
                      />
                    </figure>
                  </NuxtLink>
                  <button
                    type="button"
                    @click="handleRemove(wishlist.id)"
                    class="btn btn-close"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </td>
              <td class="product-name">
                <NuxtLink
                  :to="`product/${wishlist.slug}-${wishlist.product_id}`"
                >
                  {{ wishlist.name }}
                </NuxtLink>
                <span class="d-block mt-1 fs-13" style="color: #336699">
                  Phân loại:
                  {{ wishlist.attributes }}
                </span>
              </td>
              <td class="text-right">
                <div class="product-price w-100">
                  <ins class="new-price">{{
                    formatCurrency(wishlist.sale_price || wishlist.price)
                  }}</ins>
                  <del class="old-price" v-if="wishlist.sale_price">{{
                    formatCurrency(wishlist.price)
                  }}</del>
                </div>
              </td>
              <td class="text-right">
                <span class="wishlist-in-stock">{{ wishlist.stock }}</span>
              </td>
              <td class="wishlist-action">
                <div class="d-lg-flex justify-end">
                  <a
                    @click.prevent="
                      addWishlistToCart(wishlist.product_variant_id)
                    "
                    href="#"
                    class="btn btn-dark btn-rounded btn-sm ml-lg-2 btn-cart"
                    >Thêm vào giỏ hàng</a
                  >
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="my-4" v-if="page > 1">
          <v-pagination
            v-model="page"
            :length="wishlists?.last_page"
            rounded="circle"
          ></v-pagination>
        </div>
      </div>
    </div>
  </main>
  <!-- End of PageContent -->
</template>
<style scoped>
.shop-table tbody {
  border-bottom: none;
}
</style>

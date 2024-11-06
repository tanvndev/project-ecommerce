<script setup>
const cartStore = useCartStore()
const carts = computed(() => cartStore.getCartSelected)

</script>
<template>
  <div class="mt-7">
    <h3 class="title text-uppercase ls-10 mb-3">
      Sản phẩm ({{ carts?.length }})
    </h3>
    <v-table>
      <thead>
        <tr>
          <th class="text-left fs-17">Sản phẩm</th>
          <th class="text-right fs-17">Đơn giá</th>
          <th class="text-right fs-17">Số lượng</th>
          <th class="text-right fs-17">Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in carts" :key="item.attributes">
          <td class="py-3">
            <div class="d-flex items-center">
              <div class="product-image">
                <img loading="lazy" :src="item.image" :alt="item.name" />
              </div>
              <div class="ml-2">
                <NuxtLink
                  :to="`product/${item.slug}-${item.product_id}`"
                  class="mb-0 product-title"
                >
                  {{ item.name }}
                </NuxtLink>
                <span class="product-attribute">{{ item.attributes }}</span>
              </div>
            </div>
          </td>
          <td class="text-right">
            <div class="product-price">
              <ins class="new-price">{{
                formatCurrency(item.sale_price || item.price)
              }}</ins>
              <del class="old-price" v-if="item.sale_price">{{
                formatCurrency(item.price)
              }}</del>
            </div>
          </td>
          <td class="text-right">{{ item.quantity }}</td>
          <td class="text-right">{{ formatCurrency(item.sub_total) }}</td>
        </tr>
      </tbody>
    </v-table>
  </div>
</template>

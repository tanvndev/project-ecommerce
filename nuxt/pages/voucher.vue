<script setup>
useSeoMeta({
  title: 'Mã giảm giá',
  ogTitle: 'Mã giảm giá',
  description: 'Mã giảm giá.',
  ogDescription: 'Mã giảm giá.',
})
import { useLoadingStore } from '#imports'
import { formatCurrency, toast } from '#imports'

const { $axios } = useNuxtApp()
const loadingStore = useLoadingStore()
const vouchers = ref([])
const nextPage = ref(null)

const getAllVouchers = async (page) => {
  try {
    if (page == 1) {
      loadingStore.setLoading(true)
    }

    const res = await $axios.get(
      '/vouchers/all' + (page ? '?page=' + page : '')
    )

    vouchers.value = [...vouchers.value, ...res?.data.data]
    nextPage.value = res?.data.next_page

    return res?.data.data.length > 0
  } catch (e) {
    console.error('Error fetching vouchers:', e)
    return false
  } finally {
    loadingStore.setLoading(false)
  }
}

const handleCopyCode = (code) => {
  navigator.clipboard
    .writeText(code)
    .then(() => {
      toast('Đã sao chép mã thành công.')
    })
    .catch((err) => {
      console.error('Không thể sao chép mã code: ', err)
      toast('Không thể sao chép mã code. Vui lòng thử lại.', 'error')
    })
}

const load = async ({ done }) => {
  const hasMore = await getAllVouchers(nextPage.value)
  if (hasMore) {
    done('ok')
  } else {
    done('empty')
  }
}
</script>
<template>
  <section class="coupon-area">
    <v-infinite-scroll :onLoad="load">
      <div class="container">
        <div class="row">
          <template v-for="(voucher, index) in vouchers" :key="voucher.id">
            <div class="col-lg-6">
              <div class="coupon-area-wrap">
                <div class="coupon">
                  <div class="coupon-left">
                    <figure class="thumb">
                      <img
                        :src="resizeImage(voucher.image, 200)"
                        :alt="voucher.name"
                      />
                    </figure>
                    <div class="content">
                      <h4 class="title-coupon">
                        {{ voucher.name }}
                      </h4>

                      <p class="value" v-if="voucher.value_type == 'fixed'">
                        - {{ formatCurrency(voucher.value) }}
                      </p>
                      <p class="value" v-else>
                        - {{ parseFloat(+voucher.value).toFixed(0) }}%
                      </p>

                      <div class="expired">
                        Thời hạn: <span> {{ voucher.expired }} </span>
                      </div>
                    </div>
                  </div>
                  <div class="coupon-right">
                    <div class="status">
                      Ưu đãi
                      <span :class="voucher?.status?.color">{{
                        voucher?.status?.text
                      }}</span>
                    </div>

                    <button
                      type="button"
                      @click="handleCopyCode(voucher.code)"
                      class="code code-coupon w-100"
                    >
                      Sao chép mã
                    </button>
                    <p class="condition">* {{ voucher.text_description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
      <template v-slot:empty>
        <div>Không có dữ liệu!</div>
      </template>
    </v-infinite-scroll>
  </section>
</template>

<style scoped></style>

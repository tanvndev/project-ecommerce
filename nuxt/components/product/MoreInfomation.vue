<script setup>
import _ from 'lodash'
const productStore = useProductStore()
const { $axios } = useNuxtApp()

const reviews = computed(() => productStore.getProductReviews)
const tabs = reactive([
  { name: 'description', label: 'Mô tả' },
  { name: 'specifications', label: 'Thông số kĩ thuật' },
  { name: 'reviews', label: 'Đánh giá' },
])
const isReload = computed(() => productStore.getIsReload)
const product = computed(() => productStore.getProduct)
const activeTab = ref('description')
const isCollapsed = ref(true)

const selectTab = (tabName) => {
  activeTab.value = tabName
}

const getAllReviews = async () => {
  const response = await $axios.get(`/product-reviews/${product.value?.id}`)
  productStore.setProductReviews(response.data)
}

onMounted(() => {
  if (isReload.value || reviews.value?.length == 0) {
    getAllReviews()
  }
})
</script>

<template>
  <div class="tab tab-nav-boxed tab-nav-underline product-tabs">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" v-for="tab in tabs" :key="tab.name">
        <a
          href="#"
          class="nav-link"
          :class="{ active: tab.name === activeTab }"
          @click.prevent="selectTab(tab.name)"
        >
          {{ tab.label }}
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div
        id="product-tab-description"
        class="tab-pane"
        :class="{ active: activeTab == 'description' }"
        v-show="activeTab == 'description'"
      >
        <div
          :class="{collapsed: isCollapsed}"
          v-html="product.description || 'Đang cập nhập mô tả...'"
        ></div>
        <div class="text-center mt-2">
          <v-btn @click="isCollapsed = !isCollapsed" variant="text">
            <span class="mr-1">{{ isCollapsed ? 'Xem thêm' : 'Thu gọn' }}</span>
            <i class="fas fa-chevron-down fs-13" v-if="isCollapsed"></i>
            <i class="fas fa-chevron-up fs-13" v-else></i>
          </v-btn>
        </div>
      </div>
      <div
        id="product-tab-specifications"
        class="tab-pane"
        :class="{ active: activeTab == 'specifications' }"
        v-show="activeTab == 'specifications'"
      >
        <div
          class="specifications-list"
          v-if="product?.attribute_not_enabled?.length > 0"
        >
          <div
            class="specification"
            v-for="(attribute, index) in product?.attribute_not_enabled"
            :key="index"
            :class="{ odd: index % 2 !== 0, even: index % 2 === 0 }"
          >
            <strong>{{ attribute.attribute_name }}:</strong>

            <span
              v-for="(value, index) in attribute.attribute_value_ids"
              :key="value.id"
            >
              {{ value.name }}
              <span v-if="index < attribute.attribute_value_ids.length - 1"
                >,
              </span>
            </span>
          </div>
        </div>
      </div>
      <div
        id="product-tab-reviews"
        class="tab-pane"
        :class="{ active: activeTab == 'reviews' }"
        v-show="activeTab == 'reviews'"
      >
        <div class="row mb-3">
          <div class="col-xl-12 col-lg-5">
            <div class="ratings-wrapper d-flex justify-between">
              <div class="avg-rating-container mr-10">
                <h4 class="avg-mark font-weight-bolder ls-50">
                  {{ reviews?.avg_rating }}
                </h4>
                <div class="avg-rating">
                  <p class="text-dark mb-1">Đánh giá trung bình</p>
                  <div class="ratings-container">
                    <div class="ratings-full">
                      <span
                        class="ratings"
                        :style="`width: ${reviews?.avg_rating_percent}%`"
                      ></span>
                      <span class="tooltiptext tooltip-top"></span>
                    </div>
                    <a href="#" class="rating-reviews"
                      >({{ reviews?.items?.length }} Đánh giá)</a
                    >
                  </div>
                </div>
              </div>

              <div class="ratings-list w-100">
                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 100%"></span>
                    <span class="tooltiptext tooltip-top">5</span>
                  </div>
                  <div class="progress-bar progress-bar-sm">
                    <span :style="`width: ${reviews?._5_star}%`"></span>
                  </div>
                  <div class="progress-value">
                    <mark>{{ reviews?._5_star }}%</mark>
                  </div>
                </div>
                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 80%"></span>
                    <span class="tooltiptext tooltip-top">4</span>
                  </div>
                  <div class="progress-bar progress-bar-sm">
                    <span :style="`width: ${reviews?._4_star}%`"></span>
                  </div>
                  <div class="progress-value">
                    <mark>{{ reviews?._4_star }}%</mark>
                  </div>
                </div>
                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 60%"></span>
                    <span class="tooltiptext tooltip-top">3</span>
                  </div>
                  <div class="progress-bar progress-bar-sm">
                    <span :style="`width: ${reviews?._3_star}%`"></span>
                  </div>
                  <div class="progress-value">
                    <mark>{{ reviews?._3_star }}%</mark>
                  </div>
                </div>
                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 40%"></span>
                    <span class="tooltiptext tooltip-top">2</span>
                  </div>
                  <div class="progress-bar progress-bar-sm">
                    <span :style="`width: ${reviews?._2_star}%`"></span>
                  </div>
                  <div class="progress-value">
                    <mark>{{ reviews?._2_star }}%</mark>
                  </div>
                </div>
                <div class="ratings-container">
                  <div class="ratings-full">
                    <span class="ratings" style="width: 20%"></span>
                    <span class="tooltiptext tooltip-top">1</span>
                  </div>
                  <div class="progress-bar progress-bar-sm">
                    <span :style="`width: ${reviews?._1_star}%`"></span>
                  </div>
                  <div class="progress-value">
                    <mark>{{ reviews?._1_star }}%</mark>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div
          class="tab tab-nav-boxed tab-nav-outline tab-nav-center"
          v-if="reviews?.items?.length > 0"
        >
          <div class="tab-content">
            <div class="tab-pane active" id="show-all">
              <ul class="comments list-style-none">
                <li
                  class="comment"
                  v-for="review in reviews?.items"
                  :key="review"
                >
                  <div class="comment-body">
                    <figure class="comment-avatar">
                      <v-img
                        :src="resizeImage(review.image, '200')"
                        :alt="review.fullname"
                        width="90"
                        height="90"
                      />
                    </figure>
                    <div class="comment-content w-100">
                      <div class="d-flex align-items-center justify-between">
                        <h4 class="comment-author">
                          <span>{{ review.fullname }}</span>
                          <span class="comment-date ms-3">{{
                            review.created_at
                          }}</span>
                        </h4>
                        <div class="comment-rating-check">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                          >
                            <path
                              d="M21.856 10.303c.086.554.144 1.118.144 1.697 0 6.075-4.925 11-11 11s-11-4.925-11-11 4.925-11 11-11c2.347 0 4.518.741 6.304 1.993l-1.422 1.457c-1.408-.913-3.082-1.45-4.882-1.45-4.962 0-9 4.038-9 9s4.038 9 9 9c4.894 0 8.879-3.928 8.99-8.795l1.866-1.902zm-.952-8.136l-9.404 9.639-3.843-3.614-3.095 3.098 6.938 6.71 12.5-12.737-3.096-3.096z"
                            />
                          </svg>
                          <span>Đã mua hàng</span>
                        </div>
                      </div>
                      <div class="ratings-container comment-rating">
                        <div class="ratings-full">
                          <span
                            class="ratings"
                            :style="`width: ${review.percent_rating}%`"
                          ></span>
                          <span class="tooltiptext tooltip-top">{{
                            review.rating
                          }}</span>
                        </div>
                      </div>
                      <p>
                        {{ review.comment }}
                      </p>

                      <div class="d-flex pb-6">
                        <v-img
                          v-for="img in JSON.parse(review?.images || '[]')"
                          :src="resizeImage(img, 150)"
                          class="bg-grey-lighten-2 mr-3"
                          max-width="150"
                          max-height="100"
                          rounded
                          cover
                        >
                        </v-img>
                      </div>
                    </div>
                  </div>

                  <ul
                    class="comments list-style-none children"
                    v-if="!_.isEmpty(review?.replies)"
                  >
                    <li class="comment">
                      <div class="comment-body">
                        <figure class="comment-avatar">
                          <v-img
                            :src="review?.replies?.image"
                            :alt="review?.replies?.fullname"
                            width="90"
                            height="90"
                          />
                        </figure>
                        <div class="comment-content">
                          <h4 class="comment-author">
                            <span>{{ review?.replies?.fullname }}</span>
                            <span class="comment-date ms-3">{{
                              review?.replies?.created_at
                            }}</span>
                          </h4>

                          <div v-html="review?.replies?.comment"></div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.specifications-list {
  font-family: Arial, sans-serif;
  line-height: 1.6;
}

.specification {
  padding: 8px 16px;
}

.specification.odd {
  background-color: #ffffff;
}

.specification.even {
  background-color: #f5f6f7;
}

.specification strong {
  color: #333;
  min-width: 300px;
  display: inline-block;
}

.progress-bar span {
  background-color: #f77c29;
}
.collapsed {
  overflow: hidden;
  height: 1000px;
}
</style>

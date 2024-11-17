import { useLoadingStore } from '#imports'
import { defineStore } from 'pinia'

export const useProductStore = defineStore('product', {
  state: () => {
    return {
      product: null,
      isReload: true,
      productReviews: [],
      productRecommendations: [],
      imageSearch: '',
    }
  },
  getters: {
    getProduct: (state) => state.product,
    getIsReload: (state) => state.isReload,
    getProductReviews: (state) => state.productReviews,
    getProductRecommendations: (state) => state.productRecommendations,
    getImageSearch: (state) => state.imageSearch,
  },
  actions: {
    setIsReload(value) {
      this.isReload = value
    },
    setProduct(product) {
      this.product = product
    },
    setProductRecommendations(productRecommendations) {
      this.productRecommendations = productRecommendations
    },
    setProductReviews(productReviews) {
      this.productReviews = productReviews
    },
    setImageSearch(imageSearch) {
      this.imageSearch = imageSearch
    },
  },
})

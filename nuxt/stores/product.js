import { defineStore } from 'pinia'
import { useLoadingStore } from '#imports'

export const useProductStore = defineStore('product', {
  state: () => {
    return {
      product: null,
      isReload: true,
      productReviews: [],
      productRecommendations: [],
    }
  },
  getters: {
    getProduct: (state) => state.product,
    getIsReload: (state) => state.isReload,
    getProductReviews: (state) => state.productReviews,
    getProductRecommendations: (state) => state.productRecommendations,
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
  },
})

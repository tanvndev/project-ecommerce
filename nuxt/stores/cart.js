import { defineStore } from 'pinia'
import { useLoadingStore, useAuthStore } from '#imports'
import Cookies from 'js-cookie'

export const useCartStore = defineStore('cart', {
  state: () => {
    return {
      carts: [],
      cartCount: 0,
      totalAmount: 0,
    }
  },
  getters: {
    getCartCount: (state) => state.cartCount,
    getCart: (state) => state.carts,
    getTotalAmount: (state) => state.totalAmount,
    getCartSelected: (state) => state.carts?.filter((cart) => cart.is_selected),
  },
  actions: {
    setCartCount(count) {
      this.cartCount = count
    },
    setCarts(carts) {
      this.carts = carts
    },
    setTotalAmount(amount) {
      this.totalAmount = amount
    },
    async getAllCarts() {
      const { $axios } = useNuxtApp()
      const loadingStore = useLoadingStore()
      const authStore = useAuthStore()
      const session_id = Cookies.get('session_id')
      loadingStore.setLoading(true)

      try {
        const endpoint = authStore.isSignedIn
          ? '/carts'
          : '/carts?session_id=' + session_id

        const response = await $axios.get(endpoint)
        this.setCarts(response.data?.items)
        this.setCartCount(response.data?.items?.length)
        this.setTotalAmount(response.data?.total_amount)
      } catch (error) {
      } finally {
        loadingStore.setLoading(false)
      }
    },
    async addToCart(payload) {
      const { $axios } = useNuxtApp()
      const authStore = useAuthStore()
      const session_id = Cookies.get('session_id')

      const endpoint = authStore.isSignedIn
        ? '/carts'
        : '/carts?session_id=' + session_id

      if (!payload?.product_variant_id) {
        return toast('Có lỗi vui lòng thử lại.', 'error')
      }

      const response = await $axios.post(endpoint, payload)

      this.setCartCount(response.data?.items.length)
      toast(response.messages, response.status)
    },

    async buyNowMulti(payload) {
      const { $axios } = useNuxtApp()
      const authStore = useAuthStore()
      const session_id = Cookies.get('session_id')

      const endpoint = authStore.isSignedIn
        ? '/carts/buy-now'
        : '/carts/buy-now?session_id=' + session_id

      if (!payload?.product_variant_id) {
        return toast('Có lỗi vui lòng thử lại.', 'error')
      }

      const response = await $axios.post(endpoint, payload)

      this.setCartCount(response.data?.items.length)
      toast(response.messages, response.status)
    },
    removeAllCarts() {
      this.carts = []
      this.cartCount = 0
      this.totalAmount = 0
    },
  },
})

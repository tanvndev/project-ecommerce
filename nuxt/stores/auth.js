import { defineStore } from 'pinia'
import Cookies from 'js-cookie'

export const useAuthStore = defineStore('auth', {
  state: () => {
    const token = Cookies.get('token') ?? null

    return {
      status: token ? { loggedIn: true } : { loggedIn: false },
      accessToken: token ? token : null,
      user: null,
      messages: [],
    }
  },
  getters: {
    isSignedIn: (state) => state.status.loggedIn,
    getUser: (state) => state.user,
    getToken: (state) => state.accessToken,
    getMessages: (state) => state.messages,
    getRole: (state) => state.user?.user_catalogue,
  },
  actions: {
    async logout() {
      const cartStore = useCartStore()
      const { $axios } = useNuxtApp()

      await $axios.post('/auth/logout')

      this.status.loggedIn = false
      this.accessToken = null
      this.messages = ''

      Cookies.remove('token')

      cartStore.removeAllCarts()

      if (!Cookies.get('session_id')) {
        const session_id = generateUUID()

        Cookies.set('session_id', session_id, {
          expires: parseInt(process.env.SESSION_ID_EXPIRES, 10),
        })
      }

      navigateTo('/')
    },
    setToken(token) {
      this.status.loggedIn = true
      this.accessToken = token
      this.messages = ''
    },
    removeToken() {
      this.status.loggedIn = false
      this.accessToken = null
      this.user = null
      this.messages = ''
    },
    setUser(user) {
      this.user = user
    },
  },
})

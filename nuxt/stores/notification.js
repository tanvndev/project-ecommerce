import { defineStore } from 'pinia'
import { useLoadingStore } from '#imports'

export const useNotificationStore = defineStore('notification', {
  state: () => {
    return {
      notifications: [],
    }
  },
  getters: {
    getNotifications: (state) => state.notifications,
  },
  actions: {
    async getAllNotifications() {
      const loadingStore = useLoadingStore()
    //   loadingStore.setLoading(true)
      const { $axios } = useNuxtApp()

    //   loadingStore.setLoading(true)

      try {
        const response = await $axios.get('/notifications/user')

        this.setNotification(response.data)
      } catch (error) {
      } finally {
        // loadingStore.setLoading(false)
      }
    },
    setNotification(notifications) {
      this.notifications = notifications
    },
    setNotificationCount(count) {
      this.notificationCount = count
    },
    removeNotification(notification) {
      const index = this.notifications.indexOf(notification)
      if (index > -1) {
        this.notifications.splice(index, 1)
      }
    },
    async readMarked() {
      const { $axios } = useNuxtApp()
      try {
        const response = await $axios.get('/notifications/user')

        this.setNotification(response.data)
      } catch (error) {}
    },

    readAll() {
      this.notifications.map((notification) => {
        notification.read = true
      })
    },
  },
})

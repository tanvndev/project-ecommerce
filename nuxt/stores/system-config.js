import { defineStore } from 'pinia'

export const useSystemConfigStore = defineStore('systemConfig', {
  state: () => {
    return {
      systemConfigs: [],
    }
  },
  getters: {
    getSystemConfigs: (state) => state.systemConfigs,
  },
  actions: {
    setSystemConfigs(data) {
      this.systemConfigs = data
    },
  },
})

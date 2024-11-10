import { createStore } from 'vuex';
import { authStore, antStore, finderStore, loadingStore, productStore, reportStore } from '@/store/modules/';

// Create a new store instance.
const store = createStore({
  modules: {
    antStore, // toast ant
    authStore,
    finderStore,
    loadingStore,
    productStore,
    reportStore
  }
});

export default store;

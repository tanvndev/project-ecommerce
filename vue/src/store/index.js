import {
  antStore,
  authStore,
  finderStore,
  loadingStore,
  productStore,
  reportStore,
  sidebarStore
} from '@/store/modules/';
import { createStore } from 'vuex';

// Create a new store instance.
const store = createStore({
  modules: {
    antStore, // toast ant
    authStore,
    finderStore,
    loadingStore,
    productStore,
    reportStore,
    sidebarStore
  }
});

export default store;

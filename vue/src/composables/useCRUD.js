import { BaseService } from '@/services';
import store from '@/store';
import { ref } from 'vue';

export default function useCRUD() {
  const loading = ref(false);
  const error = ref(null);
  const messages = ref(null);
  const data = ref(null);

  const getAll = async (endpoint, payload = {}) => {
    loading.value = true;

    error.value = null;
    try {
      const response = await BaseService.getAll(endpoint, payload);
      if (response.success) {
        messages.value = response.messages;
        data.value = response.data;
        return response.data;
      }
    } catch (err) {
      error.value = err;
    } finally {
      loading.value = false;
    }
  };

  const getOne = async (endpoint, id) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await BaseService.getOne(endpoint, id);
      if (response.success) {
        data.value = response.data;
        return response.data;
      }
    } catch (err) {
      error.value = err;
    } finally {
      loading.value = false;
    }
  };
  const create = async (endpoint, payload, isLoading = true) => {
    // isLoading && store.dispatch('loadingStore/startLoading');
    loading.value = true;
    error.value = null;
    try {
      const response = await BaseService.create(endpoint, payload);
      messages.value = response.messages;
      data.value = response.data;
      return response.success;
    } catch (err) {
      error.value = err;
    } finally {
      //   isLoading && store.dispatch('loadingStore/stopLoading');
      loading.value = false;
    }
  };
  const update = async (endpoint, id, payload) => {
    // store.dispatch('loadingStore/startLoading');
    loading.value = true;
    error.value = null;
    try {
      const response = await BaseService.update(endpoint, id, payload);
      messages.value = response.messages;
      return response.success;
    } catch (err) {
      error.value = err;
    } finally {
      //   store.dispatch('loadingStore/stopLoading');
      loading.value = false;
    }
  };
  const deleteOne = async (endpoint, id, payload = null) => {
    store.dispatch('loadingStore/startLoading');
    loading.value = true;
    error.value = null;
    try {
      const response = await BaseService.deleteOne(endpoint, id, payload);
      data.value = response.data;
      messages.value = response.messages;
      return response.success;
    } catch (err) {
      error.value = err;
    } finally {
      store.dispatch('loadingStore/stopLoading');
      loading.value = false;
    }
  };
  return { getAll, getOne, create, update, deleteOne, loading, error, messages, data };
}

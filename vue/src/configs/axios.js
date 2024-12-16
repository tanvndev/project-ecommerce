import router from '@/router';
import store from '@/store';
import axios from 'axios';
import Cookies from 'js-cookie';

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true
});

let refreshTokenPromise = null;

// Add a request interceptor
instance.interceptors.request.use(
  function (config) {
    // Start loading
    // store.dispatch('loadingStore/startLoading');

    // Get token
    const token = Cookies.get('token') || null;

    if (token && token != null) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error);
  }
);

// Add a response interceptor
instance.interceptors.response.use(
  function (response) {
    // Stop loading
    // store.dispatch('loadingStore/stopLoading');
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response.data;
  },
  async function (error) {
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    const originalRequest = error.config;
    if (error.response.status === 401 && !originalRequest._retry) {
      if (!refreshTokenPromise) {
        originalRequest._retry = true;
        refreshTokenPromise = store
          .dispatch('authStore/refreshToken')
          .catch((err) => {
            // Nếu refresh token thất bại, reset promise
            refreshTokenPromise = null;
            store.dispatch('authStore/logout');
            return Promise.reject(err);
          })
          .finally(() => {
            // Reset promise khi hoàn thành
            refreshTokenPromise = null;
          });
      }

      // Chờ promise refresh token hoàn tất
      await refreshTokenPromise;
      return instance(originalRequest);
    }

    // Handle 403 error (Forbidden)
    if (error.response.status === 403) {
      // Optional: Redirect to a page, such as login or an error page
      // window.location.href = '/login';

      // Redirect to 403 page using Vue Router
      router.push({ name: 'forbidden' });

      return Promise.reject(error); // Reject the promise with the error
    }

    // Handle 403 error (Forbidden)
    if (error.response.status === 429) {
      // Optional: Redirect to a page, such as login or an error page
      // window.location.href = '/login';

      // Redirect to 403 page using Vue Router
      router.push({ name: 'too-many-request' });

      return Promise.reject(error); // Reject the promise with the error
    }
    // Stop loading
    // store.dispatch('loadingStore/stopLoading');
    return Promise.reject(error);
  }
);

export default instance;

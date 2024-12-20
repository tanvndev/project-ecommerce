import router from '@/router';
import AuthService from '@/services/AuthService';
import Cookies from 'js-cookie';

const token = Cookies.get('token') || null;

// State
const state = {
  status: token ? { loggedIn: true } : { loggedIn: false },
  accessToken: token,
  user: null,
  messages: []
};

// Getters
const getters = {
  isLoggedIn: (state) => state.status.loggedIn,
  getUser: (state) => state.user,
  getToken: (state) => state.accessToken,
  getMessages: (state) => state.messages,
  getRole: (state) => state.user?.catalogue_id
};

// Actions
const actions = {
  async login({ commit }, payload) {
    const response = await AuthService.login(payload);

    if (!response.success) {
      return commit('loginFailure', response.messages);
    }

    commit('loginSuccess', response.data);
  },

  async loginOtp({ commit }, payload) {
    const response = await AuthService.loginOtp(payload);

    if (!response.success) {
      return commit('loginFailure', response.messages);
    }
    return commit('loginSuccess', response.data);
  },

  async logout({ commit }) {
    const response = await AuthService.logout();
    commit('logout');

    if (!response.success) {
      console.error(response.messages);
    }
  },

  async refreshToken({ commit }) {
    const response = await AuthService.refreshToken();

    if (!response.success) {
      return commit('loginFailure', response.messages);
    }

    commit('loginSuccess', response.data);
  },

  async googleLogin({ commit }, formData) {
    try {
      const response = await AuthService.googleLogin(formData);

      if (!response.success) {
        return commit('loginFailure', response.messages);
      }
      commit('loginSuccess', response.data);
    } catch (error) {
      console.log(error);

      throw new Error(error.response?.data.message || 'Đăng nhập thất bại.');
    }
  }
};

// Mutations
const mutations = {
  setUser(state, user) {
    state.user = user;
  },

  setIsLoggedIn(state, value) {
    state.status.loggedIn = value;
  },

  loginSuccess(state, data) {
    state.status.loggedIn = true;
    state.accessToken = data.token;
    state.user = { user_catalogue: data.catalogue };
    state.messages = [];
  },

  loginFailure(state, messages) {
    state.status.loggedIn = false;
    state.accessToken = null;
    state.user = null;
    state.messages = messages;
  },

  logout(state) {
    state.status.loggedIn = false;
    state.accessToken = null;
    state.user = null;
    state.messages = [];
    Cookies.remove('token');
    router.push('/login');
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};

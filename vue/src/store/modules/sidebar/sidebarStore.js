// state
const isShowSidebarLocal = localStorage.getItem('isShowSidebar');
const state = {
  isShow: isShowSidebarLocal ? JSON.parse(isShowSidebarLocal) : false
};

// getters
const getters = {
  getIsShow: (state) => state.isShow
};
// actions
const actions = {};

// mutations
const mutations = {
  setIsShow(state, isShow) {
    state.isShow = isShow;
    localStorage.setItem('isShowSidebar', JSON.stringify(isShow));
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};

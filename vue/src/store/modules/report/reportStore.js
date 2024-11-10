// state
const state = {
  allDayFormat: []
};

// getters
const getters = {
  getAllDayFormat: (state) => state.allDayFormat
};
// actions
const actions = {};

// mutations

const mutations = {
  setAllDayFormat(state, payload) {
    state.allDayFormat = payload;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};

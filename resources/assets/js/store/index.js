import Vue from 'vue';
import Vuex from 'vuex';

import { mutations } from './mutations'

Vue.use(Vuex);

export default new Vuex.Store({
    strict: true,
    state: {
        showLoader: false,
        showModal: false,
        activeUser: {
          id: null,
          name: null
        }
    },
    mutations: Object.assign({}, mutations)
})

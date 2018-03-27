import Vue from 'vue';
import Vuex from 'vuex';

// import { productActions, manufacturerActions } from './actions'
import { mutations } from './mutations'

Vue.use(Vuex);

export default new Vuex.Store({
    strict: true,
    state: {
        showLoader: false,
        showModal: false
    },
    mutations: Object.assign({}, mutations),
    // getters: Object.assign({}, productGetters, manufacturerGetters),
    // actions: Object.assign({}, productActions, manufacturerActions)
})

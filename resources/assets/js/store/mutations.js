import {
    LOADING_FAILURE,
    LOADING_SUCCESS,
    LOADING,
    SUCCESS_MSG,
    ERROR_MSG,
    MODAL_OPEN,
    MODAL_CLOSE
} from './mutation-types'


export const mutations = {
    [LOADING] (state) {
        state.showLoader = true;
    },
    [LOADING_SUCCESS] (state, payload) {
        state.showLoader = false;
    },
    [MODAL_OPEN] (state) {
        state.showModal = true;
    },
    [MODAL_CLOSE] (state, payload) {
        state.showModal = false;
    },
};
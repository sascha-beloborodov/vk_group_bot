import {
    LOADING_FAILURE,
    LOADING_SUCCESS,
    LOADING,
    SUCCESS_MSG,
    ERROR_MSG,
    MODAL_OPEN,
    MODAL_CLOSE,
    SET_ACTIVEUSER,
    LOGIN_USER,
    LOGOUT_USER
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
    [SET_ACTIVEUSER] (state, payload) {
        state.activeUser = {
          id: payload.vk_id,
          name: `${payload.first_name} ${payload.last_name}`
        };
    },
    [LOGIN_USER] (state) {
        state.isLoggedIn = true
    },
    [LOGOUT_USER] (state) {
        state.isLoggedIn = false
    },
};

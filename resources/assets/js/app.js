require('./bootstrap');

// window.Vue = require('vue');

// import Vue from 'vue';
// import VueRouter from 'vue-router'
//
// // Vue.use(VueRouter);
//
// const FaqList = Vue.component('faq-list', require('./components/FaqList.vue'));
// const MessagesList = Vue.component('messages-list', require('./components/MessagesList.vue'));
// debugger;
// const routes = [
//     { path: '/faq', component: FaqList },
//     { path: '/messages', component: MessagesList }
// ];
//
// const router = new VueRouter({
//     routes
// });
//
// if (document.getElementById('app')) {
//     // const app = new Vue({
//     //     el: '#app'
//     // });
//
//     const app = new Vue({
//         router
//     }).$mount('#app')
// }


import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
})

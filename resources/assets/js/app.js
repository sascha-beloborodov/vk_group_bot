require('./bootstrap');

require('vue-toastr/src/vue-toastr.less');

import 'semantic-ui-css/semantic.min.css';
import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import Toastr from 'vue-toastr';
import SuiVue from 'semantic-ui-vue';

Vue.use(Toastr);
Vue.use(SuiVue);

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
});

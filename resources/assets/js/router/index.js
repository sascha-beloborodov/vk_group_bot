import Vue from 'vue'
import Router from 'vue-router'

import MessagesList from './messages';
import FaqList from './faq';
import { usersList } from "./users";
import Notify from "./notify";

Vue.use(Router);

export default new Router({
    routes: [
        MessagesList,
        FaqList,
        usersList,
        Notify
    ]
});

import Vue from 'vue'
import Router from 'vue-router'

import MessagesList from './messages';
import FaqList from './faq';
import { user, usersList} from "./users"

Vue.use(Router);

export default new Router({
    routes: [
        MessagesList,
        FaqList,
        user,
        usersList
    ]
});

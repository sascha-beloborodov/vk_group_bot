import Vue from 'vue'
import Router from 'vue-router'

import MessagesList from './messages';
import FaqList from './faq';
import { usersList } from "./users";
import Notify from "./notify";
import { photoList } from "./photos";
import { festList } from "./fests";
import { participantList } from "./participants";
import { auth } from './auth';
import store from '../store';

Vue.use(Router);

const router = new Router({
    routes: [
        {
            path: '/',
            meta: {
                requiresAuth: true
            }
        },
        MessagesList,
        FaqList,
        usersList,
        photoList,
        festList,
        participantList,
        Notify,
        auth
    ]
});

router.beforeEach((to, from, next) => {

    // check if the route requires authentication and user is not logged in
    if (to.matched.some(route => route.meta.requiresAuth) && !store.state.isLoggedIn) {
        // redirect to login page
        next({ path: '/auth/login' });
        return;
    }

    // if logged in redirect to users
    if(to.path === '/login' && store.state.isLoggedIn) {
        next({ path: 'users' });
        return;
    }

    next();
})

export default router;
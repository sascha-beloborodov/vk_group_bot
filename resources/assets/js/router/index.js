import Vue from 'vue'
import Router from 'vue-router'

import MessagesList from './messages';
import FaqList from './faq';
import { usersList } from "./users";
import Notify from "./notify";
import {photoList} from "./photos";
import {festList} from "./fests";

Vue.use(Router);

export default new Router({
    routes: [
        MessagesList,
        FaqList,
        usersList,
        photoList,
        festList,
        Notify
    ]
});

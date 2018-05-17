import Notify from '../components/Notify';

export default {
    path: '/notify',
    name: 'Notify',
    meta: {
      breadcrumb: 'Нотификация пользователей',
      requiresAuth: true
    },
    component: Notify
};

import FaqList from '../pages/FaqList'

export default {
    path: '/faq',
    name: 'FaqList',
    meta: {
      breadcrumb: 'Вопросы FAQ',
      requiresAuth: true
    },
    component: FaqList
}

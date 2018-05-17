import MessagesList from '../pages/MessagesList'

export default {
    path: '/messages-list',
    name: 'MessagesList',
    meta: {
      breadcrumb: 'Сообщения',
      requiresAuth: true
    },
    component: MessagesList
}

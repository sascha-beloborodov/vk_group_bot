import User from '../pages/User';
import UserList from '../pages/UserList';

export const usersList = {
    path: '/users/',
    component: UserList,
    name: 'UserList',
    meta: {
      breadcrumb: 'Пользователи',
      requiresAuth: true
    },
    children: [
      {
        path: ':id',
        name: 'User',
        component: User,
        meta: {
          dynamic: true,
          breadcrumb: user => user.name,
          requiresAuth: true
        }
      }
    ]
};

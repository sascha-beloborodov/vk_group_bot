import User from '../pages/User';
import UserList from '../pages/UserList';

// export const user = {
//     path: '/users/:id',
//     name: 'User',
//     meta: {
//       breadcrumb: 'Пользователи/:id'
//     },
//     component: User
// };

export const usersList = {
    path: '/users/',
    component: UserList,
    name: 'UserList',
    meta: {
      breadcrumb: 'Пользователи',
    },
    children: [
      {
        path: ':id',
        name: 'User',
        component: User,
        meta: {
          dynamic: true,
          breadcrumb: user => user.name
        }
      }
    ]
};

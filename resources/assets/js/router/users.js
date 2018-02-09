import User from '../pages/User';
import UserList from '../pages/UserList';

export const user = {
    path: '/users/:id',
    name: 'User',
    component: User
};

export const usersList = {
    path: '/users',
    name: 'UserList',
    component: UserList
};
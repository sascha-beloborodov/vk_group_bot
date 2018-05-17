import Login from '../components/auth/Login';
import Logout from '../components/auth/Logout';
import AuthPage from '../components/auth/AuthPage';

export const auth = {
    path: '/auth/',
    name: 'Auth',
    component: AuthPage,
    meta: {
        requiresAuth: true
    },
    children: [
        {
            path: 'login',
            name: 'Login',
            component: Login,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: 'logout',
            name: 'Logout',
            component: Logout,
            meta: {
                requiresAuth: true
            }
        }
    ]
};

import SunmarPage from '../components/sunmar/SunmarPage';
import SunmarUsers from '../components/sunmar/SunmarUsers';

import Tasks from '../components/sunmar/Tasks';


export const sunmar = {
    path: '/sunmar/',
    component: SunmarPage,
    name: 'SunmarPage',
    meta: {
        breadcrumb: 'Санмар',
    },
    children: [
        {
            path: 'users',
            name: 'SunmarUsers',
            component: SunmarUsers,
            meta: {
                isListHidden: true,
                dynamic: true,
                breadcrumb: () => 'Пользователи'
            }
        },
        {
            path: 'tasks',
            name: 'Tasks',
            component: Tasks,
            meta: {
                isListHidden: false,
                breadcrumb: 'Задания'
            }
        },
    ]
};

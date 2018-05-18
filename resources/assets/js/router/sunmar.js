import SunmarPage from '../components/sunmar/SunmarPage';
import SunmarUsers from '../components/sunmar/SunmarUsers';

import FirstTask from '../components/sunmar/FirstTask';
import SecondTask from '../components/sunmar/SecondTask';
import ThirdTask from '../components/sunmar/ThirdTask';
import FourthTask from '../components/sunmar/FourthTask';
import FifthTask from '../components/sunmar/FifthTask';
import SixthTask from '../components/sunmar/SixthTask';
import SeventhTask from '../components/sunmar/SeventhTask';

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
            path: 'firstTask',
            name: 'FirstTask',
            component: FirstTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Первое задание'
            }
        },
        {
            path: 'secondTask',
            name: 'SecondTask',
            component: SecondTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Второе задание'
            }
        },
        {
            path: 'thirdTask',
            name: 'thirdTask',
            component: ThirdTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Третье задание'
            }
        },
        {
            path: 'fourthTask',
            name: 'FourthTask',
            component: FourthTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Четвертое задание'
            }
        },
        {
            path: 'fifthTask',
            name: 'FifthTask',
            component: FifthTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Пятое задание'
            }
        },
        {
            path: 'sixthTask',
            name: 'SixthTask',
            component: SixthTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Шестое задание'
            }
        },
        {
            path: 'seventhTask',
            name: 'SeventhTask',
            component: SeventhTask,
            meta: {
                isListHidden: false,
                breadcrumb: 'Седьмое задание'
            }
        },
    ]
};

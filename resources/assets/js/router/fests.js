import Fest from '../components/fests/Fest';
import FestList from '../components/fests/FestList';
import FestAdd from '../components/fests/FestAdd.vue';

export const festList = {
    path: '/fests/',
    component: FestList,
    name: 'FestList',
    meta: {
        breadcrumb: 'Фестивали',
    },
    children: [
        {
            path: 'add',
            name: 'FestAdd',
            component: FestAdd,
            meta: {
                isListHidden: true,
                dynamic: true,
                breadcrumb: () => 'Добавить фестиваль'
            }
        },
        {
            path: ':id',
            name: 'Fest',
            component: Fest,
            meta: {
                dynamic: true,
                breadcrumb: fest => fest.city
            }
        }
    ]
};

import Fest from '../pages/Fest';
import FestList from '../pages/FestList';

export const festList = {
    path: '/fests/',
    component: FestList,
    name: 'FestList',
    meta: {
        breadcrumb: 'Фестивали',
    },
    children: [
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

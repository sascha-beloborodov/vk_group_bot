import Photo from '../pages/Photo';
import PhotoList from '../pages/PhotoList';

export const photoList = {
    path: '/photos/',
    component: PhotoList,
    name: 'PhotoList',
    meta: {
        breadcrumb: 'Фотоконкурс',
    },
    children: [
        {
            path: ':id',
            name: 'Photo',
            component: Photo,
            meta: {
                dynamic: true,
                breadcrumb: photo => photo.id
            }
        }
    ]
};

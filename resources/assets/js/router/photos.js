import Photo from '../pages/Photo';
import PhotoList from '../pages/PhotoList';
import PhotoAdd from '../components/photos/PhotoAdd';

export const photoList = {
    path: '/photos/',
    component: PhotoList,
    name: 'PhotoList',
    meta: {
        breadcrumb: 'Фотоконкурс',
    },
    children: [
        {
            path: 'add',
            name: 'PhotoAdd',
            component: PhotoAdd,
            meta: {
                isListHidden: true,
                dynamic: true,
                breadcrumb: () => 'Добавить фото'
            }
        },
        {
            path: ':id',
            name: 'Photo',
            component: Photo,
            meta: {
                isListHidden: false,
                dynamic: true,
                breadcrumb: photo => photo.id
            }
        },
    ]
};

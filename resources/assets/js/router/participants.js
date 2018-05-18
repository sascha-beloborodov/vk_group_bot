import Participant from '../components/participants/Participant';
import ParticipantList from '../components/participants/ParticipantList';
import ParticipantAdd from '../components/participants/ParticipantAdd';

export const participantList = {
    path: '/participants/',
    component: ParticipantList,
    name: 'ParticipantList',
    meta: {
        breadcrumb: 'Участники голосования',
    },
    children: [
        {
            path: 'add',
            name: 'ParticipantAdd',
            component: ParticipantAdd,
            meta: {
                isListHidden: true,
                dynamic: true,
                breadcrumb: () => 'Добавить участников'
            }
        },
        {
            path: ':id',
            name: 'Participant',
            component: Participant,
            meta: {
                isListHidden: false,
                dynamic: true,
                breadcrumb: participant => participant.id
            }
        },
    ]
};

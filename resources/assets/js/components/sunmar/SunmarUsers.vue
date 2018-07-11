<template>
    <div class='row show-page-container'>
        <div class="col-md-8">
            <button class="btn btn-primary btn-popup" @click="toggleModal" :diabled="false">Отправить сообщение участникам</button>
        </div>
        <div class="col-md-8" v-if="isLoaded">
            <div class="row" v-if="users.length">
                <div class="col-md-12">
                    <table class="table table-hover">
                        <tr>
                            <td>id</td>
                            <td>Имя</td>
                            <td>Аватар</td>
                            <td>Добавлен</td>
                            <td>Учавствует</td>
                            <td>Причина отказа</td>
                            <td>Возраст</td>
                            <td>Живет</td>
                            <td>Хочет</td>
                            <td>Зарегистрирован</td>
                            <td>#1</td>
                            <td>#2</td>
                            <td>#3</td>
                            <td>#4</td>
                            <td>#5</td>
                            <td>#6</td>
                            <td>#7</td>
                        </tr>
                        <tr v-for="(user, idx) in users" :class="{deesagred : user.is_agreed == 2 || user.is_agreed == 0 }">
                            <td><a :href="vkPage(user.vk_id)" _target="blank">{{user.vk_id}}</a></td>
                            <td>{{user.first_name }} {{ user.last_name}}</td>
                            <td><img :src="user.photo_50" alt=""></td>
                            <td>{{user.created_at}}</td>
                            <td>{{user.is_agreed == 1 ? 'Да' : 'Нет'}}</td>
                            <td>{{user.is_agreed == 0 ? 'Сразу вышел/не стал уточнять' : user.reason_disagree}}</td>
                            <td>{{user.age}}</td>
                            <td>{{user.city_of_life}}</td>
                            <td>{{user.city_of_travel}}</td>
                            <td>{{user.completed}}</td>
                            <td>{{user.first_task.completed}}</td>
                            <td>{{user.second_task.completed}} <br> {{user.second_task.link}}</td>
                            <td>{{user.third_task.completed}} <br> {{user.third_task.link}}</td>
                            <td>Статус - {{user.fourth_task.completed}} <br>   
                                Согласен - {{user.fourth_task.info.is_agreed}} <br>
                                <span v-if="!user.fourth_task.info.is_agreed">Причина - {{user.fourth_task.info.reason_disagreed}} <br></span>        
                                Ответы: <br>
                                <ul><li v-for="answer in user.fourth_task.info.answers">{{answer.name}} - {{answer.right ? '+' : '-'}}</li></ul>
                              </td>
                            <td>{{user.fifth_task.completed}}</td>
                            <td>{{user.sixth_task.completed}}</td>
                            <td>{{user.seventh_task.completed}}</td>
                        </tr>
                    </table>
                    <br>
                    <button class="btn btn-primary" @click="importParticipants()">Список в CSV</button>
                    <br><br>
                    <pagination
                            :currentPage="currentPage"
                            :lastPage="lastPage"
                            :url="url"
                            :perPage="perPage"
                            :total="total"></pagination>
                </div>
            </div>
        </div>
        <message-modal v-if="showModal">
            <div slot="header">
                Сообщение участникам
            </div>
            <div slot="body">
                <textarea class="form-control" v-model="message" cols="30" rows="10"></textarea>
            </div>
            <div slot="footer">
                <button class="btn btn-success" @click="send()">Отправить</button>
                <button class="btn btn-danger" @click="toggleModal()">Закрыть</button>
            </div>
        </message-modal>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE,
        SET_ACTIVEUSER
    } from '../../store/mutation-types';

    import { participantMixin } from '../../mixins/participants';
    import moment from 'moment';
    import MessageModal from '../MessageModal';
    import Pagination from '../Pagination';
//    import toastr from 'toastr';
    require('toastr');

    export default {
        data() {
            return {
                users: [],
                isLoaded: false,
                message: '',

                currentPage: 1,
                perPage: null,
                lastPage: null,
                total: null,

                filter: {
                    type: 'faq'
                }
            }
        },

        components: {
            messageModal: MessageModal,
            pagination: Pagination
        },

        created() {
            this.fetchData();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
            url() {
                return `#/sunmar/users`;
            }
        },

        watch: {
            $route() {
                this.fetchData();
            }
        },

        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                const query = this.$route.query;
                if (query.page === undefined) {
                    query.page = 1;
                }
                axios.get(`/admin/sunmar/users?page=${query.page}`, { params: this.filter }).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.users = response.data.users.data;
                    this.currentPage = response.data.users.current_page;
                    this.perPage = response.data.users.per_page;
                    this.lastPage = response.data.users.last_page;
                    this.total = response.data.users.total;
                });
            },
            vkPage(userId) {
                return `https://vk.com/id${userId}`;
            },
            importParticipants() {
                window.open(`/admin/sunmar/users/import`);
            },
            toggleModal() {
                this.$store.state.showModal ? this.$store.commit(MODAL_CLOSE) : this.$store.commit(MODAL_OPEN);
            },
            send() {
                if (!this.message.length) {
                    this.$toastr.e("Вы не ввели сообщение");
                } else if (this.message.length > 700) {
                    this.$toastr.e("Сообщение слишком длинное");
                } else {
                    this.$store.commit(LOADING);
                    this.error = false;
                    axios.post('/admin/sunmar/message', { text: this.message, }).then((response) => {
                        this.closeModal();
                        this.$toastr.s("Сообщения начитнают рассылаться");
                        this.message = '';
                        this.$store.commit(LOADING_SUCCESS);
                    }).catch((error) => {
                        console.log(error);
                        this.$toastr.e("Произошла ошибка");
                    });
                }
            },
        },
        filters: {
            frotTimeStamp(value) {
                return moment.unix(value).format('DD.MM.YYYY HH:mm:ss');
            },
            dateConvert(value) {
                return moment(value).format('DD.MM.YYYY HH:mm:ss');
            },
        }
    };
</script>

<style>
    td {
        padding: 5px;
        border-bottom: 1px solid #b9b9b9ee;
    }
    .deesagred {
        background: #afafaf;
    }
</style>

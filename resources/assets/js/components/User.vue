<template>
    <div class='row show-page-container'>
        <div class="col-md-8" v-if="isLoaded">
            <h1>Пользователь <br>{{ user.first_name }} {{ user.last_name }}</h1>

            <div class="row">
                <div class="col-md-12">
                    #id {{ user.vk_id }} <br><br>
                    <img :src="user.photo_100" alt=""><br><br>
                    Есть ли аккаунт на kfcbattle.com: {{ user.has_kfc ? "да" : "нет" }} <br>
                    Первое сообщение боту: {{ user.created_at | dateConvert }} <br>
                    Всего сообщений : {{ totalMessages }} <br>
                    Новых сообщений: {{ unreadMessages }}
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary" @click="openModal()">Отправить сообщение</button>
                    <button class="btn btn-danger" @click="clearAttempts()">Очистить попытки FAQ</button>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Тип:</label>
                        <select name="" id="" v-model="filter.type" class="form-control" @change="changeType">
                            <option :value="''" selected>Все</option>
                            <option :value="'faq'">FAQ</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-if="messages.length">
                <div class="col-md-12">
                    <table class="table table-hover">
                        <tr>
                            <td>Сообщение</td>
                            <td>Время</td>
                            <td>От кого</td>
                            <td>Новое</td>
                            <td>Тип</td>
                        </tr>
                        <tr v-for="(message, idx) in messages">
                            <td>{{ message.data.body }}</td>
                            <td>{{ message.data.date | dateFormat }}</td>
                            <td>{{ message.data.from == 'admin' ? "Администратор" : "Пользователь" }}</td>
                            <td>{{ message.is_new ? '+' : '-' }}</td>
                            <td>{{ message.data.type }}</td>
                        </tr>
                    </table>
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
                Написать сообщение пользователю
            </div>
            <div slot="body">
                <textarea class="form-control" v-model="message" cols="30" rows="10"></textarea>
            </div>
            <div slot="footer">
                <button class="btn btn-success" @click="reply()">Отправить</button>
                <button class="btn btn-danger" @click="closeModal()">Закрыть</button>
            </div>
        </message-modal>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE
    } from '../store/mutation-types';

    import moment from 'moment';
    import MessageModal from './MessageModal';
    import Pagination from './Pagination';
//    import toastr from 'toastr';
    require('toastr');

    export default {
        data() {
            return {
                user: null,
                messages: [],
                isLoaded: false,
                message: '',

                totalMessages: 0,
                unreadMessages: 0,

                currentPage: 1,
                perPage: null,
                lastPage: null,
                total: null,
                chosenPage: null,
                hasPagination: null,
                visiblePages: [],

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
            this.fetchUserData();
            this.fetchUserMessages();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
            url() {
                return `#/users/${this.$route.params.id}`;
            }
        },

        watch: {
            $route() {
                this.fetchUserMessages();
            }
        },

        methods: {
            fetchUserData() {
                this.$store.commit(LOADING);
                axios.get(`/admin/users/${this.$route.params.id}`).then((response) => {
                    this.isLoaded = true;
                    this.user = response.data.user;
                });
            },

            fetchUserMessages() {
                this.$store.commit(LOADING);
                const query = this.$route.query;
                if (query.page === undefined) {
                    query.page = 1;
                }
                axios.get(`/admin/users/${this.$route.params.id}/messages?page=${query.page}`, { params: this.filter }).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.messages = response.data.messages.data;
                    this.totalMessages = response.data.totalMessages;
                    this.unreadMessages = response.data.unreadMessages;
                    this.currentPage = response.data.messages.current_page;
                    this.perPage = response.data.messages.per_page;
                    this.lastPage = response.data.messages.last_page;
                    this.total = response.data.messages.total;
                });
            },

            reply(index) {
                const data = { text: this.message };
                this.$store.commit(LOADING);
                axios
                    .post(`/admin/send-message/${this.$route.params.id}`, data)
                    .then((response) => {
                        this.fetchUserData();
                        this.closeModal();
                        this.$store.commit(LOADING_SUCCESS);
                    })
                    .catch(err => this.$store.commit(LOADING_SUCCESS));
            },

            clearAttempts() {
                this.$store.commit(LOADING);
                axios
                    .post(`/admin/clear-attempts/${this.$route.params.id}`)
                    .then((response) => {
                        this.$store.commit(LOADING_SUCCESS);
                        toastr.success('Теперь пользователь может снова спрашивать', 'Попытки FAQ сброшены')
                    })
                    .catch(err => this.$store.commit(LOADING_SUCCESS));
            },

            openModal() {
                this.$store.commit(MODAL_OPEN);
            },

            closeModal() {
                this.message = '';
                this.$store.commit(MODAL_CLOSE);
            },

            changeType(e) {
                this.filter.type = e.target.value || '';
                this.fetchUserMessages();
            }

        },

        filters: {
            dateFormat(value) {
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
</style>
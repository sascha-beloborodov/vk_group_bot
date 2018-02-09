<template>
    <div class='row show-page-container'>
        <div class="col-md-8" v-if="isLoaded">
            <h1>Пользователь <br>{{ user.first_name }} {{ user.last_name }}</h1>

            <div class="row">
                <div class="col-md-12">
                    #id {{ user.vk_id }} <br><br>
                    <img :src="user.photo_100" alt=""><br><br>
                    Есть ли аккаунт на kfcbattle.com: {{ user.has_kfc ? "да" : "нет" }} <br>
                    Первое сообщение боту: {{ user.created_at | dateConvert }}
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary" @click="openModal()">Отправить сообщение</button>
                </div>
            </div>
            <br><br>
            <div class="row" v-if="messages.length">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>Сообщение</td>
                            <td>Время</td>
                            <td>От кого</td>
                        </tr>
                        <tr v-for="(message, idx) in messages">
                            <td>{{ message.data.body }}</td>
                            <td>{{ message.data.date | dateFormat }}</td>
                            <td>{{ message.data.from == 'admin' ? "Администратор" : "Пользователь" }}</td>
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

    export default {
        data() {
            return {
                user: null,
                messages: [],
                isLoaded: false,
                message: '',

                currentPage: 1,
                perPage: null,
                lastPage: null,
                total: null,
                chosenPage: null,
                hasPagination: null,
                visiblePages: []
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
                    this.$store.commit(LOADING_SUCCESS);
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
                axios.get(`/admin/users/${this.$route.params.id}/messages?page=${query.page}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.messages = response.data.messages.data;
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

            openModal() {
                this.$store.commit(MODAL_OPEN);
            },

            closeModal() {
                this.message = '';
                this.$store.commit(MODAL_CLOSE);
            },

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


</style>
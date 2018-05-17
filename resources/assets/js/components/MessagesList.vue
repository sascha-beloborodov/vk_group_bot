<template>
    <div class='ten row' v-if="isLoaded">
        <h1>Список сообщений ({{ this.unreadMessages }} | {{ this.totalMessages }})</h1>
        <div class="ui form">
            <div class="two field">
                <label for="">Тип</label>
                <select name="" id="" class="form-control" v-model="filter.type" @change="changeType">
                    <option :value="''" selected>Все</option>
                    <option :value="'faq'">FAQ</option>
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <table class="ui table">
                <tr>
                    <td>От</td>
                    <td>Текст</td>
                    <td>Новое</td>
                    <td>Дата</td>
                    <td>Тип</td>
                    <td></td>
                </tr>
                <tr v-for="(message, index) in list">
                    <td>
                        <a :href="createLink(message.data.user_id)">{{ message.data.user_id }}</a>
                    </td>
                    <td>{{ message.data.body }}</td>
                    <td>{{ message.is_new ? '+' : '-' }}</td>
                    <td>{{ message.data.type }}</td>
                    <td>{{ message.created_at }}</td>
                    <td>
                        <button class="ui primary button" id="show-modal" @click="openModal(message.data.user_id)">Ответить</button>
                    </td>
                </tr>
            </table>
            <pagination :currentPage="currentPage" :lastPage="lastPage" :url="url" :perPage="perPage" :total="total"></pagination>
        </div>
        <!-- <message-modal v-if="showModal">
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
        </message-modal> -->


        <sui-modal v-model="showModal">
            <sui-modal-header>Написать сообщение пользователю</sui-modal-header>
            <sui-modal-content>
                <sui-modal-description class="ui form">
                    <textarea class="form-control" v-model="message" cols="30" rows="10"></textarea>
                </sui-modal-description>
            </sui-modal-content>
            <sui-modal-actions>
                <sui-button floated="right" negative @click="closeModal()">
                    Закрыть
                </sui-button>
                <sui-button floated="right" positive @click="reply()">
                    Отправить
                </sui-button>

            </sui-modal-actions>
        </sui-modal>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE
    } from '../store/mutation-types';
    import MessageModal from './MessageModal';
    import Pagination from './Pagination';

    export default {
        data() {
            return {
                list: [],
                messages: [],
                totalMessages: 0,
                unreadMessages: 0,
                message: '',
                currentUserId: 0,
                filter: {
                    type: 'faq'
                },

                isLoaded: false,
                currentPage: 1,
                perPage: null,
                lastPage: null,
                total: null,
            }
        },

        components: {
            messageModal: MessageModal,
            pagination: Pagination
        },

        created() {
            this.fetchMessagesList();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
            url() {
                return `#/messages-list`;
            }
        },


        methods: {
            fetchMessagesList() {
                this.$store.commit(LOADING);
                axios.get(`/api/messages-list`, {
                    params: this.filter
                }).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.list = response.data.messages.data;
                    this.totalMessages = response.data.totalMessages;
                    this.unreadMessages = response.data.unreadMessages;
                    this.isLoaded = true;
                    this.currentPage = response.data.messages.current_page;
                    this.perPage = response.data.messages.per_page;
                    this.lastPage = response.data.messages.last_page;
                    this.total = response.data.messages.total;
                });
            },

            reply(index) {
                const data = {
                    text: this.message
                };
                this.$store.commit(LOADING);
                axios
                    .post(`/admin/send-message/${this.currentUserId}`, data)
                    .then((response) => {
                        console.log(response);
                        this.$store.commit(LOADING_SUCCESS)
                    })
                    .catch(err => this.$store.commit(LOADING_SUCCESS));
            },

            openModal(userId) {
                this.currentUserId = userId;
                this.$store.commit(MODAL_OPEN);
            },

            closeModal() {
                this.message = '';
                this.$store.commit(MODAL_CLOSE);
            },

            createLink(userId) {
                return `#/users/${userId}`;
            },

            changeType(e) {
                this.filter.type = e.target.value || '';
                this.fetchMessagesList();
            }
        }


    };
</script>

<style>
    td {
        padding: 5px;
    }
</style>
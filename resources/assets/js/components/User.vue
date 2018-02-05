<template>
    <div class='row'>
        <div class="col-md-8" v-if="isLoaded">
            <h1>Пользователь #id {{ user.vk_id }}</h1>
            <div class="row">
                <div class="col-md-12">
                    {{ user.first_name }} {{ user.last_name }} <br>
                    <img :src="user.photo_100" alt=""><br>
                    Наличие аккаунта KFC: {{ user.has_kfc ? "+" : "-" }} <br>
                    Дата инициализации: {{ user.created_at }}
                </div>
                <div class="col-md-12">
                    <button class="btn btn-primary" @click="openModal()">Отправить сообщение</button>
                </div>
            </div>

            <div class="row" v-if="messages.length">
                <div class="col-md-12">
                    <ul>
                        <li v-for="(message, idx) in messages">
                            Сообщение: {{ message.data.body }} <br>
                            Дата: {{ message.data.date }} <br>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <message-modal v-if="showModal">
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

    import MessageModal from './MessageModal';

    export default {
        data() {
            return {
                user: null,
                messages: [],
                isLoaded: false,
                message: '',
            }
        },

        components: {
            messageModal: MessageModal
        },

        created() {
            this.fetchUserData();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
        },


        methods: {
            fetchUserData() {
                this.$store.commit(LOADING);
                axios.get(`/admin/users/${this.$route.params.id}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.user = response.data.user;
                    this.messages = response.data.messages.data;
                });
            },
            reply(index) {
                const data = { text: this.message };
                this.$store.commit(LOADING);
                axios
                    .post(`/admin/send-message/${this.$route.params.id}`, data)
                    .then((response) => {
                        console.log(response);
                        this.$store.commit(LOADING_SUCCESS)
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
        }


    };
</script>
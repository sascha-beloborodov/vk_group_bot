<template>
    <div class='row'>
        <div class="col-md-8">
            <h1>Список всех сообщений</h1>
            <ul class="list-group">
                <li v-if='list.length === 0'>Нет пока сообщений!</li>
                <li class="list-group-item" v-for="(message, index) in list">
                    <b>От:</b><br>
                    <p>{{ message.data.user_id }}</p>
                    <div>
                        <button class="btn btn-primary" id="show-modal" @click="openModal(message.data.user_id)">Ответить</button>
                    </div>
                    <b>Текст:</b><br>
                    <p>{{ message.data.body }}</p>
                    <b>Аттачс (пока нет):</b>
                </li>
            </ul>
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
                list: [],
                messages: [],
                message: '',
                currentUserId: 0
            }
        },

        components: {
            messageModal: MessageModal
        },

        created() {
            this.fetchMessagesList();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
        },


        methods: {
            fetchMessagesList() {
                this.$store.commit(LOADING);
                axios.get(`/admin/messages-list`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.list = response.data.data;
                });
            },

            reply(index) {
                const data = { text: this.message };
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
            }
        }


    };
</script>
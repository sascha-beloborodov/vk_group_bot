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
                        <textarea class="form-control" v-model="messages[index]" cols="30" rows="10"></textarea>
                        <a href="#" @click="reply(message.data.user_id, index)">Ответить</a>
                    </div>
                    <b>Текст:</b><br>
                    <p>{{ message.data.body }}</p>
                    <b>Аттачс (пока нет):</b>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING
    } from '../store/mutation-types'


    export default {
        data() {
            return {
                list: [],
                messages: []
            }
        },

        created() {
            this.fetchMessagesList();
        },

        methods: {
            fetchMessagesList() {
                this.$store.commit(LOADING);
                axios.get(`/admin/messages-list`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.list = response.data.data;
                });
            },

            reply(userId, index) {
                const data = { text: this.messages[index] };
                axios.post(`/admin/send-message/${userId}`, data).then((response) => {
                    this.messages[index] = '';
                });
            }
        }


    };
</script>
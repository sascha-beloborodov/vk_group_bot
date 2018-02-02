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
                        <textarea v-model="messages[index]" cols="30" rows="10"></textarea>
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
                axios.get(`/admin/messages-list`).then((response) => {
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
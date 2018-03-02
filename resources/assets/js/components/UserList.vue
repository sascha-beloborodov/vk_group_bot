<template>
    <div class="row show-page-container">
        <div class="col-md-8" v-if="isLoaded">
            <h2>Список пользователей</h2>
            <div class="row">
                <div class="col-md-3">

                </div>
            </div>
            <div class="row">
                <table class="table table-hover">
                    <tr>
                        <td>ID</td>
                        <td>Имя</td>
                        <td>Аккаунт KFC</td>
                        <td>Время последнего сообщения</td>
                        <td>Ожидает ответа</td>
                    </tr>
                    <tr v-for="(user, idx) in list" @click="chooseUser(user.vk_id)" :class="{ reached: isLimitReached(user) }">
                        <td>{{ user.vk_id }}</td>
                        <td>{{ user.first_name }} {{ user.last_name }}</td>
                        <td>{{ user.has_kfc ? 'да' : 'нет' }}</td>
                        <td>{{ user.lastMessage ? user.lastMessage.created_at : '' }}</td>
                        <td>{{ isLimitReached(user) ? 'Да (' + user.attempts.attempts + ')' : 'Нет' }}</td>
                    </tr>
                </table>
                <pagination
                        :currentPage="currentPage"
                        :lastPage="lastPage"
                        :url="url"
                        :perPage="perPage"
                        :total="total"></pagination>
            </div>

        </div>
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

        data () {
            return {
                list: [],
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
            this.fetchList();
        },

        computed: {
            url() {
                return `#/users`;
            }
        },

        methods: {
            fetchList() {
                this.$store.commit(LOADING);

                const query = this.$route.query;
                if (query.page === undefined) {
                    query.page = 1;
                }
                axios.get(`/admin/users?page=${query.page}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.list = response.data.users.data;
                    this.currentPage = response.data.users.current_page;
                    this.perPage = response.data.users.per_page;
                    this.lastPage = response.data.users.last_page;
                    this.total = response.data.users.total;
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            },
            makeUserUrl(userId) {
                return `#/users/${userId}`;
            },
            isLimitReached(user) {
                if (!user.attempts) {
                    return false;
                }
                return user.attempts.attempts >= 3;
            },
            chooseUser(userVkId) {
                this.$router.push({ name: 'User', params: { id: userVkId }});
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
    }
</script>

<style>

    .reached {
        background: rgba(231, 43, 40, 0.67);
    }

    tr:hover {
        cursor: pointer;
        background: rgba(194, 194, 194, 0.15);
    }

</style>
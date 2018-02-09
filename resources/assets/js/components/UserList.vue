<template>
    <div class="row show-page-container">
        <div class="col-md-8" v-if="isLoaded">
            <h2>Список пользователей</h2>
            <table class="table">
                <tr>
                    <td>ID</td>
                    <td>Имя</td>
                    <td>Аккаунт KFC</td>
                    <td>Дата первого сообщения</td>
                    <td></td>
                </tr>
                <tr v-for="(user, idx) in list">
                    <td>{{ user.vk_id }}</td>
                    <td>{{ user.first_name }} {{ user.last_name }}</td>
                    <td>{{ user.has_kfc ? 'да' : 'нет' }}</td>
                    <td>{{ user.created_at | dateConvert }}</td>
                    <td><a :href="makeUserUrl(user.vk_id)">Сообщения</a></td>
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
                return `#/faq`;
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
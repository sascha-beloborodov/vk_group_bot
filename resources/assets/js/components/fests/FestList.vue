<template>
    <div class="row show-page-container">
        <router-view></router-view>
        <div class="col-md-8" v-if="isLoaded && !$route.params.id && !$route.meta.isListHidden" >
            <h2>Список фестивалей</h2>
            <div class="row">
                <table class="table table-hover">
                    <tr>
                        <td>ID</td>
                        <td>City</td>
                        <td>Date</td>
                        <td></td>
                    </tr>
                    <tr v-for="(fest, idx) in list">
                        <td>{{ fest.id }}</td>
                        <td>{{ fest.name }}</td>
                        <td>{{ fest.date }}</td>
                        <td>
                            <button class="btn btn-success" @click="chooseFest(fest._id)"><i class="glyphicon glyphicon-pencil"></i></button>
                            <button class="btn btn-danger" @click="removeFest(fest._id)"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>
                </table>
                <router-link :to="{ name: 'FestAdd' }">Добавить фестиваль</router-link>
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
    } from '../../store/mutation-types';

    import moment from 'moment';
    import MessageModal from '../MessageModal';
    import Pagination from '../Pagination';

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
                return `#/fests`;
            }
        },

        watch: {
            $route() {
                this.fetchList();
            }
        },

        methods: {
            fetchList() {
                this.$store.commit(LOADING);

                const query = this.$route.query;
                if (query.page === undefined) {
                    query.page = 1;
                }
                axios.get(`/admin/fests?page=${query.page}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.list = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.perPage = response.data.per_page;
                    this.lastPage = response.data.last_page;
                    this.total = response.data.total;
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            },

            chooseFest(id) {
                this.$router.push({ name: 'Fest', params: { id: id.$oid }});
            },

            removeFest(id) {
                this.$store.commit(LOADING);
                axios.delete(`/admin/fests/${id.$oid}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
                this.fetchList();
            }
        },

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

    td, th {
        padding: 5px 0 5px 0;
    }

    tr {
        border-bottom: 1px #b5b5b5 solid;
    }
</style>

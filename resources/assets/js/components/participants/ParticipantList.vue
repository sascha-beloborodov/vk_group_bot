<template>
    <div class="row show-page-container">
        <router-view></router-view>
        <div class="col-md-8" v-if="isLoaded && !$route.params.id && !$route.meta.isListHidden" >
            <h2>Список фото</h2>
            <div class="row">
                <table class="table table-hover">
                    <tr>
                        <td>City ID</td>
                        <td>City</td>
                        <td>Name</td>
                    </tr>
                    <tr v-for="(photo, idx) in list" @click="choosePhoto(photo._id)">
                        <td>{{ photo.city_id }}</td>
                        <td>{{ photo.city }}</td>
                        <td>{{ photo.name }}</td>
                        <td></td>
                     </tr>
                </table>
                <router-link :class="{btn: true, ['btn-primary']: true}" :to="{ name: 'PhotoAdd' }">Добавить идентификатор</router-link>
                <pagination
                        :currentPage="currentPage"
                        :lastPage="lastPage"
                        :url="url"
                        :perPage="perPage"
                        :total="total"></pagination>
            </div>
            <hr>
            <div class="row">
                
                <h3>Скачать результаты</h3>
                <div class="col-md-4">
                    <div class="form-group">
                        <select v-model="currentCity.id" class="form-control">
                            <option :value="city.id" v-for="city in cities">{{city.name.toUpperCase()}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for=""></label>
                    <button :class="{
                            btn: true,
                            ['btn-success']: currentCity.id,
                            ['btn-default']: !currentCity.id
                        }" :disabled="!currentCity.id" @click="downloadResults">Скачать</button>
                </div>
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
                cities: [],
                currentCity: {
                    id: null,
                    name: ''
                }
            }
        },

        components: {
            messageModal: MessageModal,
            pagination: Pagination
        },

        created() {
            axios.get(`/admin/fests/all`).then((response) => {
                this.$store.commit(LOADING_SUCCESS);
                this.isLoaded = true;
                this.cities = response.data;
            }).catch(error => { console.warn(error); });
            this.fetchList();
        },

        computed: {
            url() {
                return `#/photos`;
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
                axios.get(`/admin/photos?page=${query.page}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.list = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.perPage = response.data.per_page;
                    this.lastPage = response.data.last_page;
                    this.total = response.data.total;
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            },

            choosePhoto(id) {
                this.$router.push({ name: 'Photo', params: { id: id.$oid }});
            },

            downloadResults() {
                if (!this.currentCity.id) {
                    this.$toastr.e("Вы не выбрали город");
                    return;
                }
                window.open(`/admin/photo/votes?id=${this.currentCity.id}`);
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

</style>

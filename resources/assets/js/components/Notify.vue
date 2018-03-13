<template>
    <div class='row show-page-container'>
        <div class="col-md-12" v-if="isLoaded">
            <div class="row">
                <div class="col-md-8">
                    <h2>Отправить сообщение всем пользователям</h2>
                    <div class="form-group">
                        <label for="">Текст сообщения:</label>
                        <textarea class="form-control" name="" id="" cols="30" rows="10" v-model="text"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="">Выберите город:</label>
                        <select name="" id="" class="form-control" v-model="currentCity">
                            <option :value="city" v-for="city in cities">{{city}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <button class="btn btn-primary" @click="openModal()">Отправить</button>
                </div>
            </div>



            <message-modal v-if="showModal">
                <div slot="header">
                    Уведомление пользователей
                </div>
                <div slot="body">
                    Вы действительно хотите уведомить n пользователей из для города - X
                </div>
                <div slot="footer">
                    <button class="btn btn-success" @click="notify()">Отправить</button>
                    <button class="btn btn-danger" @click="closeModal()">Закрыть</button>
                </div>
            </message-modal>
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
    require('toastr');

    export default {
        data() {
            return {
                text: '',
                isLoaded: false,
                cities: [],
                currentCity: ''
            }
        },

        components: {
            messageModal: MessageModal,
        },

        created() {
            this.fetchData();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
        },

        watch: {

        },

        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                axios.get('/admin/cities').then((response) => {
                    this.cities = response.data;
                    axios.get('/admin/notifications').then((response) => {
                        this.$store.commit(LOADING_SUCCESS);
                        this.isLoaded = true;
                    });
                });
            },
            notify() {
                axios.post('/admin/notify', { text: this.text }).then((response) => {
                    debugger;
                });
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

        }


    };
</script>

<style>
    td {
        padding: 5px;
        border-bottom: 1px solid #b9b9b9ee;
    }
</style>
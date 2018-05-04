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
                        <select name="" id="" class="form-control" v-model="currentCity" @change="setUsersCount()">
                            <option
                                    :value="city"
                                    v-for="city in cities">{{city.city.toUpperCase()}} {{!city.activity ? ' (нет активностей)' : ''}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <input type="checkbox" v-model="checkedActivity" :disabled="!currentCity.activity" @change="setUsersCount()">
                        <label for="">Только активности</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <button class="btn btn-primary" @click="openModal()" :disabled="!usersCount">Отправить</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <table class="table">
                        <tr>
                            <td>Текст</td>
                            <td>Добавлено</td>
                            <td>Город</td>
                            <td>Отправлено</td>
                            <td>Отправляется</td>
                            <td>Общее кол-во получателей</td>
                            <td>Успешных получателей</td>
                            <td>Активность</td>
                        </tr>
                        <tr v-for="notification in notifications">
                            <td>{{ notification.text }}</td>
                            <td>{{ notification.created_at | dateConvert }}</td>
                            <td>{{ notification.city }}</td>
                            <td>{{ notification.sent }}</td>
                            <td>{{ notification.queued }}</td>
                            <td>{{ notification.totalRecipients }}</td>
                            <td>{{ notification.successRecipients }}</td>
                            <td>{{ notification.activity ? 1 : 0 }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <pagination
                    :currentPage="currentPage"
                    :lastPage="lastPage"
                    :url="url"
                    :perPage="perPage"
                    :total="total"></pagination>


            <message-modal v-if="showModal">
                <div slot="header">
                    Уведомление пользователей
                </div>
                <div slot="body">
                    <div>
                        Вы действительно хотите уведомить {{usersCount}} пользователей из города - {{currentCity.city}}
                    </div>
                    <div class="error" v-if="error">
                        Вы не выбрали город или нет сообщения
                    </div>
                </div>
                <div slot="footer">
                    <button class="btn btn-success" @click="notify()">Отправить</button>
                    <button class="btn btn-danger" @click="closeModal()">Закрыть</button>
                </div>
            </message-modal>
        </div>
        <div class="col-md-12" v-if="isLoaded"></div>
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
        data() {
            return {
                text: '',
                isLoaded: false,
                cities: [],
                currentCityId: '',
                currentCity: {},
                error: false,
                usersCount: 0,
                notifications: [],
                checkedActivity: false,
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
            this.fetchData();
        },

        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
            url() {
                return `#/notify`;
            }
        },

        watch: {

        },

        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                axios.get('/admin/cities').then((response) => {
                    this.cities = response.data;
                    axios.get('/admin/notifications').then((response) => {
                        this.notifications = response.data.data;

                        this.currentPage = response.data.current_page;
                        this.perPage = response.data.per_page;
                        this.lastPage = response.data.last_page;
                        this.total = response.data.total;

                        this.$store.commit(LOADING_SUCCESS);
                        this.isLoaded = true;
                    });
                });
            },
            notify() {
                if (!this.currentCity.id || !this.text.length) {
                    this.$toastr.e("Вы не ввели сообщение или город")
                } else {
                    this.error = false;
                    axios.post('/admin/notify', { text: this.text, cityId: this.currentCity.id, activity: this.checkedActivity ? 1 : 0 }).then((response) => {
                        this.closeModal();
                        this.$toastr.s("Сообщения начитнают рассылаться");
                        this.text = '';
                        this.currentCity = { id: '', name: ''};
                        this.fetchData();
                    }).catch(error => this.$toastr.e("Произошла ошибка"));
                }

            },

            openModal() {
                this.$store.commit(MODAL_OPEN);

            },

            closeModal() {
                this.error = false;
                this.$store.commit(MODAL_CLOSE);
            },

            setUsersCount() {
                this.$store.commit(LOADING);
                const activity = this.checkedActivity ? 1 : 0;
                axios.get(`/admin/usersCount?cityId=${this.currentCity.id}&activity=${activity}`).then((response) => {
                    this.usersCount = response.data;
                    this.$store.commit(LOADING_SUCCESS);
                });
            }
        },

        filters: {
            dateConvert(value) {
                return moment(value).format('DD.MM.YYYY HH:mm:ss');
            },
        }


    };
</script>

<style>
    td {
        padding: 5px;
        border-bottom: 1px solid #b9b9b9ee;
    }

    .error {
        color: red;
    }
</style>
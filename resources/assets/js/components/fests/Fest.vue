<template>
    <div v-if="isLoaded">
        <div class="row">
            <div class="col-md-12">
                <h2>Редактирование фестиваля</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">

                <div class="form-group">
                    <label for="">Город:</label>
                    <input type="text" v-model="fest.name" class="form-control">
                </div>

                <div class="form-group">
                    <label for="">ID:</label>
                    <input type="text" v-model="fest.id" class="form-control">
                </div>

                <div class="form-group">
                    <label for="">Дата (год-месяц-день):</label>
                    <input type="text" v-model="fest.date" class="form-control">
                </div>

                <div class="form-group" v-if="fest.activities && fest.activities.length">
                    <p>Список доступных активностей:</p>
                    <ul>
                        <li v-for="activity in fest.activities">{{activity | upperCaseFirst}}<i @click="removeActivity(activity)" class="glyphicon glyphicon-remove"></i></li>
                    </ul>
                </div>

                <div class="form-inline">
                    <label for="">Список доступных активностей:</label>
                    <div class="form-group">
                        <div class="input-group">
                            <select name="" id="" class="form-control" v-model="chosenActivity">
                                <option value="">Выберите</option>
                                <option :value="activity" v-for="activity in defaultActivities">{{activity}}</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click="addActivity()">Добавить</button>
                </div>  

                <div class="form-group">
                    <button class="btn btn-primary" @click="edit">Сохранить</button>
                    <router-link :to="{ name: 'FestList' }">Назад</router-link>
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

    import { festMixin } from '../../mixins/fests';
    import { commonMixin } from '../../mixins/common';

    export default {
        mixins: [festMixin, commonMixin],
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                axios.get(`/api/fests/${this.$route.params.id}`).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.fest = Object.assign(this.fest, response.data);
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            },
            edit() {
                if (this.fest.name.length < 2 ||
                    this.fest.id.length < 1 ||
                    this.fest.date.length < 2) {
                    alert('Некорректно заполнены данные');
                    return;
                }
                this.$store.commit(LOADING);
                const data = {
                    id: this.fest.id,
                    name: this.fest.name,
                    date: this.fest.date,
                    activities: this.fest.activities
                };

                axios.post(`/api/fests/${this.$route.params.id}`, data).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.$router.push({ name: 'FestList' });
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            }
        }
    }
</script>

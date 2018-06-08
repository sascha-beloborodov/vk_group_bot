<template>
    <div v-if="isLoaded">
        <div class="row">
            <div class="col-md-12">
                <h2>Редактирование фото</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="">Значение:</label>
                    <input type="text" v-model="photo.name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Фестиваль:</label>
                    <select v-model="photo.fest.id" class="form-control">
                        <option :value="fest.id" v-for="fest in fests">{{fest.name.toUpperCase()}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <button сlass="btn btn-primary" @click="edit">Сохранить</button>
                    <router-link :to="{ name: 'PhotoList' }">Назад</router-link>
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

    export default {
        data () {
            return {
                isLoaded: false,
                photo: {
                    name: '',
                    fest: {
                        id: '',
                        name: ''
                    }
                },
                fests: []
            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                axios.get(`/admin/fests/all`).then((response) => {
                    this.isLoaded = true;
                    this.fests = response.data;

                    axios.get(`/admin/photo/${this.$route.params.id}`).then((response) => {

                        this.$store.commit(LOADING_SUCCESS);
                        this.isLoaded = true;
                        this.photo.name = response.data.name;
                        this.photo.fest.id = response.data.city_id;
                        this.photo.fest.name = response.data.city;
                    }).catch(error => { this.$store.commit(LOADING_SUCCESS); });

                }).catch(error => {
                    console.warn(error);
                    this.$store.commit(LOADING_SUCCESS);
                });
            },
            edit() {
                if (this.photo.name.length == 0 || !this.photo.fest.id) {
                    alert('Некорректно заполнены данные');
                    return;
                }
                
                this.$store.commit(LOADING);
                const data = { id: this.photo.fest.id, name: this.photo.name};
                data.city = this.fests.filter(val => val.id == this.photo.fest.id)[0]['name'];
                debugger;
                axios.post(`/admin/photos/${this.$route.params.id}`, data).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.$router.push({ name: 'PhotoList' });
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            }
        }
    }
</script>

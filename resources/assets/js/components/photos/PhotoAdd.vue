<template>
    <div v-if="isLoaded">
        <div class="row">
            <div class="col-md-12">
                <h2>Добавление фото</h2>
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
                    <button сlass="btn btn-primary" @click="create">Сохранить</button>
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
                    this.$store.commit(LOADING_SUCCESS);
                    this.isLoaded = true;
                    this.fests = response.data;
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            },
            create() {
                if (this.photo.name.length < 2 || !this.photo.fest.id) {
                    alert('Некорректно заполнены данные');
                    return;
                }
                this.$store.commit(LOADING);
                const data = { id: this.photo.fest.id, name: this.photo.name};
                data.city = this.fests.filter(val => val.id == this.photo.fest.id)[0]['name'];
                debugger;
                axios.put(`/admin/photos`, data).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.$router.push({ name: 'PhotoList' });
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });

            }
        }
    }
</script>

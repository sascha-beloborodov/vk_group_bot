<template>
    <div v-if="isLoaded">
        <div class="row">
            <div class="col-md-12">
                <h2>Добавление</h2>
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
                    <label for="">Дата:</label>
                    <input type="text" v-model="fest.date" class="form-control">
                </div>
                <div class="form-group">
                    <button сlass="btn btn-primary" @click="edit">Сохранить</button>
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

    export default {
        data () {
            return {
                isLoaded: false,
                fest: {
                    name: '',
                    data: '',
                    id: ''
                },
            }
        },
        created() {
            this.isLoaded = true;
        },
        methods: {
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
                    date: this.fest.date
                };

                axios.put(`/admin/fests`, data).then((response) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.$router.push({ name: 'FestList' });
                }).catch(error => { this.$store.commit(LOADING_SUCCESS); });
            }
        }
    }
</script>



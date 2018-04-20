<template>
    <div v-if="isLoaded">
        <div class="row">
            <div class="col-md-12">
                <h2>Добавление участников</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="">Текст уведомления:</label>
                    <textarea type="text" v-model="participant.text" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="">Ключевое слово для голосования (уникально):</label>
                    <input type="text" v-model="participant.key" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="">Продолжительность (в минутах, кратно 30):</label>
                    <input type="text" v-model="participant.duration" class="form-control">
                </div>

                <div class="form-group">
                    <label for="">Фестиваль:</label>
                    <select v-model="photo.participant.id" class="form-control">
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
    import { participantMixin } from '../../mixins/participants';


    export default {
        mixins: [participantMixin],
        data () {
            return {
                isLoaded: false,
                participant: {
                    text: null,
                    key: null,
                    duration: null,
                    participants: null,
                    started_at: null,
                    description: null,
                    fest: {
                        id: null,
                        name: null
                    }
                },
                nominees: null,
                currentNominee: {
                    name: null
                },
                fests: [],
                
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
                }).catch(error => {
                    console.warn(error);
                    this.$store.commit(LOADING_SUCCESS);
                });
            },
            
            edit() {
                if (this.photo.name.length < 2 || !this.photo.fest.id) {
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

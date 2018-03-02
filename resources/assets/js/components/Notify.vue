<template>
    <div class='row show-page-container'>
        <div class="row">
            <div class="col-md-8">
                <h2>Отправить сообщение всем пользователям</h2>
                <div class="form-group">
                    <label for="">Текст сообщения:</label>
                    <textarea class="form-control" name="" id="" cols="30" rows="10" v-model="text"></textarea>
                    <br>
                    <button class="btn btn-primary" @click="notify">Отправить</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">

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
    } from '../store/mutation-types';

    import moment from 'moment';
    require('toastr');

    export default {
        data() {
            return {
                text: ''
            }
        },

        components: {

        },

        created() {

        },

        computed: {

        },

        watch: {

        },

        methods: {
            notify() {
                axios.post('/admin/notify', { text: this.text }).then((response) => {
                    debugger;
                });
            }
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
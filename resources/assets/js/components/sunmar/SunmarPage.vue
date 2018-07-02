<template>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary btn-popup" @click="toggleModal" :diabled="false">Отправить сообщение участникам</button>
        </div>
        <router-view></router-view>
        <message-modal v-if="showModal">
            <div slot="header">
                Сообщение участникам
            </div>
            <div slot="body">
                <textarea class="form-control" v-model="message" cols="30" rows="10"></textarea>
            </div>
            <div slot="footer">
                <button class="btn btn-success" @click="send()">Отправить</button>
                <button class="btn btn-danger" @click="toggleModal()">Закрыть</button>
            </div>
        </message-modal>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE
    } from '../../store/mutation-types';
    import MessageModal from './../MessageModal';
    import { participantMixin } from '../../mixins/participants';

    export default {
        components: {
            messageModal: MessageModal,
        },
        data() {
            return {
                message: '',
            }
        },
        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
        },
        methods: {
            toggleModal() {
                this.$store.state.showModal ? this.$store.commit(MODAL_CLOSE) : this.$store.commit(MODAL_OPEN);
            },
            send() {
                if (!this.message.length) {
                    this.$toastr.e("Вы не ввели сообщение");
                } else if (this.message.length > 700) {
                    this.$toastr.e("Сообщение слишком длинное");
                } else {
                    this.$store.commit(LOADING);
                    this.error = false;
                    axios.post('/sunmar/message', { text: this.message, }).then((response) => {
                        this.closeModal();
                        this.$toastr.s("Сообщения начитнают рассылаться");
                        this.message = '';
                        this.$store.commit(LOADING_SUCCESS);
                    }).catch(error => this.$toastr.e("Произошла ошибка"));
                }
            },
        }
    }
</script>

<style>
    .modal-container {
        width: 70%!important;
    }
    .btn-popup {
        margin: 1% 10% 0 0%;
        float: right;
    }
</style>

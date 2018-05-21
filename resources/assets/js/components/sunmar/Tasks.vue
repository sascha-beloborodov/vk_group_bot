<template>
    <div>
        <h4>Список заданий</h4>
        <div class="col-md-9" v-if="isLoaded">
            <ul>
                <li v-for="(task, idx) in tasks">{{ task.name}}<br>
                    <button class="btn btn-primary" @click="openModal(task.num)">Запустить</button>
                    <button class="btn btn-success" @click="checkResults(task.num)">Проверить результаты</button>
                </li>
            </ul>
        </div>

        <transition name="modal" v-if="showModal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">
                        <div class="modal-header">
                            <slot name="header">
                                Запуск задания #1
                            </slot>
                        </div>

                        <div class="modal-body">
                            <slot name="body">
                                <textarea class="form-control" v-model="text" cols="30" rows="10"></textarea>
                            </slot>
                        </div>

                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="btn btn-success" @click="runTask()">Запустить</button>
                                <button class="btn btn-danger" @click="closeModal()">Закрыть</button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE
    } from '../../store/mutation-types';
    import { sunmar } from '../../mixins/sunmar';

    export default {
        mixins: [sunmar],
        data() {
            return {
                activeTask: {},
                tasks: [],
                isLoaded: false,
                text: '',
                num: 1
            };
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.$store.commit(LOADING);
                axios.get(`/admin/sunmar/tasks`).then((response) => {
                    for (let i = 0; i < this.tasksInfo.length; i++) {
                        let hasTask = false;
                        for (let j = 0; j < response.data; j++) {
                            if (this.tasksInfo[i].num == response.data[j].num) {
                                if (response.data[j].is_active == 1) {
                                    this.activeTask = {
                                        name: this.tasksInfo[i].name,
                                        num: this.tasksInfo[i].num,
                                        text: this.tasksInfo[i].text,
                                        created_at: response.data[j].created_at_utc,
                                        is_active: response.data[j].is_active
                                    };
                                }
                                hasTask = true;
                                this.tasks.push({
                                    name: this.tasksInfo[i].name,
                                    num: this.tasksInfo[i].num,
                                    text: this.tasksInfo[i].text,
                                    created_at: response.data[j].created_at_utc,
                                    is_active: response.data[j].is_active
                                });
                            }
                        }
                        if (hasTask)  {
                            continue;
                        }
                        this.tasks.push({
                            name: this.tasksInfo[i].name,
                            num: this.tasksInfo[i].num,
                            text: this.tasksInfo[i].text,
                            created_at: null,
                            is_active: 0
                        });
                    }
                    this.isLoaded = true;
                    
                    this.$store.commit(LOADING_SUCCESS);
                }).catch(error => {
                    console.warn(error);
                    this.$store.commit(LOADING_SUCCESS);
                });
            },
            runTask() {
                if (!this.text.length) {
                    this.$toastr.e('Нельзя отправить пустой текст');
                    return;
                }
                this.$store.commit(LOADING);
                axios.post(`/admin/sunmar/task/run/1`, {text: this.text, num: this.num}).then((response) => {
                    this.task = response.data;
                    closeModal();
                    this.$store.commit(LOADING_SUCCESS);
                }).catch(error => {
                    console.warn(error);
                    this.$store.commit(LOADING_SUCCESS);
                });
            },
            checkResults(num) {
                
                this.$store.commit(LOADING);
                axios.post(`/admin/sunmar/task/check/${num}`, {num: num}).then((response) => {
                    console.log(response);
                    this.$store.commit(LOADING_SUCCESS);
                }).catch(error => {
                    console.warn(error);
                    this.$store.commit(LOADING_SUCCESS);
                });
            },
            openModal(num) {
                this.num = num;
                debugger;
                this.text = this.tasks.filter((val, idx) => {
                    if (val.num == num) {
                        return true;
                    }
                })[0].text;
                this.$store.commit(MODAL_OPEN);
            },
            closeModal() {
                this.$store.commit(MODAL_CLOSE);
            }
        },
        computed: {
            showModal() {
                return this.$store.state.showModal;
            },
        },
    }
</script>

<style>
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 60%;
        margin: 0px auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;
        color: #42b983;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-default-button {
        float: right;
    }

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>
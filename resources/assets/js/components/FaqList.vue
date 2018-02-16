<template>
    <div class='row show-page-container'>
        <div class="col-md-8" v-if="isLoaded">
            <h1>My FAQ's</h1>
            <h4 v-if='edit'>Редактирование</h4>
            <h4 v-if='!edit'>Новый вопрос-ответ</h4>
            <form action="#" @submit.prevent="createOrUpdateFAQ(faq._id)">
                <div class="form-group">
                    <label for="">Вопрос:</label>
                    <input v-model="faq.question" type="text" name="question" class="form-control" autofocus><br>
                </div>
                <div class="form-group">
                    <label for="">Ответ:</label>
                    <textarea v-model="faq.answer" name="answer" class="form-control"></textarea><br>
                </div>
                <div class="form-group">
                    <label for="">Категория:</label>
                    <input v-model="faq.category" name="answer" class="form-control"><br>
                </div>
                <div class="form-group">
                    <p v-if='faq.keywords.length === 0'>Нет ключевых слов</p>
                    <p>Ключевые слова</p>
                    <ol>
                        <li class="edited-keyword-list-item" v-for="(kword, index) in faq.keywords">{{ kword }}<i @click="removeKeyword(index)" class="glyphicon glyphicon-remove"></i></li>
                    </ol>
                </div>

                <div class="form-group">
                    <label for="">Новое ключевое слово:</label>
                    <input v-model="keyword" type="text" name="answer" class="form-control">
                    <br>
                    <span @click="addKeyword(keyword)" class="btn btn-success">Добавить</span>
                </div>

                <div class="form-group">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary" v-if="edit">Сохранить</button>
                        <button class="btn btn-default" v-if="edit" @click="clearEdited()">Отмена</button>
                        <button type="submit" class="btn btn-primary" v-if="!edit">Сохранить и добавить</button>
                    </span>
                </div>
            </form>
            <h4>All FAQ</h4>
            <pagination
                    :currentPage="currentPage"
                    :lastPage="lastPage"
                    :url="url"
                    :perPage="perPage"
                    :total="total"></pagination>
            <ul class="list-group">
                <li v-if='list.length === 0'>There are no faqs yet!</li>
                <li class="list-group-item" v-for="(faqItem, index) in list">
                    <b>Вопрос:</b><br>
                    <p>{{ faqItem.question }}</p>
                    <b>Ответ:</b><br>
                    <p>{{ faqItem.answer }}</p>
                    <b>Ключевые слова:</b>
                    <ul>
                        <li v-for="(keyw, index) in faqItem.keywords">
                            <span>{{keyw}}</span>
                        </li>
                    </ul>
                    <button @click="deleteFAQ(faqItem._id.$oid)" class="btn btn-danger btn-xs pull-right">Delete</button>&nbsp;&nbsp;
                    <button @click="editFAQ(faqItem._id.$oid)" class="btn primary btn-xs pull-right">Edit</button>
                </li>
            </ul>
            <pagination
                    :currentPage="currentPage"
                    :lastPage="lastPage"
                    :url="url"
                    :perPage="perPage"
                    :total="total"></pagination>
        </div>

    </div>
</template>
<script>
    import {
        LOADING_SUCCESS,
        LOADING
    } from '../store/mutation-types'
    import Pagination from './Pagination';

    export default {
        data() {
            return {
                list: [],
                faq: {
                    id: null,
                    answer: '',
                    category: '',
                    question: '',
                    keywords: [],
                },
                keyword: '',
                edit: false,
                currentPage: 1,
                perPage: null,
                lastPage: null,
                total: null,
                chosenPage: null,

                isLoaded: false,
            };
        },

        created() {
            this.fetchFAQList();
        },

        components: {
            pagination: Pagination
        },

        watch: {
            $route() {
                this.fetchFAQList();
            }
        },

        computed: {
            url() {
                return `#/faq`;
            }
        },

        methods: {
            addKeyword(keyw) {
                if (keyw && keyw.length > 3) {
                    this.faq.keywords.push(keyw);
                    this.keyword = '';
                }
            },

            fetchFAQList() {
                this.$store.commit(LOADING);
                const pageParams = this.chosenPage ? `?page=${this.chosenPage}` : ``;
                const query = this.$route.query || { page: 1 };
                if (query.page === undefined) {
                    query.page = 1;
                }
                axios.get(`/admin/faq-list?page=${query.page}`).then((res) => {
                    this.$store.commit(LOADING_SUCCESS);
                    this.list = res.data.data;

                    this.currentPage = res.data.current_page;
                    this.perPage = res.data.per_page;
                    this.lastPage = res.data.last_page;
                    this.total = res.data.total;
                    this.isLoaded = true;
                });
            },

            createOrUpdateFAQ(id) {
                if (!this.edit) {
                    axios.post(`/admin/faq`, this.faq)
                        .then((res) => {
                            this.clearData();
                            this.fetchFAQList();
                        })
                        .catch((err) => console.error(err));
                } else {
                    axios.put(`/admin/faq/${id.$oid}`, this.faq)
                        .then((res) => {
                            this.clearData();
                            this.fetchFAQList();
                        })
                        .catch((err) => console.error(err));
                }
            },

            editFAQ(id) {
                axios.get(`/admin/faq/${id}`).then((res) => {
                    this.faq = res.data.data;
                    this.edit = true;
                });
            },

            deleteFAQ(id) {
                axios.delete(`/admin/faq/${id}`)
                    .then((res) => {
                        this.fetchFAQList()
                    })
                    .catch((err) => console.error(err));
            },

            removeKeyword(index) {
                this.faq.keywords = this.faq.keywords.filter(function(value, idx) {
                    return index != idx;
                });
            },

            clearEdited() {
                this.clearData();
            },

            clearData() {
                this.edit = false;
                this.faq.answer = '';
                this.faq.category = '';
                this.faq.question = '';
                this.faq.keywords = [];
            },
        }
    }
</script>

<style>
    .glyphicon-remove {
        float: right;
        cursor: pointer;
    }

    .edited-keyword-list-item:hover {
        background: #8C8C8C;
    }

    .list-group-item {
        overflow: hidden;
    }
</style>
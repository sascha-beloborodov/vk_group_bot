<template>
    <div class='row'>
        <div class="col-md-8">
            <h1>My FAQ's</h1>
            <h4>New FAQ</h4>
            <form action="#" @submit.prevent="createTask()">
                <div class="form-group">
                    <label for="">Вопрос:</label>
                    <input v-model="faq.question" type="text" name="question" class="form-control" autofocus><br>
                </div>
                <div class="form-group">
                    <label for="">Ответ:</label>
                    <input v-model="faq.answer" type="text" name="answer" class="form-control"><br>
                </div>
                <div class="form-group">
                    <p>Ключевые слова</p>
                    <ol>
                        <li v-for="(kword, index) in faq.keywords">{{ kword }}</li>
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
                        <button type="submit" class="btn btn-primary">Save QA</button>
                    </span>
                </div>
            </form>
            <h4>All FAQ</h4>
            <ul class="list-group">
                <li v-if='list.length === 0'>There are no faqs yet!</li>
                <li class="list-group-item" v-for="(faqItem, index) in list">
                    {{ faqItem.answer }} - {{ faqItem.question }} - <br>
                    <ul>
                        <li v-for="(keyw, index) in faqItem.keywords">
                            <span>{{keyw}}</span>
                        </li>
                    </ul>
                    <button @click="deleteTask(faqItem._id.$oid)" class="btn btn-danger btn-xs pull-right">Delete</button>
                    <button @click="editTask(faqItem._id.$oid)" class="btn primary btn-xs pull-right">Edit</button>
                </li>
            </ul>
        </div>

    </div>
</template>
<script>
    export default {
        data() {
            return {
                list: [],
                faq: {
                    answer: '',
                    question: '',
                    keywords: [],
                },
                keyword: '',
                edit: false
            };
        },

        created() {
            this.fetchTaskList();
        },

        methods: {
            addKeyword(keyw) {
                this.faq.keywords.push(keyw);
                this.keyword = '';
            },

            fetchTaskList() {
                axios.get('faq-list').then((res) => {
                    this.list = res.data;
                });
            },

            createTask() {
                if (!this.edit) {

                }
                axios.post('faq', this.faq)
                    .then((res) => {
                        this.faq.answer = '';
                        this.faq.question = '';
                        this.faq.keywords = [];
                        this.edit = false;
                        this.fetchTaskList();
                    })
                    .catch((err) => console.error(err));
            },

            editTask(id) {
                axios.get(`faq/${id}`).then((res) => {
                    this.faq = res.data.data;
                    this.edit = true;
                });
            },

            deleteTask(id) {
                axios.delete('api/tasks/' + id)
                    .then((res) => {
                        this.fetchTaskList()
                    })
                    .catch((err) => console.error(err));
            },
        }
    }
</script>

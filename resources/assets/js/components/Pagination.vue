<template>
    <nav aria-label="Page navigation" v-if="hasPagination">
        <ul class="pagination">
            <li>
                <a :href="makeLink(1)" v-bind:class="{ active: 1 == currentPage }">Начало</a>
            </li>
            <li v-for="(pageNum, index) in visiblePages" v-bind:class="{ active: pageNum == currentPage }">
                <a :href="makeLink(pageNum)">{{pageNum}}</a>&nbsp;&nbsp;
            </li>
            <li>
                <a :href="makeLink(lastPage)" v-bind:class="{ active: lastPage == currentPage }">Конец</a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        data () {
            return {
                visiblePages: [],
                hasPagination: false,
            }
        },

        props: ["currentPage", "lastPage", "perPage", "total", "url"],

        created() {
            if (this.total > this.perPage) {
                this.hasPagination = true;
                for (let i = 1; i <= this.lastPage; i++) {
                    if (this.currentPage - 2 > 0 && this.currentPage - 2 == i) {
                        this.visiblePages.push(this.currentPage - 2);
                    }
                    if (this.currentPage - 1 > 0 && this.currentPage - 1 == i) {
                        this.visiblePages.push(this.currentPage - 1);
                    }
                    if (this.currentPage > 0 && this.currentPage == i) {
                        this.visiblePages.push(this.currentPage);
                    }
                    if (this.currentPage + 1 > 0 && this.currentPage + 1 == i) {
                        this.visiblePages.push(this.currentPage + 1);
                    }
                    if (this.currentPage + 2 > 0 && this.currentPage + 2 == i) {
                        this.visiblePages.push(this.currentPage + 2);
                    }
                }
            } else {
                this.hasPagination = false;
            }
        },

        methods: {
            makeLink(page) {
                return `${this.url}?page=${page}`;
            }
        }

    }
</script>

<style>
    a.active {
        color: #000;
    }
    .pagination>li {
        display: inline-flex;
    }
</style>
<template>
    <div>
        <a href="#" v-on:click.prevent="goToPage(1)" v-bind:class="{ active: 1 == currentPage }">начало</a>&nbsp;&nbsp;
        <span>
        <a href="#" v-on:click.prevent="goToPage(pageNum)" v-for="(pageNum, index) in visiblePages" v-bind:class="{ active: pageNum == currentPage }">{{pageNum}}</a>&nbsp;&nbsp;
    </span>
        <a href="#" v-on:click.prevent="goToPage(lastPage)" v-bind:class="{ active: lastPage == currentPage }">конец</a>
    </div>
</template>

<script>
    export default {
        data () {
            return {

            }
        },

        props: ["currentPage", "lastPage", "goToPage", "visiblePages"]

    }
</script>

<style>
    a.active {
        color: #000;
    }
</style>
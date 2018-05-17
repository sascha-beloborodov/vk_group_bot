<template>
    <div class="ui secondary vertical pointing fluid menu">
        <router-link to="/messages-list" v-bind:class="{ active: $route.path == '/messages-list', item: true }">Сообщения</router-link>
        <router-link to="/faq" v-bind:class="{ active: $route.path == '/faq', item: true }">Вопросы FAQ</router-link>
        <router-link to="/users" v-bind:class="{ active: $route.path == '/users', item: true }">Пользователи</router-link>
        <router-link to="/notify" v-bind:class="{ active: $route.path == '/notify', item: true }">Нотификация пользователей</router-link>
        <router-link to="/photos" v-bind:class="{ active: $route.path == '/photos', item: true }">Фотоконкурс</router-link>
        <router-link to="/fests" v-bind:class="{ active: $route.path == '/fests', item: true }">Фестивали</router-link>
        <router-link to="/participants" v-bind:class="{ active: $route.path == '/participants', item: true }">Участники голосования</router-link>
    </div>
</template>
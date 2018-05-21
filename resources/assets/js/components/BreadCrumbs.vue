<template>
  <div class="breadcrumbs">
    <ul class="breadcrumbs__list">
      <li><router-link to="/">Главная</router-link></li>
      <li v-for="(route, index) in $route.matched">
        <router-link :to="{ name: route.name, params: {} }">
          {{
            !route.meta.dynamic
            ? route.meta.breadcrumb: route.meta.breadcrumb($store.state.activeUser)
          }}
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script>
  import Vue from 'vue';

  import {
    SET_BREADCRUMB,
  } from '../store/mutation-types';

  export default {
    data() {
      return {
        value: '',
        formattedValue: ''
      }
    }
  }
</script>

<style scoped>
  .breadcrumbs__list {
    padding: 0;
    padding-left: 10px;
    margin: 0;
    margin-top: 20px;
    display: flex;
  }

  .breadcrumbs__list li {
    position: relative;
    margin-right: 20px;
  }

  .breadcrumbs__list li:after {
    content: '/';
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    color: #000;
  }

  .breadcrumbs__list li:first-of-type:before {
    content: '/';
    position: absolute;
    left: -10px;
    top: 50%;
    transform: translateY(-50%);
    color: #000;
  }

  .breadcrumbs__list li:last-of-type:after {
    display: none;
  }
</style>

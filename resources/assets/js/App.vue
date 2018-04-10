<template>
    <div>


    <aside class="main-sidebar" id="sidebar-wrapper">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar Menu -->

            <ul class="sidebar-menu">
                <li v-bind:class="{ active: $route.path == '/messages-list' }">
                    <router-link to="/messages-list"><i class="fa fa-home"></i>Сообщения</router-link>
                </li>
                <li v-bind:class="{ active: $route.path == '/faq' }">
                    <router-link to="/faq"><i class="fa fa-home"></i>Вопросы FAQ</router-link>
                </li>
                <li v-bind:class="{ active: $route.path == '/users' }">
                    <router-link to="/users"><i class="fa fa-home"></i>Пользователи</router-link>
                </li>
                <li v-bind:class="{ active: $route.path == '/notify' }">
                    <router-link to="/notify"><i class="fa fa-home"></i>Нотификация пользователей</router-link>
                </li>

                <li v-bind:class="{ active: $route.path == '/photos' }">
                    <router-link to="/photos"><i class="fa fa-home"></i>Фотоконкурс</router-link>
                </li>

                <li v-bind:class="{ active: $route.path == '/fests' }">
                    <router-link to="/fests"><i class="fa fa-home"></i>Фестивали</router-link>
                </li>

            </ul>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>


        <nav>
            <div class="container">
              <bread-crumbs></bread-crumbs>
                <!-- <ul class="nav nav-tabs">
                    <li v-bind:class="{ active: $route.path == '/messages-list' }">
                        <router-link to="/messages-list"><i class="fa fa-home"></i>Сообщения</router-link>
                    </li>
                    <li v-bind:class="{ active: $route.path == '/faq' }">
                        <router-link to="/faq"><i class="fa fa-home"></i>Вопросы FAQ</router-link>
                    </li>
                    <li v-bind:class="{ active: $route.path == '/users' }">
                        <router-link to="/users"><i class="fa fa-home"></i>Пользователи</router-link>
                    </li>
                    <li v-bind:class="{ active: $route.path == '/notify' }">
                        <router-link to="/notify"><i class="fa fa-home"></i>Нотификация пользователей</router-link>
                    </li>
                </ul> -->
            </div>
        </nav>

        <div class="container">
            <div class="row">
                <router-view></router-view>
            </div>
        </div>


        <div class="overlay" v-show="showLoader">
            <div class="loading-spinner">
                <div class="dot dotOne"></div>
                <div class="dot dotTwo"></div>
                <div class="dot dotThree"></div>
            </div>
        </div>
    </div>
</template>

<script>


    import {
        LOADING_SUCCESS,
        LOADING
    } from './store/mutation-types';

    import BreadCrumbs from './components/BreadCrumbs';

    export default {
        name: 'app',
        data() {
            return {

//                cartItems: this.$store.state.cart
            }
        },
        components: {
          'bread-crumbs': BreadCrumbs
        },
//        created () {
//            this.$store.subscribe((mutation) => {
//                if (mutation.payload) {
//                    switch (mutation.type) {
//                        case ERROR_MSG:
//                            toastr.error(mutation.payload.content, mutation.payload.title)
//                            break
//                        case ADD_PRODUCT_SUCCESS:
//                            toastr.success('Product created.', 'Success!')
//                            break
//                        case UPDATE_PRODUCT_SUCCESS:
//                            toastr.success('Product updated.', 'Success!')
//                            break
//                        case REMOVE_PRODUCT_SUCCESS:
//                            toastr.warning('Product deleted.', 'Deleted!')
//                            break
//                    }
//                }
//            })
//        },
        computed: {
//            cartItemsCount () {
//                return this.cartItems.length
//            },
            showLoader() {
                return this.$store.state.showLoader;
            }
        }
    }
</script>

<style>
    .overlay {
        background: rgba(255, 255, 255, 0.6);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translateX(-50%) translateY(-50%);
        -moz-transform: translateX(-50%) translateY(-50%);
        transform: translateX(-50%) translateY(-50%);
    }

    .dot {
        background: black;
        border-radius: 100%;
        color: white;
        height: 8px;
        line-height: 8px;
        text-align: center;
        width: 8px;
    }

    .dotOne {
        -webkit-animation: dotOneKeyframes 5s ease infinite;
        background: #F9E610;
        position: absolute;
        top: 0;
        left: 0;
    }

    .dotTwo {
        -webkit-animation: dotTwoKeyframes 5s ease 0.4166666666666s infinite;
        background: #EFDB06;
        position: absolute;
        top: 0;
        left: 14px;
    }

    .dotThree {
        -webkit-animation: dotThreeKeyframes 5s ease 0.83333333333s infinite;
        background: #DBC906;
        position: absolute;
        top: 14px;
        left: 14px;
    }

    @-webkit-keyframes dotOneKeyframes {
        0% {
            top: 0;
            left: 0;
        }
        8.3333333333% {
            top: 14px;
            left: 0;
        }
        16.6666666666% {

        }
        25% {
            top: 14px;
            left: 0;
        }
        33.3333333333% {
            top: 14px;
            left: 14px;
        }
        41.6666666666% {

        }
        50% {
            top: 14px;
            left: 14px;
        }
        58.3333333333% {
            top: 0;
            left: 14px;
        }
        66.6666666666% {

        }
        75% {
            top: 0;
            left: 14px;
        }
        83.3333333333% {
            top: 0;
            left: 0;
        }
        91.6666666666% {

        }
        100% {

        }
    }

    @-webkit-keyframes dotTwoKeyframes {
        0% {
            top: 0;
            left: 14px;
        }
        8.3333333333% {
            top: 0;
            left: 0;
        }
        16.6666666666% {

        }
        25% {
            top: 0;
            left: 0;
        }
        33.3333333333% {
            top: 14px;
            left: 0;
        }
        41.6666666666% {

        }
        50% {
            top: 14px;
            left: 0;
        }
        58.3333333333% {
            top: 14px;
            left: 14px;
        }
        66.6666666666% {

        }
        75% {
            top: 14px;
            left: 14px;
        }
        83.3333333333% {
            top: 0;
            left: 14px;
        }
        91.6666666666% {

        }
        100% {

        }
    }

    @-webkit-keyframes dotThreeKeyframes {
        0% {
            top: 14px;
            left: 14px;
        }
        8.3333333333% {
            top: 0;
            left: 14px;
        }
        16.6666666666% {

        }
        25% {
            top: 0;
            left: 14px;
        }
        33.3333333333% {
            top: 0;
            left: 0;
        }
        41.6666666666% {

        }
        50% {
            top: 0;
            left: 0;
        }
        58.3333333333% {
            top: 14px;
            left: 0;
        }
        66.6666666666% {

        }
        75% {
            top: 14px;
            left: 0;
        }
        83.3333333333% {
            top: 14px;
            left: 14px;
        }
        91.6666666666% {

        }
        100% {

        }
    }
</style>

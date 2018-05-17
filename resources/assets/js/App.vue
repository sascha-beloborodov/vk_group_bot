<template>
    <div class="ui "> <!-- class = container -->
        <div class="ui secondary menu">
            <div class="header item">KFC</div>
            <div class="right menu">
                <div class="item">
                    <div class="ui icon input">
                        <input type="text" placeholder="Search...">
                        <i class="search link icon"></i>
                    </div>
                </div>
                <a class="ui item">
                    Logout
                </a>
            </div>
        </div>
        <div class="ui divider"></div>
        <div class="ui grid">
            <div class="four wide column">
                <menu-items v-if="isLoggedIn"></menu-items>
            </div>
            <div class="twelve wide column">
                <bread-crumbs v-if="isLoggedIn"></bread-crumbs>
                <div class="ui container">
                    <router-view></router-view>
                </div>
            </div>
        </div>
        <sui-segment v-if="showLoader">
            <sui-dimmer active inverted>
                <sui-loader content="Loading..." />
            </sui-dimmer>
        </sui-segment>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING
    } from './store/mutation-types';
    import Menu from './components/common/Menu';
    import BreadCrumbs from './components/common/BreadCrumbs';

    export default {
        name: 'app',
        data() {
            return {

                //                cartItems: this.$store.state.cart
            }
        },
        components: {
            'bread-crumbs': BreadCrumbs,
            'menu-items': Menu
        },
        //    created () {
        //        this.$store.subscribe((mutation) => {
        //            if (mutation.payload) {
        //                switch (mutation.type) {
        //                    case ERROR_MSG:
        //                        toastr.error(mutation.payload.content, mutation.payload.title)
        //                        break
        //                    case ADD_PRODUCT_SUCCESS:
        //                        toastr.success('Product created.', 'Success!')
        //                        break
        //                    case UPDATE_PRODUCT_SUCCESS:
        //                        toastr.success('Product updated.', 'Success!')
        //                        break
        //                    case REMOVE_PRODUCT_SUCCESS:
        //                        toastr.warning('Product deleted.', 'Deleted!')
        //                        break
        //                }
        //            }
        //        })
        //    },
        computed: {
            //            cartItemsCount () {
            //                return this.cartItems.length
            //            },
            isLoggedIn() {
                return this.$store.state.isLoggedIn;
            },
            showLoader() {
                return this.$store.state.showLoader;
            }
        },
        methods: {

        }
    }
</script>

<style>
</style>
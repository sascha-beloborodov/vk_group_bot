<template>
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal image header">
                <div class="content">
                    Вход
                </div>
            </h2>
            <sui-form v-on:submit.prevent="submitLogin">
                <sui-form-field>
                    <label>Email</label>
                    <input placeholder="Email" required autofocus v-model="email">
                </sui-form-field>
                <sui-form-field>
                    <label>Пароль</label>
                    <input placeholder="Password" type="password" required v-model="password">
                </sui-form-field>
                <sui-button v-if="!loading" type="submit" class="ui fluid large teal submit button">Войти</sui-button>
                <sui-button loading v-if="loading" type="submit" class="ui fluid large teal submit button">Войти</sui-button>
            </sui-form>
        </div>
    </div>
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        LOGIN_USER
    } from '../../store/mutation-types';

    export default {
        data() {
            return {
                email: '',
                password: '',
                loading: false
            }
        },
        methods: {

            submitLogin() {
                this.loading = true;
                axios.post('/api/auth/login', {
                    email: this.email,
                    password: this.password
                }).then(response => {
                    // login user, store the token and redirect to dashboard
                    this.$store.commit(LOGIN_USER);
                    debugger;
                    localStorage.setItem('token', response.data.access_token);
                    this.$router.push({
                        name: 'Dashboard'
                    })
                    this.loading = false;
                    this.$toastr.s("Вы успешно авторизованы");
                }).catch(error => {
                    this.$toastr.e("Неверный логин или пароль");
                    console.warn(error);
                    this.loginError = true;
                    this.loading = false;
                });
            }
        }
    }
</script>

<style scoped>
    body {
        background-color: #DADADA;
        margin: 0px;
        padding: 0px;
        overflow-x: hidden;
        min-width: 320px;
        background: #FFFFFF;
        font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;
        font-size: 14px;
        line-height: 1.4285em;
        color: rgba(0, 0, 0, 0.87);
        font-smoothing: antialiased;
    }

    .column {
        max-width: 450px;
    }
</style>
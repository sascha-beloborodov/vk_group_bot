<template>

</template>

<script>
    import {
        LOGOUT_USER
    } from '../../store/mutation-types';

    export default {
        mounted () {
            localStorage.removeItem('token')
            this.$store.commit(LOGOUT_USER)
            this.$router.push({ path: '/auth/login' })
        }
    }
</script>
<template>
    <div>
        <div>Здесь информация про текущий </div>
        <router-view></router-view>
    </div>
    
</template>

<script>
    import {
        LOADING_SUCCESS,
        LOADING,
        MODAL_OPEN,
        MODAL_CLOSE
    } from '../../store/mutation-types';
    import { participantMixin } from '../../mixins/participants';

    export default {
        
    }
</script>


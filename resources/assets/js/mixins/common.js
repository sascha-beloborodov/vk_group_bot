export const commonMixin = {
    data() {
        return {
            defaultActivities: ['автограф-сессия', 'квест', 'воркаут', 'скимбординг']
        }
    },
    filters: {
        /**
         * 
         * @param {*string} value 
         */
        upperCaseFirst(value) {
            if (!value.length) return '';
            return value[0].toUpperCase() + value.slice(1, value.length);
        },
    }
};



export const commonMixin = {
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



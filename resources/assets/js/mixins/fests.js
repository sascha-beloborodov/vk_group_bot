export const festMixin = {
    data () {
        return {
            chosenActivity: '',
            isLoaded: false,
            fest: {
                name: '',
                data: '',
                id: '',
                activities: []
            },
        };
    },
    methods: {
        addActivity() {
            if (!this.chosenActivity || !!~this.fest.activities.indexOf(this.chosenActivity)) {
                return;
            }
            this.fest.activities.push(this.chosenActivity);
            this.chosenActivity = '';
        },
        removeActivity(activity) {
            this.fest.activities = this.fest.activities.filter((value, idx) => {
                return activity != value;
            });
        }
    }
};

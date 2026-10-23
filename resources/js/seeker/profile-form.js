export default function (Alpine) {
    Alpine.data('profileForm', () => ({
        yearLevel: '',

        init() {
            const config = JSON.parse(this.$el.dataset.config || '{}');

            this.yearLevel = config.yearLevel || '';
        },

        get isStudent() {
            return this.yearLevel !== '' && this.yearLevel !== 'none';
        },
    }));
}

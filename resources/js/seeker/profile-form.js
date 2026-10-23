export default function (Alpine) {
    Alpine.data('profileForm', () => ({
        region: '',
        province: '',
        yearLevel: '',
        regions: {},

        init() {
            const config = JSON.parse(this.$el.dataset.config || '{}');

            this.region = config.region || '';
            this.province = config.province || '';
            this.yearLevel = config.yearLevel || '';
            this.regions = config.regions || {};
        },

        get provinces() {
            return this.regions[this.region]?.provinces || [];
        },

        regionChanged() {
            this.province = '';
        },

        get isStudent() {
            return this.yearLevel !== '' && this.yearLevel !== 'none';
        },
    }));
}

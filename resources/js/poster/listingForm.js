// The listing form: shows the right "Who can apply" fields for each type,
// the region and province pickers, the extra-details picker and the apply mode.
// Used with: <form x-data="listingForm" data-config="{...json...}">
export default function (Alpine) {
    Alpine.data('listingForm', () => ({
        type: 'scholarship',
        applyMode: 'info',
        region: '',
        province: '',
        targeting: {},
        regions: {},
        detailFields: {},
        detailValues: {},
        chosenDetails: [],

        init() {
            const config = JSON.parse(this.$el.dataset.config || '{}');

            this.type = config.type || 'scholarship';
            this.applyMode = config.applyMode || 'info';
            this.region = config.region || '';
            this.province = config.province || '';
            this.targeting = config.targeting || {};
            this.regions = config.regions || {};
            this.detailFields = config.detailFields || {};
            this.detailValues = { ...(config.details || {}) };

            // Details that already have a value start as "added"
            this.chosenDetails = Object.keys(this.detailValues).filter(
                (key) => String(this.detailValues[key] ?? '').trim() !== '',
            );
        },

        // Does the current listing type use this "Who can apply" field?
        shows(field) {
            return (this.targeting[this.type] || []).includes(field);
        },

        get provinces() {
            return this.regions[this.region]?.provinces || [];
        },

        regionChanged() {
            this.province = '';
        },

        // Extra details
        get activeDetails() {
            return this.detailFields[this.type] || [];
        },

        get chosenDetailFields() {
            return this.activeDetails.filter((field) => this.chosenDetails.includes(field.key));
        },

        hasDetail(key) {
            return this.chosenDetails.includes(key);
        },

        toggleDetail(key) {
            this.chosenDetails = this.hasDetail(key)
                ? this.chosenDetails.filter((k) => k !== key)
                : [...this.chosenDetails, key];
        },
    }));
}

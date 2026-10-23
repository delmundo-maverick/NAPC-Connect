// The listing form: shows the right "Who can apply" fields for each type,
// the extra-details picker and the apply mode.
// Region, province and city are handled by the shared location picker.
// Used with: <form x-data="listingForm" data-config="{...json...}">
export default function (Alpine) {
    Alpine.data('listingForm', () => ({
        type: 'scholarship',
        applyMode: 'info',
        targeting: {},
        detailFields: {},
        detailValues: {},
        chosenDetails: [],

        init() {
            const config = JSON.parse(this.$el.dataset.config || '{}');

            this.type = config.type || 'scholarship';
            this.applyMode = config.applyMode || 'info';
            this.targeting = config.targeting || {};
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

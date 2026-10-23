export default function (Alpine) {
    Alpine.data('searchForm', () => ({
        type: '',

        setType(value) {
            this.type = value;
        },
        chipClass(value) {
            return this.type === value ? 'chip-active' : 'chip';
        },
    }));
}

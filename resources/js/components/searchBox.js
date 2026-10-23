// Usage: <form x-data="searchBox" data-initial="current search text"> ... </form>
export default function (Alpine) {
    Alpine.data('searchBox', () => ({
        value: '',

        init() {
            this.value = this.$el.dataset.initial || '';
        },

        get hasValue() {
            return this.value.trim() !== '';
        },

        clear() {
            this.value = '';
            this.$nextTick(() => this.$root.requestSubmit());
        },
    }));
}

// Type a value and press Enter or comma to turn it into a tag.
// Used with: <div x-data="tagInput" data-name="courses" data-initial='["BSIT"]'>
export default function (Alpine) {
    Alpine.data('tagInput', () => ({
        tags: [],
        draft: '',
        name: 'tags',
        max: 15,

        init() {
            this.name = this.$el.dataset.name || 'tags';
            this.tags = JSON.parse(this.$el.dataset.initial || '[]');
        },

        add() {
            this.draft
                .split(',')
                .map((tag) => tag.trim())
                .filter(Boolean)
                .forEach((tag) => {
                    const exists = this.tags.some((t) => t.toLowerCase() === tag.toLowerCase());

                    if (!exists && this.tags.length < this.max) {
                        this.tags.push(tag.slice(0, 80));
                    }
                });

            this.draft = '';
        },

        remove(index) {
            this.tags.splice(index, 1);
        },

        // Backspace on an empty box removes the last tag
        backspace() {
            if (this.draft === '' && this.tags.length > 0) {
                this.tags.pop();
            }
        },
    }));
}

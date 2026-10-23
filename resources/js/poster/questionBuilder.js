// Lets an organization build its own application form.
// Used with: <div x-data="questionBuilder" data-initial="[...]" data-types="{...}" data-choice="[...]">
export default function (Alpine) {
    Alpine.data('questionBuilder', () => ({
        questions: [],
        types: {},
        choiceTypes: [],

        presets: [
            { label: 'Why are you applying?', type: 'textarea' },
            { label: 'Contact number', type: 'text' },
            { label: 'School or company ID number', type: 'text' },
            { label: 'Date of birth', type: 'date' },
        ],

        init() {
            const data = this.$el.dataset;

            this.types = JSON.parse(data.types || '{}');
            this.choiceTypes = JSON.parse(data.choice || '[]');
            this.questions = JSON.parse(data.initial || '[]').map((q) => this.make(q));

            // Start a new form with one empty question
            if (this.questions.length === 0) {
                this.add();
            }
        },

        make(q = {}) {
            return {
                uid: Math.random().toString(36).slice(2, 10), // only for the screen, never saved
                key: q.key || null,                           // saved key, created by the server for new questions
                label: q.label || '',
                type: q.type || 'text',
                required: Boolean(q.required),
                optionsText: q.optionsText || '',
            };
        },

        isChoice(question) {
            return this.choiceTypes.includes(question.type);
        },

        add(preset = {}) {
            this.questions.push(this.make(preset));
        },

        remove(index) {
            this.questions.splice(index, 1);
        },

        move(index, direction) {
            const target = index + direction;

            if (target < 0 || target >= this.questions.length) return;

            [this.questions[index], this.questions[target]] = [this.questions[target], this.questions[index]];
        },
    }));
}

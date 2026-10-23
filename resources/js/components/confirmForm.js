// Usage:
//   <form x-data="confirmForm" data-confirm="Are you sure?" @submit="submit($event)">
// - data-confirm is optional. Without it the form just locks after the first click.
// - Once submitted, the buttons are disabled so it can't be sent twice.
export default function (Alpine) {
    Alpine.data('confirmForm', () => ({
        busy: false,

        init() {
            // If the user comes back with the browser's Back button, unlock the form again
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) this.unlock();
            });
        },

        submit(event) {
            const message = this.$el.dataset.confirm;

            if (message && !window.confirm(message)) {
                event.preventDefault();
                return;
            }

            if (this.busy) {
                event.preventDefault();
                return;
            }

            this.busy = true;
            // Wait a tick so the browser still sends the form before the button is disabled
            setTimeout(() => this.setButtons(true), 0);
        },

        unlock() {
            this.busy = false;
            this.setButtons(false);
        },

        setButtons(disabled) {
            this.$el.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = disabled;
            });
        },
    }));
}

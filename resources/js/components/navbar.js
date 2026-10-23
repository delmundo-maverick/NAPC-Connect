export default function (Alpine) {
    Alpine.data('navbar', () => ({
        menuOpen: false,
        userOpen: false,

        get menuLabel() {
            return this.menuOpen ? 'Close' : 'Menu';
        },

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
        },
        toggleUser() {
            this.userOpen = !this.userOpen;
        },
        closeUser() {
            this.userOpen = false;
        },
        closeAll() {
            this.menuOpen = false;
            this.userOpen = false;
        },
    }));
}

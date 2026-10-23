export default function (Alpine) {
    Alpine.data('registerForm', (initialRole = 'seeker') => ({
        role: initialRole,

        get isPoster() {
            return this.role === 'poster';
        },
        cardClass(value) {
            return this.role === value
                ? 'bg-[var(--navy)] text-white'
                : 'bg-white text-[var(--navy)]';
        },
    }));
}

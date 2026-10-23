import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Auto-register every Alpine component.
// Shared ones live in components/, role-specific ones in their own folder.
const modules = import.meta.glob(
    [
        './components/**/*.js',
        './poster/**/*.js',
        './admin/**/*.js',
        './seeker/**/*.js',
    ],
    { eager: true },
);

Object.values(modules).forEach((module) => module.default(Alpine));

Alpine.start();

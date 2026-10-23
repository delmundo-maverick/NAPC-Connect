import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'; // Tailwind v4. Remove this line and the plugin below if you use Tailwind v3.
import { readdirSync, statSync, existsSync } from 'node:fs';
import { join, extname } from 'node:path';

function collect(dir, exts) {
    if (!existsSync(dir)) return [];

    return readdirSync(dir).flatMap((name) => {
        if (name.startsWith('_')) return [];

        const full = join(dir, name).replace(/\\/g, '/');

        if (statSync(full).isDirectory()) return collect(full, exts);

        return exts.includes(extname(full)) ? [full] : [];
    });
}

const cssFiles = collect('resources/css', ['.css']);
const jsFiles = collect('resources/js', ['.js']);

export default defineConfig({
    plugins: [
        laravel({
            input: [...cssFiles, ...jsFiles],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

import { URL, fileURLToPath } from 'node:url';
import { globSync, readFileSync } from 'node:fs';

import { defineConfig } from 'vite';
import inertia from '@inertiajs/vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';

// Auto-generate Vite aliases from local workspace packages.
// Mirrors Composer's path-repository glob: every package under packages/X/Y
// with a name + exports['.'] is aliased to its source automatically.
function workspaceAliases(): Record<string, string> {
    const aliases: Record<string, string> = {};
    for (const pkgFile of globSync('packages/*/*/package.json')) {
        const pkg = JSON.parse(readFileSync(pkgFile, 'utf-8'));
        const exports = pkg.exports?.['.'];
        const entry = pkg.source ?? (typeof exports === 'string' ? exports : null);
        if (pkg.name && entry) {
            aliases[pkg.name] = path.resolve(path.dirname(pkgFile), entry);
        }
    }
    return {
        ...aliases,
        $lib: fileURLToPath(new URL('./resources/js/lib', import.meta.url)),
        $components: fileURLToPath(new URL('./resources/js/components', import.meta.url)),
        $vendor: fileURLToPath(new URL('./vendor', import.meta.url)),
        $types: fileURLToPath(new URL('./resources/js/types', import.meta.url)),
        $layouts: fileURLToPath(new URL('./resources/js/Layouts', import.meta.url)),
        $pages: fileURLToPath(new URL('./resources/js/Pages', import.meta.url)),
        $lang: fileURLToPath(new URL('./lang', import.meta.url)),
        $models: fileURLToPath(new URL('./resources/js/types/models.ts', import.meta.url)),
        $actions: fileURLToPath(new URL('./resources/js/actions', import.meta.url)),
        $controllers: fileURLToPath(
            new URL('./resources/js/actions/App/Http/Controllers', import.meta.url),
        ),
    };
}

export default defineConfig({
    resolve: {
        alias: workspaceAliases(),
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        inertia({
            ssr: false,
        }),
        tailwindcss(),
        svelte(),
        wayfinder({
            formVariants: true,
        }),
    ],
});

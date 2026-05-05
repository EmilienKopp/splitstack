import { defineConfig } from 'vite';
import inertia from '@inertiajs/vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import path from 'path';
import { readFileSync, globSync } from 'node:fs';

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
    return aliases;
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

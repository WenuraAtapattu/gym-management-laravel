import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    root: '/app',
    base: '/build/',
    plugins: [
        laravel({
            input: [
                resolve(__dirname, 'resources/css/app.css'),
                resolve(__dirname, 'resources/js/app.js'),
            ],
            refresh: true,
            buildDirectory: 'build',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            '~': fileURLToPath(new URL('./node_modules', import.meta.url)),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', 'axios'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});

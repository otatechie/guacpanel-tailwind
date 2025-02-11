import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
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
            '@': path.resolve(__dirname, './resources/js'),
            'vue': 'vue/dist/vue.esm-bundler.js',

            // Carefully validate these paths
            'laravel-medialibrary-pro': path.resolve(__dirname, './vendor/spatie/laravel-medialibrary-pro/resources/js'),
            'media-library-pro-vue3-attachment': path.resolve(__dirname, './vendor/spatie/laravel-medialibrary-pro/resources/js/media-library-pro-vue3-attachment'),
            'media-library-pro-vue3-collection': path.resolve(__dirname, './vendor/spatie/laravel-medialibrary-pro/resources/js/media-library-pro-vue3-collection'),
            'media-library-pro-react-attachment': path.resolve(__dirname, './vendor/spatie/laravel-medialibrary-pro/resources/js/media-library-pro-react-attachment'),
            'media-library-pro-react-collection': path.resolve(__dirname, './vendor/spatie/laravel-medialibrary-pro/resources/js/media-library-pro-react-collection'),
        },
    },
    server: {
        hmr: {
            host: 'starter.test',
        },
        host: 'starter.test',
        cors: true,
    },
    build: {
        outDir: "public/build",
        manifest: true,
    },
});

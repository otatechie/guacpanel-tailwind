import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [vue()],
    test: {
        environment: 'happy-dom',
        include: ['resources/js/**/*.test.js'],
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@js': path.resolve(__dirname, 'resources/js'),
            '@css': path.resolve(__dirname, 'resources/css'),
        },
    },
})

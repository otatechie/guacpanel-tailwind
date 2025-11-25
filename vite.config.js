import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'
import { viteCommonjs, esbuildCommonjs } from '@originjs/vite-plugin-commonjs'
import { ViteMinifyPlugin } from 'vite-plugin-minify'

export default defineConfig(({ mode }) => {

    const env = loadEnv(mode, process.cwd(), '')
    const useChunkOutput = env.VITE_APP_CHUNK_OUTPUT === 'true'

    return {
        optimizeDeps: {
            force: true,
            esbuildOptions: {
                plugins: [esbuildCommonjs()],
            },
        },
        plugins: [
            laravel({
                input: [
                    'resources/js/app.js',
                    'resources/css/app.css',
                ],
                refresh: true,
                buildDirectory: 'build',
            }),
            tailwindcss(),
            vue({
                reactivityTransform: true,
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            viteCommonjs(),
            ViteMinifyPlugin({
                removeComments: true,
                collapseWhitespace: true,
                conservativeCollapse: true,
                removeRedundantAttributes: true,
                useShortDoctype: true,
                keepClosingSlash: true,
                removeEmptyAttributes: true,
                removeScriptTypeAttributes: true,
                removeStyleLinkTypeAttributes: true,
                sortAttributes: true,
                sortClassName: true,
                minifyCSS: true,
                minifyJS: true,
                ignoreCustomFragments: [
                    /<%[\s\S]*?%>/,
                    /<\?[\s\S]*?\?>/,
                    /\{\{[\s\S]*?\}\}/,
                ],
            }),
        ],
        resolve: {
            alias: {
                '~': path.resolve(__dirname, 'node_modules'),
                '@js': path.resolve(__dirname, './resources/js'),
                '@css': path.resolve(__dirname, 'resources/css'),
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },
        server: {
            cors: true,
        },
        build: {
            reportCompressedSize: true,
            chunkSizeWarningLimit: 1600,
            manifest: 'manifest.json',
            outDir: 'public/build',
            assetsDir: 'assets',
            sourcemap: false,
            minify: 'terser',
            terserOptions: {
                compress: {
                    drop_console: true,
                    drop_debugger: true,
                },
            },
            modulePreload: {
                polyfill: true,
            },
            commonjsOptions: {
                include: [/node_modules/],
            },
            rollupOptions: {
                output: {
                    manualChunks(id) {
                        if (!useChunkOutput) {
                            return
                        }

                        if (id.includes('node_modules')) {
                            const modulePath = id.split('node_modules/')[1]
                            const topLevelFolder = modulePath.split('/')[0]

                            if (topLevelFolder !== '.pnpm') {
                                return topLevelFolder
                            }

                            const scopedPackageName = modulePath.split('/')[1]
                            const chunkName =
                                scopedPackageName.split('@')[
                                    scopedPackageName.startsWith('@') ? 1 : 0
                                ]

                            return chunkName
                        }
                    },
                    entryFileNames: 'js/[name].js',
                    chunkFileNames: 'js/[name].js',
                    assetFileNames: 'assets/[name].[ext]',
                },
            },
        },
    }
})

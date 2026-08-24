import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import path from "path";
// import { viteCommonjs, esbuildCommonjs } from "@originjs/vite-plugin-commonjs";
import { ViteMinifyPlugin } from "vite-plugin-minify";

export default defineConfig(() => {
  return {
    optimizeDeps: {
      esbuildOptions: {
        target: "es2020",
      },
    },
    plugins: [
      laravel({
        input: ["resources/js/app.js", "resources/css/app.css"],
        refresh: true,
        buildDirectory: "build",
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
      // viteCommonjs(),
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
        "~": path.resolve(__dirname, "node_modules"),
        "@js": path.resolve(__dirname, "./resources/js"),
        "@css": path.resolve(__dirname, "resources/css"),
      },
    },
    server: {
      cors: true,
    },
    build: {
      reportCompressedSize: true,
      manifest: "manifest.json",
      outDir: "public/build",
      assetsDir: "assets",
      sourcemap: false,
      minify: "terser",
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
          entryFileNames: "js/[name].js",
          chunkFileNames: "js/[name].js",
          assetFileNames: "assets/[name].[ext]",
        },
      },
    },
  };
});

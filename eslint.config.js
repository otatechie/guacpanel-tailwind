import js from "@eslint/js";
import globals from "globals";
import vue from "eslint-plugin-vue";
import * as parser from "vue-eslint-parser";
import prettier from "eslint-config-prettier/flat";

export default [
  {
    ignores: [
      "**/node_modules/**",
      "**/dist/**",
      "**/build/**",
      "**/vendor/**",
      "**/public/**",
      "tailwind.config.js",
      // Generated shadcn-vue source (TypeScript) — regenerate, don't lint
      "resources/js/Components/ui/**",
      "resources/js/lib/**",
    ],
  },
  {
    // The wrapper boundary: pages never touch the shadcn layer directly.
    // See docs/ui-contract.md.
    files: [
      "resources/js/Pages/**/*.{js,vue}",
      "resources/js/Layouts/**/*.{js,vue}",
      "resources/js/composables/**/*.js",
      "resources/js/utils/**/*.js",
    ],
    rules: {
      "no-restricted-imports": [
        "error",
        {
          patterns: [
            {
              group: ["@/Components/ui/*", "@/Components/ui/**", "@js/Components/ui/*", "@js/Components/ui/**"],
              message:
                "Import the wrapper from @/Components/* instead — pages must not depend on shadcn directly (docs/ui-contract.md).",
            },
            {
              group: ["reka-ui", "reka-ui/*"],
              message: "reka-ui is only used inside Components/ui/ (docs/ui-contract.md).",
            },
            {
              group: ["class-variance-authority", "@/lib/*", "@/lib/**"],
              message: "cva/cn belong to the ui layer (docs/ui-contract.md).",
            },
          ],
        },
      ],
    },
  },
  {
    files: ["**/*.{js,jsx,mjs,cjs,ts,tsx}"],
    ...js.configs.recommended,
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.es2021,
        ...globals.node,
      },
    },
    rules: {
      indent: "off",
      "linebreak-style": "off",
      quotes: "off",
      semi: "off",
      "no-unused-vars": "warn",
    },
  },
  {
    files: ["**/*.vue"],
    ...vue.configs["vue3-essential"],
    languageOptions: {
      parser: parser,
      parserOptions: {
        ecmaVersion: "latest",
        sourceType: "module",
      },
    },
    rules: {
      indent: "off",
      "linebreak-style": "off",
      quotes: "off",
      semi: "off",
      "vue/html-indent": "off",
      "vue/max-attributes-per-line": "off",
      "vue/multiline-html-element-content-newline": "off",
      "vue/singleline-html-element-content-newline": "off",
      "vue/first-attribute-linebreak": "off",
      "vue/html-closing-bracket-newline": "off",
      "vue/html-closing-bracket-spacing": "off",
      "vue/html-self-closing": "off",
      "vue/multi-word-component-names": "off",
      "vue/require-default-prop": "off",
      "vue/no-v-html": "off",
      "no-unused-vars": "off",
    },
  },
  prettier,
];

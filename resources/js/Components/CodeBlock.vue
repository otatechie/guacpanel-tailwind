<script setup>
import { ref, onMounted, watch } from 'vue';
import hljs from 'highlight.js/lib/core';
import 'highlight.js/styles/github.css';

import javascript from 'highlight.js/lib/languages/javascript';
import php from 'highlight.js/lib/languages/php';
import xml from 'highlight.js/lib/languages/xml';
import css from 'highlight.js/lib/languages/css';
import bash from 'highlight.js/lib/languages/bash';

hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('js', javascript);
hljs.registerLanguage('php', php);
hljs.registerLanguage('html', xml);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('css', css);
hljs.registerLanguage('bash', bash);

const languageAliases = {
    'vue': 'html',
    'jsx': 'javascript',
    'tsx': 'typescript'
};

const props = defineProps({
    code: {
        type: [String, Object],
        required: true
    },
    language: {
        type: String,
        default: 'javascript'
    }
});

const highlightedCode = ref('');

const highlight = () => {
    const codeToHighlight = typeof props.code === 'object'
        ? JSON.stringify(props.code, null, 2)
        : props.code;

    const lang = languageAliases[props.language] || props.language;

    const result = lang === 'auto'
        ? hljs.highlightAuto(codeToHighlight)
        : hljs.highlight(codeToHighlight, {
            language: lang,
            ignoreIllegals: true
        });

    highlightedCode.value = result.value;
};

onMounted(highlight);
watch(() => [props.code, props.language], highlight);
</script>


<template>
    <div class="code-block-container">
        <pre><code :class="language" v-html="highlightedCode"></code></pre>
    </div>
</template>

<style>
.code-block-container {
    width: 100%;
    font-weight: 600;
    overflow-x: auto;
    margin: 1rem 0;
    border-radius: 0.2rem;
    background-color: #fdf6e3 !important;
    color: #000 !important;
    border: 1px solid #eee8d5;
}

pre {
    margin: 0;
    padding: 1rem;
    position: relative;
}

code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.875rem;
    line-height: 1.5;
}

.hljs {
    background: transparent !important;
    padding: 0 !important;
}
</style>

<script setup>
import { ref, onMounted, watch } from 'vue'
import hljs from 'highlight.js/lib/core'
import 'highlight.js/styles/atom-one-dark.css'

import javascript from 'highlight.js/lib/languages/javascript'
import php from 'highlight.js/lib/languages/php'
import xml from 'highlight.js/lib/languages/xml'
import css from 'highlight.js/lib/languages/css'
import bash from 'highlight.js/lib/languages/bash'
import typescript from 'highlight.js/lib/languages/typescript'
import python from 'highlight.js/lib/languages/python'
import json from 'highlight.js/lib/languages/json'

hljs.registerLanguage('javascript', javascript)
hljs.registerLanguage('js', javascript)
hljs.registerLanguage('php', php)
hljs.registerLanguage('html', xml)
hljs.registerLanguage('xml', xml)
hljs.registerLanguage('css', css)
hljs.registerLanguage('bash', bash)
hljs.registerLanguage('typescript', typescript)
hljs.registerLanguage('ts', typescript)
hljs.registerLanguage('python', python)
hljs.registerLanguage('py', python)
hljs.registerLanguage('json', json)

const languageAliases = {
    vue: 'html',
    jsx: 'javascript',
    tsx: 'typescript'
}

const props = defineProps({
    code: {
        type: [String, Object],
        required: true
    },
    language: {
        type: String,
        default: 'javascript'
    },
    showCopyButton: {
        type: Boolean,
        default: true
    }
})

const highlightedCode = ref('')
const copySuccess = ref(false)

const highlight = () => {
    const codeToHighlight =
        typeof props.code === 'object' ? JSON.stringify(props.code, null, 2) : props.code

    const lang = languageAliases[props.language] || props.language

    const result =
        lang === 'auto'
            ? hljs.highlightAuto(codeToHighlight)
            : hljs.highlight(codeToHighlight, {
                  language: lang,
                  ignoreIllegals: true
              })

    highlightedCode.value = result.value
}

const copyToClipboard = async () => {
    try {
        const codeToCopy = typeof props.code === 'object' ? JSON.stringify(props.code, null, 2) : props.code
        await navigator.clipboard.writeText(codeToCopy)
        copySuccess.value = true
        setTimeout(() => {
            copySuccess.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy: ', err)
    }
}

onMounted(highlight)
watch(() => [props.code, props.language], highlight)
</script>

<template>
    <div class="code-block-container">
        <div class="code-header" v-if="showCopyButton">
            <span class="language-label">{{ language }}</span>
            <button 
                @click="copyToClipboard"
                class="copy-button"
                :class="{ 'copy-success': copySuccess }"
                :title="copySuccess ? 'Copied!' : 'Copy to clipboard'"
            >
                <svg v-if="!copySuccess" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>
        <pre><code :class="language" v-html="highlightedCode"></code></pre>
    </div>
</template>

<style scoped>
.code-block-container {
    width: 100%;
    margin: 0;
    border-radius: 0.5rem;
    background-color: #1a1a1a;
    border: 1px solid #374151;
    overflow: hidden;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
}

.code-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background-color: #1a1a1a;
    border-bottom: 1px solid #374151;
}

.language-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.copy-button {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    background-color: transparent;
    border: 1px solid #4b5563;
    border-radius: 0.25rem;
    color: #9ca3af;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.copy-button:hover {
    background-color: #374151;
    color: #d1d5db;
    border-color: #6b7280;
}

.copy-button.copy-success {
    background-color: #10b981;
    color: white;
    border-color: #10b981;
}

pre {
    margin: 0;
    padding: 1rem;
    overflow-x: auto;
    background-color: #1a1a1a;
}

code {
    font-family: inherit;
    font-size: 0.875rem;
    line-height: 1.6;
    color: #e5e7eb;
}

.hljs {
    background: transparent !important;
    padding: 0 !important;
    color: #e5e7eb !important;
}

/* Dark theme syntax highlighting overrides */
.hljs-comment,
.hljs-quote {
    color: #6b7280 !important;
}

.hljs-keyword,
.hljs-selector-tag,
.hljs-subst {
    color: #f472b6 !important;
}

.hljs-number,
.hljs-literal,
.hljs-variable,
.hljs-template-variable,
.hljs-tag .hljs-attr {
    color: #fbbf24 !important;
}

.hljs-string,
.hljs-doctag {
    color: #34d399 !important;
}

.hljs-title,
.hljs-section,
.hljs-selector-id {
    color: #60a5fa !important;
}

.hljs-type,
.hljs-class .hljs-title {
    color: #a78bfa !important;
}

.hljs-tag,
.hljs-name,
.hljs-attribute {
    color: #f87171 !important;
}

.hljs-regexp,
.hljs-link {
    color: #fbbf24 !important;
}

.hljs-symbol,
.hljs-bullet {
    color: #34d399 !important;
}

.hljs-built_in,
.hljs-builtin-name {
    color: #60a5fa !important;
}

.hljs-meta {
    color: #9ca3af !important;
}

.hljs-deletion {
    background: #dc2626 !important;
}

.hljs-addition {
    background: #16a34a !important;
}

.hljs-emphasis {
    font-style: italic;
}

.hljs-strong {
    font-weight: bold;
}
</style>

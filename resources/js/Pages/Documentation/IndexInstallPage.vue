<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import hljs from 'highlight.js/lib/core'
import bash from 'highlight.js/lib/languages/bash'
import php from 'highlight.js/lib/languages/php'
import 'highlight.js/styles/github-dark.css'

hljs.registerLanguage('bash', bash)
hljs.registerLanguage('php', php)

const vHighlight = {
    mounted(el) {
        hljs.highlightElement(el)
    },
    updated(el) {
        hljs.highlightElement(el)
    }
}

defineOptions({
    layout: Public
})

const searchQuery = ref('')

const articleLinks = [
    { text: 'Prerequisites', href: '#prerequisites' },
    { text: 'Installation', href: '#installation' },
    { text: 'After Installation', href: '#after-installation' }
]

const filteredSections = computed(() => {
    if (!searchQuery.value) return features.core
    const query = searchQuery.value.toLowerCase()

    return features.core.map(section => ({
        ...section,
        items: section.items.filter(item =>
            item.name.toLowerCase().includes(query) ||
            item.description.toLowerCase().includes(query)
        )
    })).filter(section => section.items.length > 0)
})

const applyHighlighting = () => {
    nextTick(() => {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block)
        })
    })
}

onMounted(() => {
    applyHighlighting()
})
</script>

<template>

    <Head title="Installation - Starter Kit" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-purple-600 to-indigo-600 dark:from-purple-900 dark:to-indigo-900">
            <div class="relative z-10 p-8 md:p-12">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Getting Started</h1>
                </div>
                <p class="text-lg text-purple-100 dark:text-purple-200 max-w-3xl mb-8">
                    Get started with our Laravel Vue Starter Kit in minutes. Follow this step-by-step guide to set up
                    your
                    development environment and begin building powerful admin interfaces.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#installation"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-purple-600 hover:bg-purple-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Installation guide
                    </a>
                    <a href="https://github.com/otatechie/starter" target="_blank"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-purple-500 text-white hover:bg-purple-400 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                clip-rule="evenodd" />
                        </svg>
                        View on GitHub
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-400/30 to-indigo-400/30 blur-3xl rounded-full">
            </div>
        </div>

        <section id="prerequisites" class="mb-12 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div
                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Prerequisites</h2>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <ul class="list-disc list-inside space-y-4 text-gray-800 dark:text-white">
                    <li>
                        <span class="font-medium">PHP >= 8.2</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">With required extensions</span>
                    </li>
                    <li>
                        <span class="font-medium">Node.js & NPM</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">Latest LTS version</span>
                    </li>
                    <li>
                        <span class="font-medium">Composer</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">Package manager</span>
                    </li>
                </ul>
            </div>
        </section>

        <section id="installation" class="space-y-6 mb-12 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Installation Steps</h2>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <div class="space-y-6">
                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <div
                                class="w-8 h-8 bg-blue-100 dark:bg-blue-800/50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                            </div>
                            Clone the Repository
                        </h3>
                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                onclick="navigator.clipboard.writeText('git clone https://github.com/otatechie/starter.git\ncd starter')">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <pre><code v-highlight class="language-bash">git clone https://github.com/otatechie/starter.git
cd starter</code></pre>
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <div
                                class="w-8 h-8 bg-purple-100 dark:bg-purple-800/50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            Install Dependencies
                        </h3>
                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                onclick="navigator.clipboard.writeText('composer install\nnpm install')">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <pre><code v-highlight class="language-bash">composer install
npm install</code></pre>
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <div
                                class="w-8 h-8 bg-green-100 dark:bg-green-800/50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            Set Up Environment
                        </h3>
                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                onclick="navigator.clipboard.writeText('cp .env.example .env\nphp artisan key:generate')">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <pre><code v-highlight class="language-bash">cp .env.example .env
php artisan key:generate</code></pre>
                        </div>
                        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">Configure your database and other
                            settings in
                            the .env file</p>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <div
                                class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800/50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            Run Development Server
                        </h3>
                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                onclick="navigator.clipboard.writeText('npm run dev\nphp artisan serve')">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                            <pre><code v-highlight class="language-bash">npm run dev
php artisan serve</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="after-installation" class="mb-12 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div
                    class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">After Installation</h2>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Database Setup</h3>
                <div class="space-y-4">
                    <div class="prose dark:prose-invert max-w-none">
                        <p>1. Update your database credentials in .env file:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-4">
                        <pre><code v-highlight class="language-bash">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password</code></pre>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p>2. Run migrations:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-4">
                        <pre><code v-highlight class="language-bash">php artisan migrate</code></pre>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Common Issues</h3>
                <div class="grid gap-6">
                    <div class="space-y-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-800/50 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">Permission Issues</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you encounter permission
                                    issues, run:</p>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre><code v-highlight class="language-bash">chmod -R 777 storage bootstrap/cache</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">Composer Dependencies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you get dependency errors,
                                    try:</p>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre><code v-highlight class="language-bash">composer install --ignore-platform-reqs</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white">NPM Issues</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you have npm issues, try
                                    clearing the cache:</p>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre><code v-highlight class="language-bash">npm cache clean --force
npm install</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="flex flex-col space-y-8 mb-8">
        <ArticleNavigation :links="articleLinks" />
    </div>
</template>

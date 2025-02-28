<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'

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
</script>

<template>

    <Head title="Installation - Starter Kit" />

    <div class="max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800">
            <div class="relative z-10 p-8 md:p-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">Installation Guide</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl">
                    Get started with our Laravel Vue Starter Kit in minutes. Follow this simple guide to set up your
                    development environment.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#installation"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Start Installation
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="https://github.com/otatechie/starter" target="_blank"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                clip-rule="evenodd" />
                        </svg>
                        View on GitHub
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-800/20 dark:to-indigo-800/20 blur-3xl rounded-full opacity-70">
            </div>
        </div>

        <section id="prerequisites" class="mb-12 scroll-mt-16">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Prerequisites</h2>
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="flex items-start gap-4 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <div class="p-2 bg-purple-100 dark:bg-purple-800/50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 dark:text-white">PHP >= 8.2</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">With required extensions</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <div class="p-2 bg-purple-100 dark:bg-purple-800/50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 dark:text-white">Node.js & NPM</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Latest LTS version</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <div class="p-2 bg-purple-100 dark:bg-purple-800/50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800 dark:text-white">Composer</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Package manager</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="installation" class="space-y-6 mb-12 scroll-mt-16">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Installation Steps</h2>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <div class="space-y-6">
                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">1</span>
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
                            <pre class="text-green-400"><span class="text-yellow-400 text-sm">git</span> clone https://github.com/otatechie/starter.git
<span class="text-cyan-400">cd</span> starter</pre>
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">2</span>
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
                            <pre class="text-yellow-400 text-sm">composer install
npm install</pre>
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">3</span>
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
                            <pre class="text-yellow-400 text-sm">cp .env.example .env
php artisan key:generate</pre>
                        </div>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Configure your database and other settings in
                            the .env file</p>
                    </div>

                    <div>
                        <h3 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <span
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">4</span>
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
                            <pre class="text-yellow-400 text-sm">npm run dev
php artisan serve</pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="after-installation" class="mb-12 scroll-mt-16">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">After Installation</h2>
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Database Setup</h3>
                <div class="space-y-4">
                    <div class="prose dark:prose-invert max-w-none">
                        <p>1. Update your database credentials in .env file:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-4">
                        <pre class="text-yellow-400 text-sm">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password</pre>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p>2. Run migrations:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                            onclick="navigator.clipboard.writeText('php artisan migrate')">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <pre class="text-yellow-400 text-sm">php artisan migrate</pre>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Common Issues</h3>
                <div class="grid gap-6">
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
                                <h4 class="font-medium text-gray-800 dark:text-white">Permission Issues</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you encounter permission
                                    issues, run:</p>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre class="text-yellow-400 text-sm">chmod -R 777 storage bootstrap/cache</pre>
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
                                <h4 class="font-medium text-gray-800 dark:text-white">Composer Dependencies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you get dependency errors,
                                    try:</p>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre class="text-yellow-400 text-sm">composer install --ignore-platform-reqs</pre>
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
                                    <pre class="text-yellow-400 text-sm">npm cache clean --force npm install</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <ArticleNavigation :links="articleLinks" />
</template>

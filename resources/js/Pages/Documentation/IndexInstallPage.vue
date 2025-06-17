<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'
import 'highlight.js/styles/atom-one-dark.css';

defineOptions({
    layout: Public
})

const searchQuery = ref('')
const showBackToTop = ref(false)

const codeExamples = {
    cloneRepo: `git clone https://github.com/otatechie/guacpanel-tailwind.git
cd guacpanel-tailwind`,

    installDeps: `composer install
npm install`,

    setupEnv: `cp .env.example .env`,

    runServer: `npm run dev
php artisan serve`,

    installAssets: `npm install
npm run dev`,

    databaseEnv: `DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password`,

    runMigrations: `php artisan migrate`,

    seedDatabase: `php artisan db:seed`,

    defaultCredentials: `Email: ota@example.com
Password: password`,

    permissionFix: `chmod -R 777 storage bootstrap/cache`,

    composerFix: `composer install --ignore-platform-reqs`,

    npmFix: `npm cache clean --force
npm install`,

    mysqlDumpEnv: `DB_DUMP_PATH=your path`,

    mysqlDumpConfig: `'mysql' => [
    // ...other mysql configuration
    'dump' => [
        'dump_binary_path' => '/usr/bin/mysqldump',  // Adjust your path
    ],
],`
}

const articleLinks = [
    { text: 'Prerequisites', href: '#prerequisites' },
    { text: 'Installation', href: '#installation' },
    { text: 'Database Setup', href: '#database-setup' },
    { text: 'Common Issues', href: '#common-issues' }
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

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>


<template>

    <Head title="Installation - Starter Kit" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-teal-600 to-teal-700 dark:from-teal-900 dark:to-teal-800">
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
                <p class="text-lg text-teal-100 dark:text-teal-200 max-w-3xl mb-8">
                    Set up GuacPanel in minutes with this step-by-step guide. Build your Laravel admin interface with
                    authentication, permissions, and modern UI components ready to go.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#installation"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-teal-600 hover:bg-teal-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Installation guide
                    </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-teal-500 text-white hover:bg-teal-400 transition-colors font-medium">
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
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-teal-400/30 to-teal-500/30 blur-3xl rounded-full">
            </div>
        </div>

        <section id="prerequisites" class="mb-12 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
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
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
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
                        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <span class="mr-3">1.</span> Clone the Repository
                        </h3>
                        <div class="bg-gray-800 rounded-lg">
                            <CodeBlock :code="codeExamples.cloneRepo" language="bash" />
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <span class="mr-3">2.</span> Install Dependencies
                        </h3>
                        <div class="bg-gray-800 rounded-lg">
                            <CodeBlock :code="codeExamples.installDeps" language="bash" />
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <span class="mr-3">3.</span> Set Up Environment
                        </h3>
                        <div class="bg-gray-800 rounded-lg">
                            <CodeBlock :code="codeExamples.setupEnv" language="bash" />
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <span class="mr-3">4.</span> Start Development Server
                        </h3>
                        <div class="bg-gray-800 rounded-lg">
                            <CodeBlock :code="codeExamples.runServer" language="bash" />
                        </div>
                    </div>

                    <div>
                        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            <span class="mr-3">5.</span> Install and compile frontend assets
                        </h3>
                        <div class="bg-gray-800 rounded-lg">
                            <CodeBlock :code="codeExamples.installAssets" language="bash" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="after-installation" class="mb-12 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">After Installation</h2>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8 mb-6"
                id="database-setup">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Database Setup</h3>
                <div class="space-y-4">
                    <div class="prose dark:prose-invert max-w-none">
                        <p><span class="mr-3">1.</span> Update your database credentials in .env file:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.databaseEnv" language="bash" />
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p><span class="mr-3">2.</span> Run migrations:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.runMigrations" language="bash" />
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p><span class="mr-3">3.</span> Seed the database with initial data:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.seedDatabase" language="bash" />
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p><span class="mr-3">4.</span> Default superuser credentials:</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.defaultCredentials" language="bash" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8"
                id="common-issues">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Common Issues</h3>
                <hr class="border-gray-100 dark:border-gray-700 my-4">
                <div class="grid gap-6">
                    <div class="space-y-4 rounded-lg ">
                        <div class="flex items-start gap-3">
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white"><span class="mr-3">1.</span>
                                    Permission Issues</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you encounter permission
                                    issues, run:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.permissionFix" language="bash" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white"><span class="mr-3">2.</span>
                                    Composer Dependencies</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you get dependency errors,
                                    try:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.composerFix" language="bash" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white"><span class="mr-3">3.</span> NPM
                                    Issues</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">If you have npm issues, try
                                    clearing the cache:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.npmFix" language="bash" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-white"><span class="mr-3">4.</span> MySQL
                                    Database Backup</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Configure mysqldump path in
                                    your .env file:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.mysqlDumpEnv" language="bash" />
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 mb-2">Alternatively, you can set
                                    the path in config/database.php:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.mysqlDumpConfig" language="php" />
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

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <a href="/documentation/getting-started"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Previous</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">
                        Getting Started</div>
                </div>
            </a>

            <a href="/documentation/features"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">
                        Features</div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <button v-show="showBackToTop" @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-teal-600 text-white p-3 rounded-full shadow-lg hover:bg-teal-500 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
        aria-label="Back to top">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</template>

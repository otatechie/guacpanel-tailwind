<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'
import 'highlight.js/styles/atom-one-dark.css'

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
    <Head title="Installation - GuacPanel" />

    <div id="prerequisites" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-900 border p-8 md:p-12 mb-12">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <h1 class="text-3xl md:text-4xl font-bold">
                        Installation Guide
                    </h1>
                </div>

                <div class=" max-w-2xl mx-auto mb-8">
                    <p class="text-gray-700 dark:text-gray-200 text-lg">
                    Set up GuacPanel in minutes with this step-by-step guide. Build your Laravel
                    admin interface with authentication, permissions, and modern UI components ready
                    to go.
                </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#installation"
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    [Installation_Guide]
                </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                clip-rule="evenodd" />
                        </svg>
                    [Source_Code]
                    </a>
                </div>
            </div>
        </div>

        <section class="space-y-10">
            <!-- Prerequisites Section -->
            <section id="prerequisites" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Prerequisites
                </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Ensure you have the following requirements before installing GuacPanel
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">PHP >= 8.2</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Required PHP extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL,
                                PDO, Tokenizer, XML, cURL, GD/Imagick
                            </p>
                        </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Node.js >= 18.0.0</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Required for Vite build tool, Tailwind CSS v4, and Vue.js
                                development
                            </p>
                        </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Composer >= 2.0</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Required for Laravel 11, Spatie packages, and PHP dependencies
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Database</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                MySQL/PostgreSQL for Laravel, Spatie Backup, and application data storage
                            </p>
                        </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Git</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                For cloning the repository and version control
                            </p>
                        </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">System Resources</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Sufficient disk space for dependencies and internet connection
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Installation Section -->
            <section id="installation" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Installation Steps
                </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Follow these steps to get GuacPanel up and running on your system
                    </p>
                </div>
                <div class="space-y-6">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">1. Clone the Repository</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.cloneRepo" language="bash" />
                            </div>
                            </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">2. Install Dependencies</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.installDeps" language="bash" />
                            </div>
                            </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">3. Set Up Environment</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.setupEnv" language="bash" />
                            </div>
                            </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">4. Start Development Server</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.runServer" language="bash" />
                            </div>
                            </div>
                        </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">5. Install and compile frontend assets</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.installAssets" language="bash" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Database Setup Section -->
            <section id="database-setup" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Database Setup
                </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Configure your database and initialize the application data
                    </p>
                </div>
                <div class="space-y-6">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">1. Update Database Credentials</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Update your database credentials in .env file:
                            </p>
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.databaseEnv" language="bash" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">2. Run Migrations</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Create the database tables:
                            </p>
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.runMigrations" language="bash" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">3. Seed Database</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Seed the database with initial data:
                            </p>
                            <div class="bg-gray-800 rounded-md">
                                <CodeBlock :code="codeExamples.seedDatabase" language="bash" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">4. Default Credentials</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Default superuser credentials:
                            </p>
                        <div class="bg-gray-800 rounded-md">
                            <CodeBlock :code="codeExamples.defaultCredentials" language="bash" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Common Issues Section -->
            <section id="common-issues" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    Common Issues
                </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Troubleshooting guide for common installation problems
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Permission Issues</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                        If you encounter permission issues, run:
                                    </p>
                                    <div class="bg-gray-800 rounded-md">
                                        <CodeBlock
                                            :code="codeExamples.permissionFix"
                                            language="bash" />
                                    </div>
                                </div>
                            </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">Composer Dependencies</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                        If you get dependency errors, try:
                                    </p>
                                    <div class="bg-gray-800 rounded-md">
                                        <CodeBlock
                                            :code="codeExamples.composerFix"
                                            language="bash" />
                                    </div>
                                </div>
                            </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">NPM Issues</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                        If you have npm issues, try clearing the cache:
                                    </p>
                                    <div class="bg-gray-800 rounded-md">
                                        <CodeBlock :code="codeExamples.npmFix" language="bash" />
                                    </div>
                                </div>
                            </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">MySQL Database Backup</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                        Configure mysqldump path in your .env file:
                                    </p>
                                    <div class="bg-gray-800 rounded-md">
                                        <CodeBlock
                                            :code="codeExamples.mysqlDumpEnv"
                                            language="bash" />
                                    </div>
                            <p class="text-gray-700 dark:text-gray-200 text-sm mt-2 mb-2 font-mono">
                                        Alternatively, you can set the path in config/database.php:
                                    </p>
                                    <div class="bg-gray-800 rounded-md">
                                        <CodeBlock
                                            :code="codeExamples.mysqlDumpConfig"
                                            language="php" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/documentation"
                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-900 border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300 hover:border-gray-600 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                [Documentation]
            </a>

            <a href="/documentation/features"
                class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                [Features]
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button v-show="showBackToTop"
        class="fixed bottom-8 right-8 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono text-sm p-3"
        aria-label="Back to top" @click="scrollToTop">
        [^]
    </button>

    <ArticleNavigation :links="articleLinks" />
</template>

<style scoped>
.scroll-mt-16 {
    scroll-margin-top: 4rem;
}

@media (max-width: 640px) {
    .scroll-mt-16 {
        scroll-margin-top: 5rem;
    }
}
</style>

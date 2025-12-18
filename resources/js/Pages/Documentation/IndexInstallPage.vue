<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@js/Layouts/Public.vue'
import ArticleNavigation from '@js/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@js/Components/Common/CodeBlock.vue'
import 'highlight.js/styles/atom-one-dark.css'

defineOptions({
  layout: Public,
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


  installBroadcasting: `php artisan install:broadcasting`,

  reverbEnv: `APP_NOTIFICATIONS_ENABLED=true
APP_NOTIFICATIONS_IN_DEMO_MODE=true # set this to false to enable live reverb notifications.

REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_TLS_CERT=
REVERB_TLS_KEY=`,

  startReverb: `php artisan reverb:start`,

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
],`,
}

const articleLinks = [
  { text: 'Prerequisites', href: '#prerequisites' },
  { text: 'Installation', href: '#installation' },
  { text: 'Reverb & Notifications', href: '#reverb-notifications' },
  { text: 'Database Setup', href: '#database-setup' },
  { text: 'Common Issues', href: '#common-issues' },
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

  <div id="prerequisites" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="mb-12 border bg-white p-8 md:p-12 dark:bg-gray-900">
      <div class="text-center">
        <div class="mb-4 flex items-center justify-center">
          <h1 class="text-3xl font-bold md:text-4xl">Installation Guide</h1>
        </div>

        <div class="mx-auto mb-8 max-w-2xl">
          <p class="text-lg text-gray-700 dark:text-gray-200">
            Set up GuacPanel in minutes with this step-by-step guide. Build your Laravel admin
            interface with authentication, permissions, and modern UI components ready to go.
          </p>
        </div>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
          <a
            href="#installation"
            class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            [Installation_Guide]
          </a>
          <a
            href="https://github.com/otatechie/guacpanel-tailwind"
            target="_blank"
            class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
              <path
                fill-rule="evenodd"
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
      <section id="prerequisites" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Prerequisites</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Ensure you have the following requirements before installing GuacPanel
          </p>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">PHP >= 8.2</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Required PHP extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer,
                XML, cURL, GD/Imagick
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Node.js >= 18.0.0</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Required for Vite build tool, Tailwind CSS v4, and Vue.js development
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Composer >= 2.0</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Required for Laravel 11, Spatie packages, and PHP dependencies
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Database</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                MySQL/PostgreSQL for Laravel, Spatie Backup, and application data storage
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Git</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                For cloning the repository and version control
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">System Resources</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Sufficient disk space for dependencies and internet connection
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Installation Section -->
      <section id="installation" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Installation Steps</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Follow these steps to get GuacPanel up and running on your system
          </p>
        </div>
        <div class="space-y-6">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">1. Clone the Repository</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.cloneRepo" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">2. Install Dependencies</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.installDeps" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">3. Set Up Environment</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.setupEnv" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                4. Start Development Server
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.runServer" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                5. Install and compile frontend assets
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.installAssets" language="bash" />
              </div>
            </div>
          </div>
        </div>
      </section>


      <!-- Reverb & Notifications Section -->
      <section id="reverb-notifications" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
            Reverb & Live Notifications
          </h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Enable real-time, in-app notifications using Laravel Reverb (WebSockets)
          </p>
        </div>

        <div class="space-y-6">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                1. Install Broadcasting + Reverb
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Run the installer to configure broadcasting and generate your Reverb credentials:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.installBroadcasting" language="bash" />
              </div>

              <div
                class="mt-4 rounded-lg border border-teal-400 bg-gradient-to-br from-teal-50 to-teal-50 p-3 dark:border-teal-800/30 dark:from-teal-900/20 dark:to-teal-900/20">
                <p class="flex items-start space-x-2 text-sm text-teal-800 dark:text-teal-300">
                  <span class="flex-shrink-0 text-xl">💡</span>
                  <span>
                    <strong>Note:</strong>
                    This command will create and populate <code class="font-mono">REVERB_APP_ID</code>,
                    <code class="font-mono">REVERB_APP_KEY</code>, and <code class="font-mono">REVERB_APP_SECRET</code>
                    in your <code class="font-mono">.env</code>.
                  </span>
                </p>
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                2. Configure Environment Variables
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Update your <code class="font-mono">.env</code> to enable notifications and configure Reverb:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.reverbEnv" language="bash" />
              </div>

              <p class="mt-3 font-mono text-sm text-gray-700 dark:text-gray-200">
                When ready for live sockets, set <code class="font-mono">APP_NOTIFICATIONS_IN_DEMO_MODE=false</code>.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                3. Start the Reverb Server
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Run Reverb in a separate terminal during development:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.startReverb" language="bash" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Database Setup Section -->
      <section id="database-setup" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Database Setup</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Configure your database and initialize the application data
          </p>
        </div>
        <div class="space-y-6">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">
                1. Update Database Credentials
              </h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Update your database credentials in .env file:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.databaseEnv" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">2. Run Migrations</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Create the database tables:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.runMigrations" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">3. Seed Database</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Seed the database with initial data:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.seedDatabase" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">4. Default Credentials</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Default superuser credentials:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.defaultCredentials" language="bash" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Common Issues Section -->
      <section id="common-issues" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Common Issues</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Troubleshooting guide for common installation problems
          </p>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Permission Issues</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                If you encounter permission issues, run:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.permissionFix" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Composer Dependencies</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                If you get dependency errors, try:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.composerFix" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">NPM Issues</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                If you have npm issues, try clearing the cache:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.npmFix" language="bash" />
              </div>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">MySQL Database Backup</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Configure mysqldump path in your .env file:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.mysqlDumpEnv" language="bash" />
              </div>
              <p class="mt-2 mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Alternatively, you can set the path in config/database.php:
              </p>
              <div class="rounded-md bg-gray-800">
                <CodeBlock :code="codeExamples.mysqlDumpConfig" language="php" />
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>
  </div>

  <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-col justify-center gap-4 sm:flex-row">
      <a
        href="/documentation"
        class="inline-flex items-center border border-gray-400 bg-white px-6 py-3 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7" />
        </svg>
        [Documentation]
      </a>

      <a
        href="/documentation/features"
        class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
        [Features]
        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
    </div>
  </div>

  <!-- Back to Top Button -->
  <button
    v-show="showBackToTop"
    class="fixed right-8 bottom-8 border border-green-500 bg-green-500/20 p-3 font-mono text-sm text-green-700 transition-all hover:bg-green-500/30 dark:text-green-200"
    aria-label="Back to top"
    @click="scrollToTop">
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

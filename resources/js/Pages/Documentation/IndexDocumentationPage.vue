<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import Public from '@js/Layouts/Public.vue'
import ArticleNavigation from '@js/Shared/Public/ArticleNavigation.vue'
import FeatureCard from '@js/Components/Cards/FeatureCard.vue'
import TechCard from '@js/Components/Cards/TechCard.vue'

defineOptions({
  layout: Public,
})

const articleLinks = [
  { text: 'Introduction', href: '#introduction' },
  { text: 'Core Features', href: '#core-features' },
  { text: 'Technologies', href: '#technologies' },
  { text: 'Getting Started', href: '#getting-started' },
]

const showBackToTop = ref(false)

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 500
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <Head title="Introduction - GuacPanel" />

  <div id="introduction" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="mb-12 border bg-white p-8 md:p-12 dark:bg-gray-900">
      <div class="text-center">
        <div class="mb-4 flex items-center justify-center">
          <h1 class="text-3xl font-bold md:text-4xl">GuacPanel</h1>
        </div>

        <div class="mx-auto mb-8 max-w-2xl">
          <p class="text-lg text-gray-700 dark:text-gray-200">
            A production-ready Laravel admin panel built with the VILT stack. Get enterprise-grade
            features out of the box.
          </p>
        </div>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
          <a
            href="/documentation/installation#installation"
            class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            [Start_Building]
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

    <!-- Core Features Section -->
    <section id="core-features" class="mb-12 scroll-mt-16">
      <div class="mb-8 text-center">
        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Core Features</h2>
        <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
          Everything you need to build modern, secure, and scalable admin applications
        </p>
      </div>

      <!-- Authentication Subsection -->
      <div class="mb-10">
        <h3 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">
          Authentication & Security
        </h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Laravel Fortify</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Secure authentication scaffolding with 2FA support
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Passwordless Login</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Magic link authentication for seamless access
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Laravel Socialite</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Social login with Google, Facebook, GitHub, and LinkedIn
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Role & Permissions</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Spatie permissions with visual management interface
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Password Policies</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Configurable password expiry and security rules
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Login History</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Track login attempts, sessions, and device info
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Activity Auditing</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Comprehensive audit logs with Spatie Activitylog
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Users Management</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Built in Users Management with fully loaded CRUD
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Management Subsection -->
      <div class="mb-10">
        <h3 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">Data Management</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">TanStack Table</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Advanced data tables with sorting, filtering & pagination
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Typesense Search</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Lightning-fast typo-tolerant search engine integration
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">ApexCharts</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Interactive charts and data visualizations
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">FilePond Upload</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Drag & drop file uploads with preview
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Spatie Backup</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Automated database and file backups
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Export Utilities</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                CSV, Excel, and PDF export capabilities
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- UI/UX Subsection -->
      <div class="mb-10">
        <h3 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">
          Interface & User Experience
        </h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Responsive Design</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Mobile-first design with Tailwind CSS
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Dark/Light Mode</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Automatic theme switching with system preference detection
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Color Themes</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Customizable color schemes to match your brand
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Flash Messages</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Beautiful toast notifications for user feedback
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Modal Components</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Reusable modal dialogs for confirmations and forms
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-700 dark:text-green-400">Form Components</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 font-mono text-sm text-gray-700 dark:text-gray-200">
                Pre-built form inputs with validation
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Technologies Section -->
    <section id="technologies" class="mb-12 scroll-mt-16">
      <div class="mb-8 text-center">
        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Tech Stack</h2>
        <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
          Built with modern technologies for optimal performance and developer experience
        </p>
      </div>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <div class="mb-3 flex items-center">
            <span class="mr-2 text-red-600 dark:text-red-500">⌾</span>
            <h4 class="font-bold text-gray-900 dark:text-gray-100">Laravel</h4>
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v12.x</span>
          </div>
          <p class="mb-3 text-sm text-gray-700 dark:text-gray-200">
            PHP framework providing the backend foundation
          </p>
          <div class="font-mono text-xs text-gray-500 dark:text-gray-500">
            [php] [backend] [framework]
          </div>
        </div>

        <div
          class="border border-gray-200 bg-white p-4 font-mono dark:border-gray-700 dark:bg-gray-900">
          <div class="mb-3 flex items-center">
            <span class="mr-2 text-green-600 dark:text-green-400">⌾</span>
            <h4 class="font-bold text-gray-900 dark:text-gray-100">Vue.js</h4>
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v3.x</span>
          </div>
          <p class="mb-3 text-sm text-gray-700 dark:text-gray-200">
            Progressive JavaScript framework for UI components
          </p>
          <div class="font-mono text-xs text-gray-500 dark:text-gray-500">
            [javascript] [frontend] [reactive]
          </div>
        </div>

        <div
          class="border border-gray-200 bg-white p-4 font-mono dark:border-gray-700 dark:bg-gray-900">
          <div class="mb-3 flex items-center">
            <span class="mr-2 text-purple-600 dark:text-purple-400">⌾</span>
            <h4 class="font-bold text-gray-900 dark:text-gray-100">Inertia.js</h4>
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v2.x</span>
          </div>
          <p class="mb-3 text-sm text-gray-700 dark:text-gray-200">
            The glue between Laravel and Vue for modern SPAs
          </p>
          <div class="font-mono text-xs text-gray-500 dark:text-gray-500">
            [spa] [bridge] [modern]
          </div>
        </div>

        <div
          class="border border-gray-200 bg-white p-4 font-mono dark:border-gray-700 dark:bg-gray-900">
          <div class="mb-3 flex items-center">
            <span class="mr-2 text-cyan-600 dark:text-cyan-400">⌾</span>
            <h4 class="font-bold text-gray-900 dark:text-gray-100">Tailwind CSS</h4>
            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v4.x</span>
          </div>
          <p class="mb-3 text-sm text-gray-700 dark:text-gray-200">
            Utility-first CSS framework for custom designs
          </p>
          <div class="font-mono text-xs text-gray-500 dark:text-gray-500">
            [css] [utility] [design]
          </div>
        </div>
      </div>
    </section>

    <!-- Getting Started Section -->
    <section id="getting-started" class="mb-12 scroll-mt-16">
      <div class="flex flex-col justify-center gap-4 sm:flex-row">
        <a
          href="/documentation/features"
          class="inline-flex items-center border border-gray-400 bg-white px-6 py-3 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400">
          <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7" />
          </svg>
          [Features]
        </a>

        <a
          href="/documentation/installation"
          class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
          [Installation_Guide]
          <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </section>
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

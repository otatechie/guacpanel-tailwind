<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import FeatureCard from '@/Components/FeatureCard.vue'
import TechCard from '@/Components/TechCard.vue'

defineOptions({
    layout: Public
})

const articleLinks = [
    { text: 'Introduction', href: '#introduction' },
    { text: 'Core Features', href: '#core-features' },
    { text: 'Technologies', href: '#technologies' },
    { text: 'Getting Started', href: '#getting-started' }
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

    <div id="introduction" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-900 border p-8 md:p-12 mb-12">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <h1 class="text-3xl md:text-4xl font-bold">GuacPanel</h1>
                </div>

                <div class="max-w-2xl mx-auto mb-8">
                    <p class="text-gray-700 dark:text-gray-200 text-lg">
                        A production-ready Laravel admin panel built with the VILT stack. Get
                        enterprise-grade features out of the box.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a
                        href="/documentation/installation#installation"
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                        <svg
                            class="w-5 h-5 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
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
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
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
        <section id="core-features" class="scroll-mt-16 mb-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Core Features</h2>
                <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                    Everything you need to build modern, secure, and scalable admin applications
                </p>
            </div>

            <!-- Authentication Subsection -->
            <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                    Authentication & Security
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Laravel Fortify
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Secure authentication scaffolding with 2FA support
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Passwordless Login
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Magic link authentication for seamless access
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Laravel Socialite
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Social login with Google, Facebook, GitHub, and LinkedIn
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Role & Permissions
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Spatie permissions with visual management interface
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Password Policies
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Configurable password expiry and security rules
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Login History
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Track login attempts, sessions, and device info
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Activity Auditing
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Comprehensive audit logs with Spatie Activitylog
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Users Management
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Built in Users Management with fully loaded CRUD
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Management Subsection -->
            <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                    Data Management
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                TanStack Table
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Advanced data tables with sorting, filtering & pagination
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Typesense Search
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Lightning-fast typo-tolerant search engine integration
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">ApexCharts</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Interactive charts and data visualizations
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                FilePond Upload
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Drag & drop file uploads with preview
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Spatie Backup
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Automated database and file backups
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Export Utilities
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                CSV, Excel, and PDF export capabilities
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UI/UX Subsection -->
            <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                    Interface & User Experience
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Responsive Design
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Mobile-first design with Tailwind CSS
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Dark/Light Mode
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Automatic theme switching with system preference detection
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Color Themes
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Customizable color schemes to match your brand
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Flash Messages
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Beautiful toast notifications for user feedback
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Modal Components
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Reusable modal dialogs for confirmations and forms
                            </p>
                        </div>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-700 dark:text-green-400 font-bold">
                                Form Components
                            </h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-200 text-sm mb-2 font-mono">
                                Pre-built form inputs with validation
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technologies Section -->
        <section id="technologies" class="scroll-mt-16 mb-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Tech Stack</h2>
                <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                    Built with modern technologies for optimal performance and developer experience
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                    <div class="flex items-center mb-3">
                        <span class="text-red-600 dark:text-red-500 mr-2">⌾</span>
                        <h4 class="text-gray-900 dark:text-gray-100 font-bold">Laravel</h4>
                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v12.x</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-sm mb-3">
                        PHP framework providing the backend foundation
                    </p>
                    <div class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                        [php] [backend] [framework]
                    </div>
                </div>

                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono">
                    <div class="flex items-center mb-3">
                        <span class="text-green-600 dark:text-green-400 mr-2">⌾</span>
                        <h4 class="text-gray-900 dark:text-gray-100 font-bold">Vue.js</h4>
                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v3.x</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-sm mb-3">
                        Progressive JavaScript framework for UI components
                    </p>
                    <div class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                        [javascript] [frontend] [reactive]
                    </div>
                </div>

                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono">
                    <div class="flex items-center mb-3">
                        <span class="text-purple-600 dark:text-purple-400 mr-2">⌾</span>
                        <h4 class="text-gray-900 dark:text-gray-100 font-bold">Inertia.js</h4>
                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v2.x</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-sm mb-3">
                        The glue between Laravel and Vue for modern SPAs
                    </p>
                    <div class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                        [spa] [bridge] [modern]
                    </div>
                </div>

                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono">
                    <div class="flex items-center mb-3">
                        <span class="text-cyan-600 dark:text-cyan-400 mr-2">⌾</span>
                        <h4 class="text-gray-900 dark:text-gray-100 font-bold">Tailwind CSS</h4>
                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">v4.x</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-sm mb-3">
                        Utility-first CSS framework for custom designs
                    </p>
                    <div class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                        [css] [utility] [design]
                    </div>
                </div>
            </div>
        </section>

        <!-- Getting Started Section -->
        <section id="getting-started" class="scroll-mt-16 mb-12">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="/documentation/features"
                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-900 border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300 hover:border-gray-600 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                    [Installation_Guide]
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        class="fixed bottom-8 right-8 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono text-sm p-3"
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

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

const mobileMenuOpen = ref(false)

const code = `git clone https://github.com/otatechie/guacpanel-tailwind
cd guacpanel-tailwind && composer install
php artisan migrate --seed
npm install && npm run dev`

const copied = ref(false)
const stars = ref(0)
const showBackToTop = ref(false)

function copyCode() {
    navigator.clipboard.writeText(code)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500
}

onMounted(async () => {
    const response = await fetch('https://api.github.com/repos/otatechie/guacpanel-tailwind')
    const data = await response.json()
    stars.value = data.stargazers_count
    
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>

    <Head title="GuacPanel - Open Source Admin" />
    <div
        class="min-h-screen bg-gradient-to-br from-green-50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 flex flex-col">
        <header
            class="w-full border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm sticky top-0 z-50 py-1 sm:py-2"
            role="banner">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-2">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" class="h-8 sm:h-10 w-auto block dark:hidden" alt="Logo" />
                    <img src="/images/logo-dark.png" class="h-8 sm:h-10 w-auto hidden dark:block" alt="Logo Dark" />
                </div>
                <button 
                    class="md:hidden flex items-center px-2 py-1 border border-gray-500 dark:border-gray-500 text-gray-800 dark:text-gray-200"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    aria-label="Toggle mobile menu">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <nav class="hidden md:flex gap-8 text-gray-700 dark:text-gray-200 text-sm font-medium"
                    aria-label="Main navigation">
                    <a href="#features" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        Features
                    </a>
                    <a href="/documentation" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        Documentation
                    </a>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="/login"
                        class="inline-flex items-center px-3 sm:px-4 py-2 bg-green-500/20 border border-green-400 text-green-400 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-xs"
                        aria-label="View demo">
                        [Demo]
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-2 border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300 hover:border-gray-600 dark:hover:border-gray-400 transition-all font-mono text-xs"
                        aria-label="View on GitHub">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.237 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ stars }}</span>
                    </a>
                </div>
            </div>
            <!-- Mobile menu -->
            <div v-show="mobileMenuOpen" class="md:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="px-4 py-3 space-y-3">
                    <a href="#features" class="block text-gray-700 dark:text-gray-200 hover:text-teal-700 dark:hover:text-teal-300 transition-colors font-mono text-sm">
                        [Features]
                    </a>
                    <a href="/documentation" class="block text-gray-600 dark:text-gray-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors font-mono text-sm">
                        [Documentation]
                    </a>
                </div>
            </div>
        </header>

        <section
            class="relative flex-1 flex flex-col items-center justify-center text-center px-4 sm:px-8 md:px-12 py-12 sm:py-16 md:py-20 overflow-hidden"
            role="region" aria-labelledby="hero-heading">
            <div
                class="absolute inset-0 bg-gradient-to-br from-teal-50/50 via-white to-white dark:from-teal-900/20 dark:via-gray-900 dark:to-gray-900 -z-10">
            </div>
            <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5 dark:opacity-10 -z-10"></div>
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-teal-100 dark:bg-teal-800 rounded-full blur-3xl opacity-20">
            </div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-100 dark:bg-green-800 rounded-full blur-3xl opacity-20">
            </div>

            <div class="relative">
                <h1 id="hero-heading" class="text-4xl md:text-6xl font-bold text-gray-900 px-2 dark:text-white mb-6">
                    Ready to ship
                    <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-teal-800 to-green-600 dark:from-teal-300 dark:to-green-300">
                        faster
                    </span>
                    ?
                </h1>

                <p class="text-lg md:text-xl text-gray-700 dark:text-gray-200 px-2 mb-4 max-w-2xl mx-auto">
                    Build amazing Laravel applications with ease. GuacPanel provides ready-to-use
                    features like authentication, permissions, backup management, and auditing - all
                    with beautiful UI components.
                    <span aria-hidden="true" class="inline-block animate-bounce">🚀</span>
                </p>

                <p class="font-mono text-[13px] tracking-tight text-gray-400 dark:text-gray-500 mb-8 space-x-3">
                    <span class="opacity-75">PHP 8.2+</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Laravel 12</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Vue 3</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Inertia 2.0</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Tailwind CSS 4+</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                    <a href="/login"
                        class="inline-flex items-center w-full sm:w-auto justify-center px-6 py-4 sm:py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        [View_Demo]
                    </a>
                    <a href="/documentation"
                        class="inline-flex items-center w-full sm:w-auto justify-center px-6 py-4 sm:py-3 bg-white dark:bg-gray-900 border border-gray-500 dark:border-gray-500 text-gray-800 dark:text-gray-200 hover:border-gray-700 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm">
                        [Documentation]
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="border border-green-500/30 dark:border-green-500/20 bg-gray-900 dark:bg-gray-950 p-0 max-w-3xl w-full mx-auto text-left font-mono text-xs sm:text-sm overflow-x-auto"
                    role="region" aria-label="Installation commands">
                    <div
                        class="flex items-center justify-between px-4 py-3 bg-gray-800 dark:bg-gray-900 border-b border-green-500/20 dark:border-green-800/20">
                        <div class="flex items-center gap-3">
                            <span class="text-green-700 dark:text-green-200 text-xs font-medium">
                                Terminal — bash
                            </span>
                        </div>
                        <button
                            class="px-2 sm:px-3 py-2 sm:py-1 text-xs bg-green-500/10 border border-green-500/50 text-green-700 dark:text-green-200 hover:bg-green-500/20 transition-all font-mono uppercase tracking-wider"
                            aria-label="Copy installation commands" @click="copyCode">
                            <svg v-if="!copied" class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg v-else class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-if="!copied">[COPY]</span>
                            <span v-else>[COPIED]</span>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-green-700 dark:text-green-200 mb-2">
                            <span class="text-green-700 dark:text-green-200">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-green-700 dark:text-green-200">~</span>
                            <span class="ml-1">$</span>
                        </div>
                        <pre
                            class="whitespace-pre-wrap text-white dark:text-white leading-relaxed pl-6 border-l border-green-300/30 dark:border-green-500/30 ml-2">{{ code }}</pre>
                        <div class="flex items-center text-green-700 dark:text-green-200 mt-4">
                            <span class="text-green-700 dark:text-green-200">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-green-700 dark:text-green-200">~</span>
                            <span class="ml-1">$</span>
                            <span class="ml-2 animate-pulse">▊</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 relative overflow-hidden" role="region" aria-labelledby="why-heading">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
            </div>
            <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5"></div>

            <div class="max-w-7xl mx-auto px-4 relative">
                <div class="max-w-3xl mx-auto">
                    <div class="text-center mb-8">
                        <h2 id="why-heading" class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            Why GuacPanel?
                        </h2>
                        <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                            A simple, clean dashboard with just the essentials
                        </p>
                    </div>

                    <div
                        class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-6 font-mono">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    Every Laravel project starts the same way: auth, user management, security, 2FA,
                                    backups, activity logs—you've built it all before.
                                </p>
                            </div>
                            <div class="flex items-start">
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    Most dashboards try to do everything. You just need one that gets out of the way and
                                    lets you build.
                                </p>
                            </div>
                            <div class="flex items-start">
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    That's why I made GuacPanel: open-source, no bloat, no subscriptions. Just the
                                    essentials developers actually need.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features"
            class="py-16 sm:py-20 md:py-24 bg-gradient-to-br from-slate-50 via-white to-teal-50/50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
            role="region" aria-labelledby="features-heading">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 id="features-heading"
                        class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-800 to-teal-600 dark:from-teal-300 dark:to-teal-200 mb-4">
                        Powerful Features
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Everything you need to build modern admin panels and dashboards with Laravel
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">TanStack Table</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Powerful table features using TanStack Table for data management with sorting, filtering, and pagination.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">FilePond Upload</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Modern file uploads using FilePond with image preview support and drag-and-drop functionality.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Spatie Backup</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Integrated Laravel backup system with UI management interface for database and files.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">UI Components</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Beautiful and responsive components with dark mode support and accessibility features.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Spatie Permissions</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Role-based access control using Spatie's Laravel-permission with UI management interface.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Session Management</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Secure browser session handling with device tracking and remote logout capabilities.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Passwordless Login</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Secure magic link and one-time code authentication with device verification.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Apex Charts</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Beautiful, interactive chart components including line, bar, area, donut, and more for data visualization.
                            </p>
                        </div>
                    </div>

                    <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-3 sm:p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                            <h4 class="text-green-800 dark:text-green-200 font-bold">Typesense Search</h4>
                        </div>
                        <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                Lightning-fast, typo-tolerant search functionality with Typesense integration.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 bg-gradient-to-br from-teal-600 to-teal-700 dark:from-teal-900 dark:to-teal-800"
            role="region" aria-labelledby="cta-heading">
            <div class="max-w-7xl mx-auto px-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-10"></div>
                    <div class="relative z-10 text-center">
                        <h2 id="cta-heading" class="text-3xl md:text-4xl font-bold text-white mb-6">
                            Ready to Build Something Amazing?
                        </h2>
                        <p class="text-xl text-teal-100 dark:text-teal-200 mb-8 max-w-2xl mx-auto">
                            Start your next Laravel project with GuacPanel; secure, modern, and ready to use.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/login"
                                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-900 border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300 hover:border-gray-600 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm"
                                aria-label="View demo application">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                [View_Demo]
                            </a>
                            <a href="/documentation"
                                class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-400 text-green-400 dark:text-green-300 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm"
                                aria-label="Read documentation">
                                [Documentation]
                                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="py-24 bg-gradient-to-br from-slate-50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
            role="region" aria-labelledby="faq-heading">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 id="faq-heading" class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Frequently Asked Questions
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Everything you need to know about GuacPanel
                    </p>
                </div>

                <div class="max-w-3xl mx-auto">
                    <div class="space-y-6">
                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">What is GuacPanel?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    An open-source Laravel 12 dashboard starter kit with essentials like 2FA, password
                                    rules, backups, logs, and user management—built with Vue 3, Tailwind, and Inertia.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Why did you build GuacPanel?
                                </h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    To avoid rebuilding the same security and user features in every project. Most
                                    dashboards felt bloated or overpriced - I wanted something simple and extendable.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Is GuacPanel production-ready?
                                </h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    Yes. It has built-in 2FA, password rules, backups, and logs for real-world apps.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Does GuacPanel include
                                    bloatware?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    No. GuacPanel only includes essential packages. You can remove any feature without
                                    breaking the app.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Can I remove or swap packages?
                                </h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    Yes. GuacPanel is modular - you can remove features like backups or permissions
                                    without
                                    breaking the app.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Does GuacPanel replace
                                    Jetstream or Fortify?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    No. GuacPanel builds on Fortify and ships a ready Vue + Inertia frontend, while
                                    Jetstream comes with its own stack.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Is GuacPanel free?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    Yes, 100% open-source under the MIT license.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Where can I contribute, request
                                    features or report issues?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    On the
                                    <a href="https://github.com/otatechie/guacpanel-tailwind"
                                        class="text-green-600 dark:text-green-500 hover:text-green-700 dark:hover:text-green-400 transition-colors underline underline-offset-2"
                                        aria-label="Visit GitHub repo">
                                        GitHub
                                    </a>
                                    repo, feel free to open issues, request features or submit pull
                                    requests!
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Is GuacPanel actively
                                    maintained?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    Yes. It's kept up to date with Laravel releases, security fixes, and community
                                    contributions.
                                </p>
                            </div>
                        </div>

                        <div
                            class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
                            <div class="flex items-start mb-2">
                                <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                                <h4 class="text-green-800 dark:text-green-200 font-bold">Does GuacPanel include CRUD
                                    generators?</h4>
                            </div>
                            <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                                    No. GuacPanel handles app essentials; you can add CRUD generators separately.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-8" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#features"
                        class="inline-flex items-center w-full sm:w-auto justify-center px-6 py-4 sm:py-3 bg-white dark:bg-gray-900 border border-gray-500 dark:border-gray-500 text-gray-800 dark:text-gray-200 hover:border-gray-700 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        [Features]
                    </a>

                    <a href="/documentation"
                        class="inline-flex items-center w-full sm:w-auto justify-center px-6 py-4 sm:py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                        [Documentation]
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                
                <div class="flex justify-center mt-8 text-sm text-gray-500 dark:text-gray-400">
                    <span>
                        Made with
                        <span class="text-red-500 dark:text-red-400" role="img" aria-label="love">
                            ♡
                        </span>
                        in
                        <a href="https://github.com/otatechie"
                            class="text-green-600 dark:text-green-500 hover:text-green-700 dark:hover:text-green-400 transition-colors underline underline-offset-2"
                            aria-label="Visit otatechie's GitHub profile">
                            Accra
                        </a>
                    </span>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button v-show="showBackToTop"
            class="fixed bottom-4 sm:bottom-8 right-4 sm:right-8 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono text-sm p-3 z-50"
            aria-label="Back to top" @click="scrollToTop">
            [^]
        </button>
    </div>
</template>

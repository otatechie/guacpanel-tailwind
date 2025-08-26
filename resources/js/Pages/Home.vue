<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const code = `git clone https://github.com/otatechie/guacpanel-tailwind
cd guacpanel-tailwind && composer install
php artisan migrate --seed
npm install && npm run dev`

const copied = ref(false)
const stars = ref(0)

function copyCode() {
    navigator.clipboard.writeText(code)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
}

onMounted(async () => {
    const response = await fetch('https://api.github.com/repos/otatechie/guacpanel-tailwind')
    const data = await response.json()
    stars.value = data.stargazers_count
})
</script>

<template>

    <Head title="GuacPanel - Open Source Admin" />
    <div
        class="min-h-screen bg-gradient-to-br from-green-50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 flex flex-col">
        <header
            class="w-full border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm sticky top-0 z-50"
            role="banner">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-2">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" class="h-10 w-auto block dark:hidden" alt="Logo" />
                    <img src="/images/logo-dark.png" class="h-10 w-auto hidden dark:block" alt="Logo Dark" />
                </div>
                <nav class="hidden md:flex gap-8 text-gray-600 dark:text-gray-300 text-sm font-medium"
                    aria-label="Main navigation">
                    <a href="#features"
                        class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Features</a>
                    <a href="/documentation"
                        class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Documentation</a>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="/login"
                        class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-teal-600 dark:bg-teal-500 text-white text-sm font-medium rounded-lg hover:bg-teal-700 dark:hover:bg-teal-600 transition-colors"
                        aria-label="View demo">
                        View Demo
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="View on GitHub">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.237 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ stars }}</span>
                    </a>
                </div>
            </div>
        </header>

        <section
            class="relative flex-1 flex flex-col items-center justify-center text-center px-12 py-20 overflow-hidden"
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
                        class="bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-green-500 dark:from-teal-400 dark:to-green-400">faster</span>?
                </h1>

                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 px-2 mb-4 max-w-2xl mx-auto">
                    Build amazing Laravel applications with ease. GuacPanel provides ready-to-use features like
                    authentication, permissions, backup management, and auditing - all with beautiful UI components.
                    <span aria-hidden="true" class="inline-block animate-bounce">🚀</span>
                </p>

                <p class="font-mono text-[13px] tracking-tight text-gray-400 dark:text-gray-500 mb-8 space-x-3">
                    <span class="opacity-75">PHP 8.2+</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Laravel 11</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Vue 3</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Inertia 2.0</span>
                    <span class="opacity-50">/</span>
                    <span class="opacity-75">Tailwind CSS 4+</span>
                </p>

                <div class="flex flex-wrap justify-center gap-4 mb-16">
                    <a href="/login"
                        class="btn-primary inline-flex items-center gap-2 text-base bg-teal-600 dark:bg-teal-500 text-white px-6 py-3 rounded-lg hover:bg-teal-700 dark:hover:bg-teal-600 transition-colors"
                        aria-label="View demo application">
                        View demo
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="/documentation"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                        aria-label="Read documentation">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Read documentation
                    </a>
                </div>

                <div class="relative bg-slate-900 dark:bg-slate-950 border border-slate-700 dark:border-slate-800 rounded-2xl shadow-2xl p-0 max-w-3xl w-full mx-auto text-left font-mono text-sm overflow-hidden"
                    role="region" aria-label="Installation commands">
                    <div
                        class="flex items-center justify-between px-4 py-3 bg-slate-800 dark:bg-slate-900 border-b border-slate-700 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-slate-400 dark:text-slate-500 text-xs font-medium">Terminal — bash</span>
                        </div>
                        <button @click="copyCode"
                            class="px-3 py-1 text-xs rounded-md bg-slate-700 dark:bg-slate-800 text-slate-300 hover:bg-slate-600 dark:hover:bg-slate-700 transition flex items-center gap-2"
                            aria-label="Copy installation commands">
                            <svg v-if="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-if="!copied">Copy</span>
                            <span v-else>Copied!</span>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-slate-400 dark:text-slate-500 mb-2">
                            <span class="text-teal-400 dark:text-teal-500">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-blue-400 dark:text-blue-500">~</span>
                            <span class="ml-1">$</span>
                        </div>
                        <pre
                            class="whitespace-pre-wrap text-slate-200 dark:text-slate-300 leading-relaxed">{{ code }}</pre>
                        <div class="flex items-center text-slate-400 dark:text-slate-500 mt-4">
                            <span class="text-teal-400 dark:text-teal-500">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-blue-400 dark:text-blue-500">~</span>
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
                    <div class="text-center mb-12">
                        <h2 id="why-heading"
                            class="text-4xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 mb-4">
                            Why GuacPanel?
                        </h2>
                        <div class="h-1 w-20 bg-gradient-to-r from-teal-500 to-green-500 mx-auto rounded-full mb-8">
                        </div>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            A simple, clean dashboard with just the essentials
                        </p>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 md:p-12 backdrop-blur-sm bg-opacity-80 dark:bg-opacity-80">
                        <div class="space-y-6 text-lg">
                            <p class="text-gray-600 dark:text-gray-300">
                                Every Laravel project starts the same way. You know the drill; authentication, user
                                management, security features, 2FA setup, password policies, backup systems, activity
                                logs. You've built these a dozen times before, and you'll probably build them a dozen
                                times more.
                            </p>
                            <p class="text-gray-600 dark:text-gray-300">
                                Most dashboards try to be everything at once, when you just need something that gets out
                                of your way and lets you build.
                            </p>
                            <p class="text-gray-600 dark:text-gray-300">
                                That's why I made GuacPanel: open-source, no bloat, no subscriptions, just the essential
                                features developers actually need. Because sometimes, less is more.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features"
            class="py-24 bg-gradient-to-br from-slate-50 via-white to-teal-50/50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
            role="region" aria-labelledby="features-heading">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 id="features-heading"
                        class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-teal-500 dark:from-teal-400 dark:to-teal-300 mb-4">
                        Powerful Features</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">Everything you need to build
                        modern admin panels and dashboards with Laravel</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-teal-100 dark:hover:border-teal-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 dark:from-teal-600 dark:to-teal-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Authentication Logs</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Track login attempts, IP
                            addresses, and suspicious activities in real-time.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-purple-100 dark:hover:border-purple-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">TanStack Table</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Powerful table features
                            using
                            TanStack Table for data management.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-blue-100 dark:hover:border-blue-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">FilePond Upload</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Modern file uploads using
                            FilePond with image preview support.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-green-100 dark:hover:border-green-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Spatie Backup</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Integrated Laravel backup
                            system with
                            UI management interface.</p>
                    </div>


                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-orange-100 dark:hover:border-orange-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Laravel Auditing</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Model changes and user
                            actions tracked
                            using Laravel Auditing.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-pink-100 dark:hover:border-pink-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 dark:from-pink-600 dark:to-pink-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">UI Components</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Beautiful and responsive
                            components with dark mode support.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-indigo-100 dark:hover:border-indigo-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Spatie Permissions</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Role-based access control
                            using
                            Spatie's Laravel-permission with UI management interface.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-rose-100 dark:hover:border-rose-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 dark:from-rose-600 dark:to-rose-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Session Management</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Secure browser session
                            handling with device tracking and remote logout.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-amber-100 dark:hover:border-amber-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Passwordless Login</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Secure magic link and
                            one-time code authentication with device verification.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-cyan-100 dark:hover:border-cyan-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-cyan-600 dark:from-cyan-600 dark:to-cyan-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Apex Charts</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Beautiful, interactive chart components including line, bar, area, donut, and more for comprehensive data visualization.</p>
                    </div>

                    <div class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-emerald-100 dark:hover:border-emerald-900 transition-all duration-300"
                        role="listitem">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Typesense Search</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">Lightning-fast, typo-tolerant search functionality with Typesense integration for an exceptional search experience.</p>
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
                        <h2 id="cta-heading" class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Build
                            Something Amazing?</h2>
                        <p class="text-xl text-teal-100 dark:text-teal-200 mb-8 max-w-2xl mx-auto">Start building your
                            next project with GuacPanel's powerful features and modern UI components.</p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="/login"
                                class="inline-flex items-center px-6 py-3 rounded-lg bg-white dark:bg-gray-800 text-teal-600 dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-700 transition-colors font-medium"
                                aria-label="View demo application">
                                View Demo
                                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="/documentation"
                                class="inline-flex items-center px-6 py-3 rounded-lg bg-teal-500 dark:bg-teal-600 text-white hover:bg-teal-400 dark:hover:bg-teal-500 transition-colors font-medium"
                                aria-label="Read documentation">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Read Documentation
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
                    <h2 id="faq-heading" class="text-4xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Frequently Asked Questions
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Everything you need to know about GuacPanel
                    </p>
                </div>

                <div class="max-w-3xl mx-auto">
                    <div class="space-y-8">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">1.</span>
                                    <span>What is GuacPanel?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                GuacPanel is an open-source, security-focused dashboard starter kit for Laravel 11. It
                                includes essential features like 2FA, password policies, backups, activity logs, and
                                user management, all built with Vue 3, Tailwind CSS, and Inertia.js.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">2.</span>
                                    <span>Why did you build GuacPanel?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Because I was tired of rebuilding the same security and user management features in
                                every Laravel project. Existing dashboards felt bloated, overly complex, or expensive,
                                I wanted something simple, clean, and easy to extend.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">3.</span>
                                    <span>Is GuacPanel production-ready?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Yes, it includes production-level features like 2FA enforcement, password expiry, backup
                                management, and detailed logs, things you would otherwise have to build or integrate
                                manually.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">4.</span>
                                    <span>Does GuacPanel include bloatware?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                No. GuacPanel only includes carefully chosen packages that solve real problems most
                                Laravel apps need, like Fortify (authentication), Spatie (backups, permissions), and
                                Filepond (file uploads). If you don't want a feature, you can remove it without breaking
                                the rest.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">5.</span>
                                    <span>Can I remove or swap packages?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Yes. Everything in GuacPanel is modular. Don't need backups or permissions? Remove the
                                related packages without breaking the app.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">6.</span>
                                    <span>Does GuacPanel replace Jetstream or Fortify?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                No, GuacPanel uses Fortify under the hood but provides a complete, ready-to-use
                                frontend with Inertia and Vue. Jetstream, on the other hand, comes with its own
                                scaffolding and stack opinions.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">7.</span>
                                    <span>Is GuacPanel free?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Yes, 100% open-source under the MIT license.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">8.</span>
                                    <span>Where can I contribute, request features or report issues?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                On the <a href="https://github.com/otatechie/guacpanel-tailwind"
                                    class="text-teal-600 dark:text-teal-500 hover:text-teal-700 dark:hover:text-teal-400 transition-colors underline underline-offset-2"
                                    aria-label="Visit GitHub repo">GitHub</a> repo, feel free to open
                                issues, request features or submit pull requests!
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">9.</span>
                                    <span>Is GuacPanel actively maintained?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Yes. We plan to keep GuacPanel updated with the latest Laravel releases, security
                                patches, and community-driven improvements.
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                <span class="flex items-baseline gap-3">
                                    <span class="text-teal-600 dark:text-teal-500">10.</span>
                                    <span>Does GuacPanel include CRUD generators?</span>
                                </span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                No, GuacPanel focuses on security, user management, and app essentials. If you want
                                CRUD generators, you can integrate them separately.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-8" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4">
                <div
                    class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 text-sm text-gray-500 dark:text-gray-400">
                    <nav class="flex items-center gap-4 md:gap-8" aria-label="Footer navigation">
                        <a href="#features" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"
                            aria-label="View features">Features</a>
                        <a href="/documentation" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"
                            aria-label="Read documentation">Documentation</a>
                        <a href="https://github.com/otatechie/guacpanel-tailwind"
                            class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"
                            aria-label="View source on GitHub">GitHub</a>
                    </nav>
                    <span class="hidden md:block text-gray-800 dark:text-gray-600" aria-hidden="true">·</span>
                    <span>
                        Made with <span class="text-red-500 dark:text-red-400" role="img" aria-label="love">♡</span> in
                        <a href="https://github.com/otatechie"
                            class="text-gray-500 font-medium hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300 transition-colors underline underline-offset-2"
                            aria-label="Visit otatechie's GitHub profile">Accra</a>
                    </span>
                </div>
            </div>
        </footer>
    </div>
</template>

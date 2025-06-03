<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Logo from '../Components/Logo.vue'

const code = `git clone https://github.com/otatechie/guacpanel-tailwind
cd guacpanel-tailwind && composer install
npm install && npm run dev
php artisan migrate --seed`

const copied = ref(false)
function copyCode() {
    navigator.clipboard.writeText(code)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
}
</script>

<template>

    <Head title="GuacPanel - Open Source Admin" />
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-white flex flex-col">
        <!-- Header -->
        <header class="w-full border-b border-gray-100 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-2">
                <div class="flex items-center gap-3">
                    <Logo size="2.5rem" class="text-teal-600" />
                </div>
                <nav class="hidden md:flex gap-8 text-gray-600 text-sm font-medium">
                    <a href="#features" class="hover:text-teal-600 transition-colors">Features</a>
                    <a href="/documentation" class="hover:text-teal-600 transition-colors">Documentation</a>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="/login" class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                        View Demo
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="GitHub">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <!-- Mobile Menu Button -->
                    <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative flex-1 flex flex-col items-center justify-center text-center px-4 py-20 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50/50 via-white to-white -z-10"></div>
            <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5 -z-10"></div>
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-teal-100 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-100 rounded-full blur-3xl opacity-20"></div>

            <!-- Hero Content -->
            <div class="relative">                
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 mb-6">
                    Ready to ship
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-green-500">faster</span>?
                </h1>
                
                <p class="text-lg md:text-xl text-gray-600 mb-4 max-w-2xl mx-auto">
                    You're already building with Laravel. GuacPanel helps you ship features faster and more efficiently.
                    <span aria-hidden="true" class="inline-block animate-bounce">🚀</span>
                </p>
                
                <p class="font-mono text-[13px] tracking-tight text-gray-400/90 mb-8 space-x-3">
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
                
                <p class="text-base text-gray-500 mb-12">
                    Open source. Modern. Beautiful.
                    <span class="text-teal-500 font-semibold">Try the demo!</span>
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap justify-center gap-4 mb-16">
                    <a href="/login" class="btn-primary inline-flex items-center gap-2 text-base">
                        View Demo
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
            <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                        View on GitHub
                    </a>
                </div>

                <!-- Terminal Preview -->
                <div class="relative bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-0 max-w-3xl w-full mx-auto text-left font-mono text-sm overflow-hidden">
                    <!-- Terminal Header -->
                    <div class="flex items-center justify-between px-4 py-3 bg-slate-800 border-b border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-slate-400 text-xs font-medium">Terminal — bash — 80×24</span>
                </div>
                <button @click="copyCode"
                            class="px-3 py-1 text-xs rounded-md bg-slate-700 text-slate-300 hover:bg-slate-600 transition flex items-center gap-2">
                            <svg v-if="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                            <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-if="!copied">Copy</span>
                    <span v-else>Copied!</span>
                </button>
                    </div>
                    <!-- Terminal Content -->
                    <div class="p-6">
                        <div class="flex items-center text-slate-400 mb-2">
                            <span class="text-teal-400">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-blue-400">~</span>
                            <span class="ml-1">$</span>
                        </div>
                        <pre class="whitespace-pre-wrap text-slate-200 leading-relaxed">{{ code }}</pre>
                        <div class="flex items-center text-slate-400 mt-4">
                            <span class="text-teal-400">user@guacpanel</span>
                            <span class="mx-1">:</span>
                            <span class="text-blue-400">~</span>
                            <span class="ml-1">$</span>
                            <span class="ml-2 animate-pulse">▊</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-gradient-to-br from-slate-50 via-white to-teal-50/50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-teal-500 mb-4">Powerful Features</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Everything you need to build modern admin panels and dashboards with Laravel</p>
                </div>

                <!-- Feature Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Security -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-teal-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Authentication Logs</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Track login attempts, IP addresses, and suspicious activities in real-time.</p>
                    </div>

                    <!-- Data Tables -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-purple-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Smart Data Tables</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Feature-rich tables with sorting, filtering, and export capabilities.</p>
                    </div>

                    <!-- File Management -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">File Management</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Drag-and-drop file uploads with preview and validation support.</p>
                    </div>

                    <!-- Backup System -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-green-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Automated Backups</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Scheduled backups with multiple storage locations and monitoring.</p>
                    </div>

                    <!-- Activity Tracking -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-orange-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Activity Tracking</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Comprehensive audit logging for all user actions and changes.</p>
                    </div>

                    <!-- UI Components -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-pink-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">UI Components</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Beautiful and responsive components with dark mode support.</p>
                    </div>

                    <!-- API Integration -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-indigo-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Role-based Access</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Fine-grained permissions with hierarchical roles and team management.</p>
                    </div>

                    <!-- Browser Sessions -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-rose-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-rose-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Session Management</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Secure browser session handling with device tracking and remote logout.</p>
                    </div>

                    <!-- Notifications -->
                    <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-amber-100 transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Passwordless Login</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Secure magic link and one-time code authentication with device verification.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-24 bg-gradient-to-br from-teal-600 to-teal-700">
            <div class="max-w-7xl mx-auto px-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-10"></div>
                    <div class="relative z-10 text-center">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Build Something Amazing?</h2>
                        <p class="text-xl text-teal-100 mb-8 max-w-2xl mx-auto">Start building your next admin panel with GuacPanel's powerful features and modern UI components.</p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="/login" class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-teal-600 hover:bg-teal-50 transition-colors font-medium">
                                View Demo
                                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank" class="inline-flex items-center px-6 py-3 rounded-lg bg-teal-500 text-white hover:bg-teal-400 transition-colors font-medium">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                                </svg>
                                View on GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 text-sm text-gray-500">
                    <div class="flex items-center gap-4 md:gap-8">
                        <a href="#features" class="hover:text-teal-600 transition-colors">Features</a>
                        <a href="/documentation" class="hover:text-teal-600 transition-colors">Documentation</a>
                        <a href="https://github.com/otatechie/guacpanel-tailwind" class="hover:text-teal-600 transition-colors">GitHub</a>
                    </div>
                    <span class="hidden md:block text-gray-400">·</span>
                    <span>Made with <span class="text-red-500">❤️</span> in Accra by <a href="https://github.com/otatechie" class="text-teal-600 hover:text-teal-700 transition-colors underline">otatechie</a></span>
                </div>
            </div>
        </footer>
    </div>
</template>

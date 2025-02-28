<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'

defineOptions({
    layout: Public
})

const searchQuery = ref('')

// Define navigation links based on the sections in your page
const articleLinks = [
    { text: 'Introduction', href: '#introduction' },
    { text: 'Authentication', href: '#authentication' },
    { text: 'Interface & Layout', href: '#interface' },
    { text: 'Data Management', href: '#data-management' },
    { text: 'Technologies', href: '#technologies' }
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
    <Head title="Obomaa Documentation" />

    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Getting Started</h2>

        <div class="relative overflow-hidden rounded-2xl mb-6 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900">
            <div class="relative z-10 p-8">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-2">Introduction</h2>
                <p class="text-gray-600 max-w-3xl mb-6">
                    Obomaa is designed for developers who need a robust admin interface without unnecessary complexity.
                    By leveraging the best of Laravel's backend capabilities, Vue's reactive components, Inertia's
                    seamless page transitions, and Tailwind's utility-first styling, Obomaa offers a premium admin
                    experience that's easy to extend and customize.
                </p>
                <p class="text-gray-600 max-w-3xl mb-8">
                    Whether you're building a content management system, e-commerce platform, or analytics dashboard,
                    Obomaa provides the building blocks to get you started quickly without sacrificing flexibility or
                    performance.
                </p>
                <div class="grid gap-6 md:grid-cols-3 text-gray-600">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 flex-shrink-0 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold mb-1">Developer Focused</h3>
                            <p class="text-sm text-gray-600">Built for extensibility</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 flex-shrink-0 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <div>
                            <h3 class="font-semibold mb-1">Premium Experience</h3>
                            <p class="text-sm text-gray-600">Seamless and reactive</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 flex-shrink-0 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold mb-1">Versatile Foundation</h3>
                            <p class="text-sm text-gray-600">CMS, e-commerce & more</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/20 dark:to-indigo-900/20 blur-3xl rounded-full"></div>
            <div class="absolute left-0 bottom-0 -mb-4 -ml-4 h-32 w-32 bg-gradient-to-tr from-purple-100 to-indigo-100 dark:from-purple-900/20 dark:to-indigo-900/20 blur-2xl rounded-full"></div>
        </div>

        <div class="border-t border-gray-200 py-2"></div>

        <!-- Features Overview -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Features</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Obomaa provides a solid foundation for building admin interfaces with Laravel, Vue, Inertia, and
                Tailwind CSS:
            </p>
        </div>

        <!-- Core Features -->
        <div class="space-y-12">
            <!-- Authentication Section -->
            <section id="authentication" class="scroll-mt-16">
                <div class="relative">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white">Authentication</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Authentication Items -->
                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Secure Login
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Based on Laravel's authentication
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Passwordless Login
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Streamlined authentication without password
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Role-based Permissions
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Control access with customizable roles
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Password Security Policies
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Configure password expiry and changes
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Authentication Logs
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Track login attempts and sessions
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Interface & Layout Section -->
            <section id="interface" class="scroll-mt-16">
                <div class="relative">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white">Interface & Layout</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Interface Items -->
                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Responsive Layout
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Mobile-friendly interface with Tailwind
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Dark/Light Mode Toggle
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Switch between themes for viewing
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Dashboard Overview
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Customizable home page
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Data Management Section -->
            <section id="data-management" class="scroll-mt-16">
                <div class="relative">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm8 13v-6m-8-4h16" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-800 dark:text-white">Data Management</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data Management Items -->
                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Data Tables
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Interactive tables with sorting
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Advanced Filtering
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Find exactly what you need quickly
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3v-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            FilePond Integration
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Drag and drop file uploads
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                            Backup System
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            Automated backup functionality
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technologies & Packages Section -->
            <section id="technologies" class="scroll-mt-16">
                <div class="relative mt-16">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Technologies & Packages</h3>
                                <p class="text-gray-600 mt-2">Obomaa leverages these powerful open-source packages</p>
                            </div>
                        </div>

                        <!-- Core Stack -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Core Stack</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-8 h-8">
                                                    <path fill="#FF2D20" d="M504.4,115.83a5.72,5.72,0,0,0-.28-.68,8.52,8.52,0,0,0-.53-1.25,6,6,0,0,0-.54-.71,9.36,9.36,0,0,0-.72-.94c-.23-.22-.52-.4-.77-.6a8.84,8.84,0,0,0-.9-.68L404.4,55.55a8,8,0,0,0-8,0L300.12,111h0a8.07,8.07,0,0,0-.88.69,7.68,7.68,0,0,0-.78.6,8.23,8.23,0,0,0-.72.93c-.17.24-.39.45-.54.71a9.7,9.7,0,0,0-.52,1.25c-.08.23-.21.44-.28.68a8.08,8.08,0,0,0-.28,2.08V223.18l-80.22,46.19V63.44a7.8,7.8,0,0,0-.28-2.09c-.06-.24-.2-.45-.28-.68a8.35,8.35,0,0,0-.52-1.24c-.14-.26-.37-.47-.54-.72a9.36,9.36,0,0,0-.72-.94,9.46,9.46,0,0,0-.78-.6,9.8,9.8,0,0,0-.88-.68h0L115.61,1.07a8,8,0,0,0-8,0L11.34,56.49h0a6.52,6.52,0,0,0-.88.69,7.81,7.81,0,0,0-.79.6,8.15,8.15,0,0,0-.71.93c-.18.25-.4.46-.55.72a7.88,7.88,0,0,0-.51,1.24,6.46,6.46,0,0,0-.29.67,8.18,8.18,0,0,0-.28,2.1v329.7a8,8,0,0,0,4,6.95l192.5,110.84a8.83,8.83,0,0,0,1.33.54c.21.08.41.2.63.26a7.92,7.92,0,0,0,4.1,0c.2-.05.37-.16.55-.22a8.6,8.6,0,0,0,1.4-.58L404.4,400.09a8,8,0,0,0,4-6.95V287.88l92.24-53.11a8,8,0,0,0,4-7V117.92A8.63,8.63,0,0,0,504.4,115.83ZM111.6,17.28h0l80.19,46.15-80.2,46.18L31.41,63.44Zm88.25,60V278.6l-46.53,26.79-33.69,19.4V123.5l46.53-26.79Zm0,412.78L23.37,388.5V77.32L57.06,96.7l46.52,26.8V338.68a6.94,6.94,0,0,0,.12.9,8,8,0,0,0,.16,1.18h0a5.92,5.92,0,0,0,.38.9,6.38,6.38,0,0,0,.42,1v0a8.54,8.54,0,0,0,.6.78,7.62,7.62,0,0,0,.66.84l0,0c.23.22.52.38.77.58a8.93,8.93,0,0,0,.86.66l0,0,0,0,92.19,52.18Zm8-106.17-80.06-45.32,84.09-48.41,92.26-53.11,80.13,46.13-58.8,33.56Zm184.52,4.57L215.88,490.11V397.8L346.6,323.2l45.77-26.15Zm0-119.13L358.68,250l-46.53-26.79V131.79l33.69,19.4L392.37,178Zm8-105.28-80.2-46.17,80.2-46.16,80.18,46.15Zm8,105.28V178L455,151.19l33.68-19.4v91.39h0Z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Laravel</h4>
                                            <p class="text-gray-600 dark:text-gray-400">PHP framework providing the backend foundation</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg viewBox="0 0 128 128" class="w-8 h-8">
                                                    <path fill="#42b883" d="M78.8,10L64,35.4L49.2,10H0l64,110l64-110C128,10,78.8,10,78.8,10z"/>
                                                    <path fill="#35495e" d="M78.8,10L64,35.4L49.2,10H25.6L64,76l38.4-66H78.8z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Vue.js</h4>
                                            <p class="text-gray-600 dark:text-gray-400">Progressive JavaScript framework for UI components</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8">
                                                    <path fill="#9553E9" d="M12 0C5.375 0 0 5.375 0 12c0 6.627 5.375 12 12 12 6.627 0 12-5.373 12-12 0-6.625-5.373-12-12-12zm.001 2.247c5.405 0 9.756 4.351 9.756 9.756 0 5.405-4.351 9.756-9.756 9.756-5.405 0-9.756-4.351-9.756-9.756 0-5.405 4.351-9.756 9.756-9.756zM8.999 7v10h6v-2h-4V7h-2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Inertia.js</h4>
                                            <p class="text-gray-600 dark:text-gray-400">The glue between Laravel and Vue for modern SPAs</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg viewBox="0 0 32 32" class="w-8 h-8">
                                                    <path fill="#38bdf8" d="M9 13.7q1.4-5.6 7-5.6c5.6 0 6.3 4.2 9.1 4.9q2.8.7 4.9-2.1-1.4 5.6-7 5.6c-5.6 0-6.3-4.2-9.1-4.9q-2.8-.7-4.9 2.1zm-7 8.4q1.4-5.6 7-5.6c5.6 0 6.3 4.2 9.1 4.9q2.8.7 4.9-2.1-1.4 5.6-7 5.6c-5.6 0-6.3-4.2-9.1-4.9q-2.8-.7-4.9 2.1z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Tailwind CSS</h4>
                                            <p class="text-gray-600 dark:text-gray-400">Utility-first CSS framework for custom designs</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Authentication & Security -->
                <div>
                    <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Key Packages</h4>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="space-y-4">
                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Fortify - <span class="text-gray-500 dark:text-gray-400">Backend authentication scaffolding</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Chart.js - <span class="text-gray-500 dark:text-gray-400">JavaScript charting library</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">TanStack Table - <span class="text-gray-500 dark:text-gray-400">Powerful table and datagrid system</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">FilePond - <span class="text-gray-500 dark:text-gray-400">Advanced file upload interface</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Backup - <span class="text-gray-500 dark:text-gray-400">Automated database and file backups</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Google Fonts - <span class="text-gray-500 dark:text-gray-400">Self-hosted font integration</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravolt Avatar - <span class="text-gray-500 dark:text-gray-400">Name-based avatar generator</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Auditing - <span class="text-gray-500 dark:text-gray-400">Model and activity tracking</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Spatie Permission - <span class="text-gray-500 dark:text-gray-400">Role and permission management</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add ArticleNavigation component -->
    <ArticleNavigation :links="articleLinks" />
</template>

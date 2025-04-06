<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'

defineOptions({
    layout: Public
})

// Core features data
const features = {
    core: [
        {
            title: 'Authentication',
            items: [
                { name: 'Secure Login', description: 'Based on Laravel\'s authentication' },
                { name: 'Passwordless Login', description: 'Streamlined authentication without password' },
                { name: 'Role-based Permissions', description: 'Control access with customizable roles' },
                { name: 'Password Security Policies', description: 'Configure password expiry and changes' },
                { name: 'Authentication Logs', description: 'Track login attempts and sessions' }
            ]
        },
        {
            title: 'Interface & Layout',
            items: [
                { name: 'Responsive Layout', description: 'Mobile-friendly interface with Tailwind' },
                { name: 'Dark/Light Mode Toggle', description: 'Switch between themes for viewing' },
                { name: 'Dashboard Overview', description: 'Customizable home page' }
            ]
        },
        {
            title: 'Data Management',
            items: [
                { name: 'Data Tables', description: 'Interactive tables with sorting' },
                { name: 'Advanced Filtering', description: 'Find exactly what you need quickly' },
                { name: 'FilePond Integration', description: 'Drag and drop file uploads' },
                { name: 'Backup System', description: 'Automated backup functionality' }
            ]
        }
    ]
}

const articleLinks = [
    { text: 'Introduction', href: '#introduction' },
    { text: 'Authentication', href: '#authentication' },
    { text: 'Interface & Layout', href: '#interface' },
    { text: 'Data Management', href: '#data-management' },
    { text: 'Technologies & Packages', href: '#technologies' },
    { text: 'Contributing', href: '#contributing' }
]

const showBackToTop = ref(false)

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

    <Head title="Introduction - Obomadash" />

    <div id="introduction" class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-purple-600 to-indigo-600 dark:from-purple-900 dark:to-indigo-900">
            <div class="relative z-10 p-8 md:p-12">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Introduction</h1>
                </div>
                <p class="text-lg text-purple-100 dark:text-purple-200 max-w-3xl mb-8">
                    Transform your Laravel development workflow with Obomadash - a VILT stack starter kit that
                    accelerates app development with pre-configured authentication, UI components, and developer tools.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#quick-start"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-purple-600 hover:bg-purple-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Start building now
                    </a>
                    <a href="https://github.com/otatechie/starter" target="_blank"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-purple-500 text-white hover:bg-purple-400 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                clip-rule="evenodd" />
                        </svg>
                        Explore source code
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-400/30 to-indigo-400/30 blur-3xl rounded-full">
            </div>
        </div>

        <div class="border-t border-gray-200 py-2"></div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Features</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Obomaa provides a solid foundation for building admin interfaces with Laravel, Vue, Inertia, and
                Tailwind CSS:
            </p>
        </div>

        <div class="space-y-12">
            <section id="authentication" class="scroll-mt-16">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Authentication</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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
            </section>

            <section id="interface" class="scroll-mt-16">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Interface & Layout</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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
            </section>

            <section id="data-management" class="scroll-mt-16">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7m5 4h6m-6 4h6m-6 4h6" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Management</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0 mt-1">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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
            </section>

            <section id="technologies" class="scroll-mt-16">
                <div class="relative mt-16">
                    <div class="max-w-4xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Technologies & Packages
                                </h3>
                            </div>
                        </div>

                        <!-- Core Stack -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Core Stack</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                    class="w-8 h-8">
                                                    <path fill="#FF2D20"
                                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Laravel
                                                <span class="text-sm text-gray-500">v11.x</span>
                                            </h4>
                                            <p class="text-gray-600 dark:text-gray-400">PHP framework providing the
                                                backend foundation</p>
                                            <a href="https://laravel.com/docs" target="_blank"
                                                class="text-purple-600 hover:text-purple-500 text-sm mt-2 inline-block">Official
                                                Docs →</a>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg viewBox="0 0 32 32" class="w-8 h-8">
                                                    <path fill="#38bdf8"
                                                        d="M13.5 16.5c-.3-2.1-1.5-4-3.2-5.2 1.6-1.6 3.9-2.5 6.2-2.5 4.6 0 8.4 3.7 8.4 8.4 0 1.9-.7 3.7-1.8 5.1 1.7-.3 3.3-1.1 4.6-2.2-.6 4-3.9 7.1-8 7.1-3.9 0-7.2-2.8-7.9-6.5-.5.5-1.2.8-1.9.8C8.1 21.5 7 20.4 7 19s1.1-2.5 2.5-2.5h4z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">Vue.js
                                                <span class="text-sm text-gray-500">v3.x</span>
                                            </h4>
                                            <p class="text-gray-600 dark:text-gray-400">Progressive JavaScript framework
                                                for UI components</p>
                                            <a href="https://vuejs.org/guide/introduction.html" target="_blank"
                                                class="text-purple-600 hover:text-purple-500 text-sm mt-2 inline-block">Official
                                                Docs →</a>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="w-8 h-8">
                                                    <path fill="#9553E9"
                                                        d="M12 0C5.375 0 0 5.375 0 12c0 6.627 5.375 12 12 12 6.627 0 12-5.373 12-12 0-6.625-5.373-12-12-12zm.001 2.247c5.405 0 9.756 4.351 9.756 9.756 0 5.405-4.351 9.756-9.756 9.756-5.405 0-9.756-4.351-9.756-9.756 0-5.405 4.351-9.756 9.756-9.756zM8.999 7v10h6v-2h-4V7h-2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                                Inertia.js <span class="text-sm text-gray-500">v2.x</span></h4>
                                            <p class="text-gray-600 dark:text-gray-400">The glue between Laravel and Vue
                                                for modern SPAs</p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-purple-500 dark:hover:border-purple-500 transition-all duration-300">
                                    <div class="flex items-start gap-6">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                                <svg viewBox="0 0 32 32" class="w-8 h-8">
                                                    <path fill="#38bdf8"
                                                        d="M13.5 16.5c-.3-2.1-1.5-4-3.2-5.2 1.6-1.6 3.9-2.5 6.2-2.5 4.6 0 8.4 3.7 8.4 8.4 0 1.9-.7 3.7-1.8 5.1 1.7-.3 3.3-1.1 4.6-2.2-.6 4-3.9 7.1-8 7.1-3.9 0-7.2-2.8-7.9-6.5-.5.5-1.2.8-1.9.8C8.1 21.5 7 20.4 7 19s1.1-2.5 2.5-2.5h4z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">
                                                Tailwind CSS <span class="text-sm text-gray-500">v4.x</span></h4>
                                            <p class="text-gray-600 dark:text-gray-400">Utility-first CSS framework for
                                                custom designs</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Key Packages</h4>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="space-y-4">
                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Fortify -
                                    <span class="text-gray-500 dark:text-gray-400">Backend authentication
                                        scaffolding</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Chart.js - <span
                                        class="text-gray-500 dark:text-gray-400">JavaScript charting library</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">TanStack Table -
                                    <span class="text-gray-500 dark:text-gray-400">Powerful table and datagrid
                                        system</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">FilePond - <span
                                        class="text-gray-500 dark:text-gray-400">Advanced file upload interface</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Backup -
                                    <span class="text-gray-500 dark:text-gray-400">Automated database and file
                                        backups</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Google
                                    Fonts - <span class="text-gray-500 dark:text-gray-400">Self-hosted font
                                        integration</span></h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravolt Avatar -
                                    <span class="text-gray-500 dark:text-gray-400">Name-based avatar generator</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Laravel Auditing -
                                    <span class="text-gray-500 dark:text-gray-400">Model and activity tracking</span>
                                </h5>
                            </div>

                            <div>
                                <h5 class="font-medium text-gray-800 dark:text-white leading-relaxed">Spatie Permission
                                    - <span class="text-gray-500 dark:text-gray-400">Role and permission
                                        management</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contributing" class="scroll-mt-16">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Contributing</h2>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            We welcome contributions from the community! Here's how you can help make Obomaa better:
                        </p>
                        <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Report bugs and submit feature requests through GitHub issues</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Submit pull requests with bug fixes and improvements</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Help improve documentation and write tutorials</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Share your experience and help others in the community</span>
                            </li>
                        </ul>
                        <div class="mt-6">
                            <a href="https://github.com/otatechie/starter/blob/main/CONTRIBUTING.md" target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                View contributing guidelines
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <button v-show="showBackToTop" @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-purple-600 text-white p-3 rounded-full shadow-lg hover:bg-purple-500 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
        aria-label="Back to top">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
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

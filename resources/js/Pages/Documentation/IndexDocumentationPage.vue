<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'

defineOptions({
    layout: Public
})

const features = {
    core: [
        {
            title: 'Authentication',
            items: [
                { name: 'Secure Login', description: 'Based on Laravel\'s authentication' },
                { name: 'Passwordless Login', description: 'Streamlined authentication without password' },
                { name: 'Role-based Permissions', description: 'Control access with customizable roles' },
                { name: 'Password Security Policies', description: 'Configure password expiry and changes' },
                { name: 'Authentication Logs', description: 'Track login attempts and sessions' },
                { name: 'Activity Logs', description: 'Monitor user actions and system changes' }
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
    { text: 'Technologies & Packages', href: '#technologies-packages' },
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

    <Head title="Introduction - GuacPanel" />

    <div id="introduction" class="max-w-7xl mx-auto lg:px-8">
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-teal-600 to-teal-700 dark:from-teal-900 dark:to-teal-800">
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
                <p class="text-lg text-teal-100 dark:text-teal-200 max-w-3xl mb-8">
                    Jumpstart your Laravel projects with GuacPanel - a modern VILT stack kit featuring Fortify
                    authentication, Spatie's powerful trio (Permissions, Backup, Auditing), and TanStack Table
                    integration.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/documentation/installation#installation"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-teal-500 hover:bg-teal-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Start building now
                    </a>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-teal-500 text-white hover:bg-teal-400 transition-colors font-medium">
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
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-teal-400/30 to-teal-500/30 blur-3xl rounded-full">
            </div>
        </div>

        <section class="space-y-10">
            <!-- Authentication Section -->
            <section id="authentication" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Authentication</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Laravel Fortify
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Secure authentication scaffolding with 2FA support
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Passwordless Login
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Streamlined authentication without password
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Spatie Permissions
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Role-based access control with visual management
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Password Security Policies
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Configure password expiry and changes
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Authentication Logs
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Track login attempts and sessions
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Activity Logs
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Monitor user actions and system changes
                                </p>
                                <a href="#"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Interface & Layout Section -->
            <section id="interface" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Interface & Layout</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-indigo-500/50 dark:hover:border-indigo-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 rounded-lg group-hover:from-indigo-500/20 group-hover:to-indigo-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-indigo-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                    Responsive Layout</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Mobile-friendly interface with Tailwind CSS for seamless viewing across all
                                    devices.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-indigo-500/50 dark:hover:border-indigo-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 rounded-lg group-hover:from-indigo-500/20 group-hover:to-indigo-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-indigo-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                    Dark/Light Mode</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Switch
                                    between dark and light themes for comfortable viewing in any environment.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-indigo-500/50 dark:hover:border-indigo-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 rounded-lg group-hover:from-indigo-500/20 group-hover:to-indigo-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-indigo-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                    Color Themes</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Customize
                                    your UI with different color themes to match your brand.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Data Management Section -->
            <section id="data-management" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Data Management</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 rounded-lg group-hover:from-emerald-500/20 group-hover:to-emerald-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-emerald-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors duration-300">
                                    TanStack Table</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Advanced
                                    data tables with powerful sorting, filtering, and pagination capabilities.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 rounded-lg group-hover:from-emerald-500/20 group-hover:to-emerald-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-emerald-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3v-7" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors duration-300">
                                    FilePond Upload</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Intuitive
                                    drag and drop file uploads with preview and progress indicators.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 rounded-lg group-hover:from-emerald-500/20 group-hover:to-emerald-500/10 transition-all duration-300">
                                <svg class="w-5 h-5 text-emerald-500 transition-transform duration-300 group-hover:scale-110"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors duration-300">
                                    Spatie Backup</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Automated
                                    database and file backups with intuitive UI management.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technologies Section -->
            <section id="technologies-packages" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Technologies & Packages</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Laravel -->
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-lg group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                <svg viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-teal-500 transition-transform duration-300 group-hover:scale-110">
                                    <path
                                        d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09V12.33L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z"
                                        fill="#FF2D20" fill-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Laravel <span class="text-sm text-gray-500">v11.x</span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">PHP framework
                                    providing the backend foundation</p>
                                <a href="https://laravel.com/docs" target="_blank"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300 mt-2">
                                    Official Docs
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Vue.js -->
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-lg group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 261.76 226.69"
                                    class="w-5 h-5 transition-transform duration-300 group-hover:scale-110">
                                    <path d="M161.096.001l-30.225 52.351L100.647.001H-.005l130.877 226.688L261.749.001z"
                                        fill="#41b883" />
                                    <path d="M161.096.001l-30.225 52.351L100.647.001H52.346l78.526 136.01L209.398.001z"
                                        fill="#34495e" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Vue.js <span class="text-sm text-gray-500">v3.x</span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Progressive
                                    JavaScript framework for UI components</p>
                                <a href="https://vuejs.org/guide/introduction.html" target="_blank"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300 mt-2">
                                    Official Docs
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Inertia.js -->
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-lg group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                <svg class="w-6 h-6 text-purple-600 transition-transform duration-300 group-hover:scale-110"
                                    viewBox="0 0 275.3 50.5" fill="currentColor">
                                    <path d="M231.2 16.1h-17.8l17.2 17.2-17.2 17.2h17.8l17.2-17.2z"></path>
                                    <path d="M258.1 16.1h-17.8l17.2 17.2-17.2 17.2h17.8l17.2-17.2z"></path>
                                    <path
                                        d="M6 15.3h10.3l-6 34.2H0l6-34.2zm.6-9.1C7.2 2.9 10.3 0 13.7 0s5.7 2.8 5.2 6.2c-.5 3.4-3.7 6.2-7.2 6.2s-5.6-3-5.1-6.2zM54.3 28.5l-3.7 21H40.4L43.8 30c.8-4.4-1.6-6.2-4.9-6.2-3.4 0-6.5 2-7.5 6.6L28 49.5H17.8l6-34.2h10.3l-1.1 6.2c2.3-2.6 6.2-4.2 10.1-4.2 6.9.1 12.2 5.1 10.6 14.2zM94.5 32.4c-.1.8-.5 2.7-1.1 4.1H68.9c.6 3.8 3.8 4.8 7 4.8 2.9 0 5.2-.8 7.2-2.7l7.2 5.9c-4 4-8.7 6-15 6-11.8 0-18-8.5-16.3-18.7a20.7 20.7 0 0 1 20.5-17.4c9.8 0 16.9 7.6 15 18zm-9.7-3.7c-.3-3.8-3-5.3-6.2-5.3a8.9 8.9 0 0 0-8.3 5.3h14.5zM123.9 14.6l-2 11.6c-4-.6-10.5.8-11.7 7.8l.1-.4-2.8 15.9H97.3l6-34.2h10.3l-1.1 6.2c2.1-4.7 6.6-6.9 11.4-6.9zM137.8 37.3c-.5 3.1 2 3.3 6.6 2.9l-1.6 9.3c-12.3 1.4-16.9-2.7-15.2-12.2l2.1-12.1h-5.5l1.8-9.9h5.4l1.2-6.5 10.8-3.1-1.7 9.6h7.1l-1.8 9.9h-7l-2.2 12.1zM155.3 15.3h10.3l-6 34.2h-10.3l6-34.2zm.6-9.1c.5-3.3 3.7-6.2 7.1-6.2s5.7 2.8 5.2 6.2c-.5 3.4-3.7 6.2-7.2 6.2s-5.7-3-5.1-6.2zM208.1 15.3l-6 34.2h-10.3l.4-2.3a15.5 15.5 0 0 1-10.3 3.3c-11.1 0-15.3-9.6-13.5-18.9 1.6-8.8 8.6-17.2 19.2-17.2 4.5 0 7.7 1.8 9.6 4.6l.6-3.6h10.3zm-13.2 17.2c.9-5.2-1.9-8.4-6.6-8.4a9.5 9.5 0 0 0-9.5 8.3c-.9 5.1 1.8 8.3 6.6 8.3 4.6.1 8.6-3.1 9.5-8.2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Inertia.js <span class="text-sm text-gray-500">v2.x</span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">The glue
                                    between Laravel and Vue for modern SPAs</p>
                                <a href="https://inertiajs.com/" target="_blank"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300 mt-2">
                                    Official Docs
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tailwind CSS -->
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-4">
                            <div
                                class="p-2 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-lg group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 54 33"
                                    class="w-5 h-5 transition-transform duration-300 group-hover:scale-110">
                                    <g clip-path="url(#prefix__clip0)">
                                        <path fill="#06B6D4" fill-rule="evenodd"
                                            d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z"
                                            clip-rule="evenodd" />
                                    </g>
                                    <defs>
                                        <clipPath id="prefix__clip0">
                                            <path fill="#fff" d="M0 0h54v32.4H0z" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Tailwind CSS <span class="text-sm text-gray-500">v4.x</span>
                                </h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">Utility-first
                                    CSS framework for custom designs</p>
                                <a href="https://tailwindcss.com/docs/installation/using-vite" target="_blank"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300 mt-2">
                                    Official Docs
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-end">
            <a href="/documentation/installation"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-blue-500 dark:group-hover:text-teal-400">
                        Installation Guide</div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
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

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'

defineOptions({
    layout: Default,
})
</script>

<template>

    <Head title="Settings" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-b border-gray-300 dark:border-gray-700">
                <div class="flex items-center space-x-2 text-sm">
                    <Link href="/admin"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Dashboard
                    </Link>
                    <span class="text-gray-400 dark:text-gray-500">/</span>
                    <span class="text-gray-800 dark:text-gray-200 font-medium">Settings</span>
                </div>
            </div>

            <header class="px-6 py-4 border-b border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="sub-heading dark:text-gray-200">Settings</h1>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Manage system-wide settings and configurations
                        </p>
                    </div>
                </div>
            </header>

            <div class="p-6 dark:bg-gray-700">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <Link :href="route('admin.audit')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">System Activity</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Track all system events, user actions, and
                        administrative changes in real-time.</p>
                    </Link>

                    <Link :href="route('admin.personalisation.index')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-200 dark:border-purple-800">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Theme Settings</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customize the platform's appearance, branding,
                        and visual preferences.</p>
                    </Link>

                    <Link :href="route('admin.user')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">User Management</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage user accounts, profiles, and account
                        settings centrally.</p>
                    </Link>

                    <Link :href="route('backup.index')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Data Backup</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Schedule and manage automated backups of your
                        system data.</p>
                    </Link>

                    <Link :href="route('admin.permission.role')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-200 dark:border-indigo-800">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Access Control</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Define and manage user roles, permissions, and
                        access levels.</p>
                    </Link>

                    <Link :href="route('admin.permission.role')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-200 dark:border-red-800">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Login History</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor login attempts, sessions, and
                        authentication activities.</p>
                    </Link>

                    <Link :href="route('admin.setting.manage')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg border border-orange-200 dark:border-orange-800">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Security Settings</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure system-wide security policies and
                        protection measures.</p>
                    </Link>

                    <Link :href="route('admin.setting.manage')"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="p-2 bg-teal-50 dark:bg-teal-900/30 rounded-lg border border-teal-200 dark:border-teal-800">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Schedule Monitoring</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor scheduled tasks in a Laravel app</p>
                    </Link>

                    <a href="/health" target="_blank"
                        class="group flex flex-col p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md transition-all duration-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="p-2 bg-pink-50 dark:bg-pink-900/30 rounded-lg border border-pink-200 dark:border-pink-800">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">System Health</h3>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Monitor application performance, services,
                            and system diagnostics.</p>
                    </a>
                </div>
            </div>
        </section>
    </main>
</template>

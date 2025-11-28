<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default
})
</script>

<template>
    <Head title="Settings" />

    <main class="max-w-7xl mx-auto" aria-labelledby="settings">
        <div class="container-border">
            <PageHeader
                title="System Settings"
                description="Manage system-wide settings and configurations"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'System Settings' }
                ]" />
            <div class="p-6 bg-[var(--color-bg)]">
                <div
                    class="bg-[var(--color-surface)] rounded-xl shadow-sm border border-[var(--color-border)] p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <Link
                            :href="route('admin.audit.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden"
                            aria-label="System Activity Settings">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-100 dark:border-blue-800 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    System Activity
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Track all system events, user actions, and administrative changes in
                                real-time.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.personalization.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden"
                            aria-label="Theme Settings">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-xl border border-purple-100 dark:border-purple-800 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Theme Settings
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Customize the platform's appearance, branding, and visual
                                preferences.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.user.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-green-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-green-50 dark:bg-green-900/30 rounded-xl border border-green-100 dark:border-green-800 group-hover:bg-green-100 dark:group-hover:bg-green-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    User Management
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Manage user accounts, profiles, and account settings centrally.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.backup.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-xl border border-yellow-100 dark:border-yellow-800 group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-yellow-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Data Backup
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Schedule and manage backups of your system data.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.permission.role.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-indigo-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Access Control
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Define and manage user roles, permissions, and access levels.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.login.history.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-red-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-red-50 dark:bg-red-900/30 rounded-xl border border-red-100 dark:border-red-800 group-hover:bg-red-100 dark:group-hover:bg-red-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-red-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Login History
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Monitor login attempts, sessions, and authentication activities.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.setting.show')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-xl border border-orange-100 dark:border-orange-800 group-hover:bg-orange-100 dark:group-hover:bg-orange-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-orange-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Security Settings
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Configure system-wide security policies and protection measures.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.sessions.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl border border-cyan-100 dark:border-cyan-800 group-hover:bg-cyan-100 dark:group-hover:bg-cyan-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-cyan-600"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    Session Management
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Monitor and manage active user sessions across the system.
                            </p>
                        </Link>

                        <Link
                            :href="route('admin.health.index')"
                            class="group relative flex flex-col p-6 bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] hover:border-b-1 hover:border-b-primary-500 hover:shadow-sm transition-all duration-200 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 h-1 w-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="p-3 bg-violet-50 dark:bg-violet-900/30 rounded-xl border border-violet-100 dark:border-violet-800 group-hover:bg-violet-100 dark:group-hover:bg-violet-900/50 transition-colors">
                                    <svg
                                        class="w-5 h-5 text-violet-600"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 16 16"
                                        fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                                    System Health
                                </h3>
                            </div>
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Monitor and manage system health and performance.
                            </p>
                        </Link>

                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

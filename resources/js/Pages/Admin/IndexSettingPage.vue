<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { markRaw } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import {
    PaintBrushIcon,
    UsersIcon,
    CloudArrowUpIcon,
    KeyIcon,
    ClockIcon,
    ShieldCheckIcon,
    ComputerDesktopIcon,
    HeartIcon,
    DocumentMagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

defineOptions({
    layout: Default,
})

const groups = [
    {
        label: 'General',
        items: [
            { label: 'Theme Settings', desc: 'Branding, logos, and appearance', icon: markRaw(PaintBrushIcon), route: 'admin.personalization.index' },
            { label: 'System Health', desc: 'Uptime, services, and diagnostics', icon: markRaw(HeartIcon), route: 'admin.health.index' },
            { label: 'Data Backup', desc: 'Scheduled and manual backups', icon: markRaw(CloudArrowUpIcon), route: 'admin.backup.index' },
        ],
    },
    {
        label: 'Users & Access',
        items: [
            { label: 'User Management', desc: 'Accounts, roles, and profiles', icon: markRaw(UsersIcon), route: 'admin.user.index' },
            { label: 'Access Control', desc: 'Roles and permissions', icon: markRaw(KeyIcon), route: 'admin.permission.role.index' },
            { label: 'Session Management', desc: 'Active sessions across the system', icon: markRaw(ComputerDesktopIcon), route: 'admin.sessions.index' },
        ],
    },
    {
        label: 'Security & Audit',
        items: [
            { label: 'Security Settings', desc: 'Passwords, 2FA, and lockout policies', icon: markRaw(ShieldCheckIcon), route: 'admin.setting.show' },
            { label: 'Login History', desc: 'Authentication attempts and activity', icon: markRaw(ClockIcon), route: 'admin.login.history.index' },
            { label: 'System Activity', desc: 'Audit log of all admin actions', icon: markRaw(DocumentMagnifyingGlassIcon), route: 'admin.audit.index' },
        ],
    },
]
</script>

<template>
    <Head title="Settings" />

    <main class="mx-auto max-w-7xl" aria-labelledby="settings">
        <PageHeader
            title="System Settings"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings' },
            ]" />

        <div class="space-y-6">
            <section v-for="group in groups" :key="group.label">
                <h2 class="mb-2.5 text-xs font-medium text-muted-foreground">{{ group.label }}</h2>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <Link
                        v-for="item in group.items"
                        :key="item.route"
                        :href="route(item.route)"
                        class="card group flex items-start gap-3.5 px-4 py-3.5 transition-shadow hover:shadow-[0px_2px_4px_rgba(0,0,0,0.06),0px_6px_12px_rgba(18,42,66,0.07)]">
                        <component
                            :is="item.icon"
                            class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground transition-colors group-hover:text-foreground"
                            aria-hidden="true" />
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-foreground">{{ item.label }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ item.desc }}</p>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </main>
</template>

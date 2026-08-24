<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed, markRaw } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import {
    ActivityIcon,
    BellIcon,
    DatabaseBackupIcon,
    HistoryIcon,
    KeyRoundIcon,
    LockIcon,
    MonitorSmartphoneIcon,
    PaletteIcon,
    ScrollTextIcon,
    UsersIcon,
} from '@lucide/vue'
defineOptions({
    layout: Default,
})

const groups = [
    {
        label: 'General',
        items: [
            {
                label: 'Personalization',
                desc: 'App name, logos, and favicon',
                icon: markRaw(PaletteIcon),
                route: 'admin.personalization.index',
            },
            {
                label: 'System health',
                desc: 'Uptime, services, and diagnostics',
                icon: markRaw(ActivityIcon),
                route: 'admin.health.index',
            },
            {
                label: 'Data backup',
                desc: 'Create, download, and delete backups',
                icon: markRaw(DatabaseBackupIcon),
                route: 'admin.backup.index',
            },
        ],
    },
    {
        label: 'Content',
        items: [
            {
                label: 'Notifications',
                desc: 'Compose and manage system notices',
                icon: markRaw(BellIcon),
                route: 'admin.notifications.index',
                permission: 'manage-notifications',
            },
        ],
    },
    {
        label: 'Users & Access',
        items: [
            {
                label: 'User management',
                desc: 'Accounts, roles, and profiles',
                icon: markRaw(UsersIcon),
                route: 'admin.user.index',
            },
            {
                label: 'Access control',
                desc: 'Roles and permissions',
                icon: markRaw(KeyRoundIcon),
                route: 'admin.permission.role.index',
            },
            {
                label: 'Sessions',
                desc: 'Active sessions across the system',
                icon: markRaw(MonitorSmartphoneIcon),
                route: 'admin.sessions.index',
            },
        ],
    },
    {
        label: 'Security & Audit',
        items: [
            {
                label: 'Security settings',
                desc: 'Password expiry, 2FA, and sign-in methods',
                icon: markRaw(LockIcon),
                route: 'admin.setting.show',
            },
            {
                label: 'Login history',
                desc: 'Authentication attempts and activity',
                icon: markRaw(HistoryIcon),
                route: 'admin.login.history.index',
            },
            {
                label: 'Activity log',
                desc: 'Every admin action, with who and when',
                icon: markRaw(ScrollTextIcon),
                route: 'admin.audit.index',
            },
        ],
    },
]

/* The gear itself only checks manage-settings, so an item needing more than that
   is filtered here — showing a link the route then blocks is worse than hiding it. */
const page = usePage()
const permissions = computed(() => page.props.auth?.user?.permissions ?? [])
const visibleGroups = computed(() =>
    groups
        .map(group => ({
            ...group,
            items: group.items.filter(
                item => !item.permission || permissions.value.includes(item.permission)
            ),
        }))
        .filter(group => group.items.length > 0)
)
</script>

<template>
    <Head title="System settings" />

    <main class="mx-auto max-w-4xl" aria-labelledby="settings">
        <PageHeader
            id="settings"
            title="System settings"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings' },
            ]" />

        <div class="space-y-8">
            <section v-for="group in visibleGroups" :key="group.label">
                <h2 class="text-muted-foreground mb-2 text-xs font-medium">{{ group.label }}</h2>
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-2">
                    <Link
                        v-for="item in group.items"
                        :key="item.route"
                        :href="route(item.route)"
                        class="group focus-visible:outline-ring -mx-3 flex items-center gap-3.5 rounded-md px-3 py-3 focus-visible:outline-2 focus-visible:-outline-offset-2">
                        <component
                            :is="item.icon"
                            class="text-muted-foreground group-hover:text-primary h-4 w-4 shrink-0 transition-colors"
                            aria-hidden="true" />
                        <div class="min-w-0">
                            <p
                                class="text-foreground group-hover:text-primary text-sm font-medium transition-colors">
                                {{ item.label }}
                            </p>
                            <p class="text-muted-foreground mt-0.5 text-xs">{{ item.desc }}</p>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </main>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const hasPermission = permissionName =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

const isActive = href => href && page.url === new URL(href).pathname

const isParentActive = children =>
    children?.some?.(child => isActive(child.href)) ?? false

const toggleDropdown = item => item.isOpen = !item.isOpen

const getVisibleChildren = children =>
    children?.length ? children.filter(child => hasPermission(child.permission)) : []

const sectionHasVisibleItems = section => {
    if (!section?.items?.length) return false

    return section.items.some(item =>
        item.type !== 'divider' &&
        (item.children ? getVisibleChildren(item.children).length > 0 : hasPermission(item.permission))
    )
}

const navigationSections = reactive([
    {
        items: [
            {
                name: 'Dashboard',
                href: route('home'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            },
            { type: 'divider' },
        ]
    },
    {
        items: [
            {
                name: 'Settings',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                isOpen: false,
                children: [
                    { name: 'General', href: route('admin.setting.index'), permission: 'manage-settings' },
                    { name: 'Backups', href: route('backup.index'), permission: 'manage-backups' },
                    { name: 'Login History', href: route('admin.login.history.index'), permission: 'view-audits' },
                    { name: 'Personalisation', href: route('admin.personalization.index'), permission: 'manage-personalization' },
                    { name: 'Permissions & Roles', href: route('admin.permission.role.index'), permission: 'view-permissions-roles' },
                    { name: 'Users', href: route('admin.user.index'), permission: 'manage-users' },
                    { name: 'Audit Logs', href: route('admin.audit.index'), permission: 'view-audits' },
                    { name: 'Security Settings', href: route('admin.setting.show'), permission: 'manage-security-settings' },
                ]
            },
            { type: 'divider' },
        ]
    },
    {
        items: [
            {
                name: 'Documentation',
                href: route('documentation.index'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />',
            },
        ]
    },
])
</script>


<template>
    <aside data-sidebar-content class="nav-sidebar" @click.stop>
        <nav class="flex-1 overflow-y-auto py-2 px-2" aria-labelledby="nav-heading">
            <ul class="space-y-1">
                <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
                    <template v-if="sectionHasVisibleItems(section)" v-for="(item, itemIndex) in section.items"
                        :key="itemIndex">
                        <li v-if="item.type === 'divider'" class="my-1.5 px-2" role="separator">
                            <div class="nav-divider"></div>
                        </li>

                        <li v-else-if="item.children && getVisibleChildren(item.children).length > 0">
                            <button @click="toggleDropdown(item)" :class="[
                                'nav-dropdown',
                                isParentActive(item.children) ? 'nav-dropdown-active' : 'nav-dropdown-default'
                            ]" :aria-expanded="item.isOpen">
                                <div class="flex items-center">
                                    <svg :class="[
                                        'nav-icon',
                                        isParentActive(item.children) ? 'nav-icon-active' : 'nav-icon-default'
                                    ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" aria-hidden="true" v-html="item.icon">
                                    </svg>
                                    <span class="text-sm font-medium">{{ item.name }}</span>
                                </div>

                                <svg :class="[
                                    'w-4 h-4 transition-transform duration-200 text-gray-600 dark:text-gray-500',
                                    item.isOpen ? 'rotate-180' : ''
                                ]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <ul v-show="item.isOpen" class="pl-9 space-y-1">
                                <li v-for="child in getVisibleChildren(item.children)" :key="child.name">
                                    <Link :href="child.href" :class="[
                                        'nav-child-item',
                                        isActive(child.href) ? 'nav-child-item-active' : 'nav-child-item-default'
                                    ]">
                                    <span class="text-sm">{{ child.name }}</span>
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <li v-else>
                            <Link v-if="hasPermission(item.permission)" :href="item.href" :class="[
                                'nav-item',
                                isActive(item.href) ? 'nav-item-active' : 'nav-item-default'
                            ]">
                            <svg :class="[
                                'nav-icon',
                                isActive(item.href) ? 'nav-icon-active' : 'nav-icon-default'
                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true" v-html="item.icon">
                            </svg>
                            <span class="text-sm font-medium">{{ item.name }}</span>
                            </Link>
                        </li>
                    </template>
                </template>
            </ul>
        </nav>
    </aside>
</template>
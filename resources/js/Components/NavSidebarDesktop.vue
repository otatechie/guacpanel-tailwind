<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const hasPermission = permissionName =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

const isCurrentRoute = routeName => {
    if (!routeName) return false

    const currentUrl = page.url.value
    const routeUrl = route(routeName)
    return currentUrl === routeUrl || route().current(routeName)
}

const sectionHasVisibleItems = section => {
    if (!section?.items?.length) return false

    return section.items.some(item =>
        item.type !== 'divider' && hasPermission(item.permission)
    )
}

const navigationSections = reactive([
    {
        items: [
            {
                name: 'Dashboard',
                route: 'dashboard',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            },
            { type: 'divider' }
        ]
    },
    {
        items: [
            {
                name: 'Documentation',
                route: 'documentation.index',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />',
            },
            { type: 'divider' }
        ]
    },
    {
        items: [
            {
                name: 'Charts',
                route: 'chart.index',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
            }
        ]
    }
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

                        <li v-else>
                            <Link v-if="hasPermission(item.permission)" :href="route(item.route)" :class="[
                                'nav-item',
                                isCurrentRoute(item.route) ? 'nav-item-active' : 'nav-item-default'
                            ]">
                            <svg :class="[
                                'nav-icon',
                                isCurrentRoute(item.route) ? 'nav-icon-active' : 'nav-icon-default'
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
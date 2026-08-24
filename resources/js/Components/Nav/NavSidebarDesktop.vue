<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, markRaw } from 'vue'
import { BellIcon, ChartColumnBigIcon, HouseIcon } from '@lucide/vue'
import { usePermissions } from '@js/composables/usePermissions'
import { workspaceNav } from '@js/navigation'

const page = usePage()

/* An array means "any of these" — the notifications page accepts either
   view-notifications or manage-notifications, matching its route middleware. */
const { user, hasPermission } = usePermissions()

const isCurrentRoute = routeName => {
    if (!routeName) return false
    if (Array.isArray(routeName)) return routeName.some(r => isCurrentRoute(r))
    const name = String(routeName)
    if (name.includes('*')) return route().current(name)
    return page.url.value === route(name) || route().current(name)
}

const isActive = item => isCurrentRoute(item.activeRoutes) || isCurrentRoute(item.route)

/* Flat, one level deep. Administration lives behind the header's gear, so the
   sidebar only carries the places people work day to day -- it renders
   workspaceNav from resources/js/navigation.js, which the command palette reads
   too, so a removed feature disappears from both at once. */
const ICONS = {
    home: markRaw(HouseIcon),
    chart: markRaw(ChartColumnBigIcon),
    bell: markRaw(BellIcon),
}

const isAvailable = item =>
    (!item.feature || Boolean(page.props.settings?.[item.feature])) &&
    hasPermission(item.permission)

const navigationSections = computed(() => {
    const items = workspaceNav.filter(isAvailable).map(item => ({
        ...item,
        icon: ICONS[item.icon],
        activeRoutes: item.activeRoutes ?? [item.route],
    }))

    return [
        { items: items.filter(i => i.route === 'dashboard') },
        { label: 'Workspace', items: items.filter(i => i.route !== 'dashboard') },
    ].filter(section => section.items.length)
})

const visibleItems = section => section.items
</script>

<template>
    <aside data-sidebar-content class="nav-sidebar overflow-y-auto" @click.stop>
        <nav class="px-2 py-3">
            <template v-for="(section, si) in navigationSections" :key="si">
                <div v-if="visibleItems(section).length" class="nav-section">
                    <p v-if="section.label" class="nav-section-label">{{ section.label }}</p>

                    <ul class="space-y-0.5">
                        <li v-for="item in visibleItems(section)" :key="item.name">
                            <Link
                                :href="route(item.route)"
                                :aria-current="isActive(item) ? 'page' : undefined"
                                :class="[
                                    'nav-item',
                                    section.label ? 'nav-item-grouped' : '',
                                    isActive(item) ? 'nav-item-active' : 'nav-item-default',
                                ]">
                                <component
                                    :is="item.icon"
                                    class="nav-icon"
                                    :stroke-width="1.75"
                                    aria-hidden="true" />
                                <span class="flex-1 truncate">{{ item.name }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </template>
        </nav>

        <!-- Reference documents, not app sections — so they sit apart, at the foot. -->
        <div class="nav-footer">
            <ul class="flex items-center gap-4 px-3">
                <li>
                    <Link :href="route('terms')" class="nav-footer-link">Terms</Link>
                </li>
            </ul>
        </div>
    </aside>
</template>

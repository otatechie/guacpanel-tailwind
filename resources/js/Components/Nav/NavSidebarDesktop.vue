<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, markRaw } from 'vue'
import { BellIcon, ChartColumnBigIcon, HouseIcon } from '@lucide/vue'
import { usePermissions } from '@js/composables/usePermissions'

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
   sidebar only carries the places people work day to day. */
const navigationSections = computed(() => [
    {
        items: [{ name: 'Dashboard', route: 'dashboard', icon: markRaw(HouseIcon) }],
    },
    {
        label: 'Workspace',
        items: [
            { name: 'Charts', route: 'chart.index', icon: markRaw(ChartColumnBigIcon) },
            /* The reader's own notifications, not the admin authoring tool —
               that lives behind the header's gear with the rest of system
               configuration. This page otherwise had no nav entry at all,
               reachable only from the bell popover's footer. */
            ...(page.props.settings?.notificationEnabled
                ? [
                      {
                          name: 'Notifications',
                          route: 'notifications.index',
                          activeRoutes: ['notifications.index'],
                          permission: ['view-notifications', 'manage-notifications'],
                          icon: markRaw(BellIcon),
                      },
                  ]
                : []),
        ],
    },
])

const visibleItems = section => section.items.filter(item => hasPermission(item.permission))
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

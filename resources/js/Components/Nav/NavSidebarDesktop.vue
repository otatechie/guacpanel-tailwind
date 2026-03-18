<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, computed, ref, markRaw } from 'vue'
import {
    HomeIcon,
    ChartBarSquareIcon,
    BellIcon,
    Cog8ToothIcon,
    ChevronDownIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const hasPermission = permissionName =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

const isCurrentRoute = routeName => {
    if (!routeName) return false
    if (Array.isArray(routeName)) return routeName.some(r => isCurrentRoute(r))
    const name = String(routeName)
    if (name.includes('*')) return route().current(name)
    return page.url.value === route(name) || route().current(name)
}

const hasVisibleChildren = item => {
    if (!item?.children?.length) return false
    return item.children.some(child => hasPermission(child.permission))
}

const isChildCurrentRoute = item => {
    if (!item?.children?.length) return false
    return item.children.some(child => {
        if (!hasPermission(child.permission)) return false
        if (isCurrentRoute(child.route)) return true
        return isChildCurrentRoute(child)
    })
}

const isParentOrChildActive = item => {
    return isCurrentRoute(item?.activeRoutes) || isCurrentRoute(item.route) || isChildCurrentRoute(item)
}

const collapsedParents = ref({})
const getParentKey = item => item.route || item.name

const isParentExpanded = item => {
    if (!item?.children?.length || !hasVisibleChildren(item)) return false
    const key = getParentKey(item)
    if (isCurrentRoute(item.route)) return collapsedParents.value[key] !== true
    if (isChildCurrentRoute(item)) return true
    return false
}

const onParentClick = (event, item) => {
    if (!item.children || !hasVisibleChildren(item)) return
    const key = getParentKey(item)
    if (isCurrentRoute(item.route)) {
        event.preventDefault()
        collapsedParents.value[key] = !collapsedParents.value[key]
        return
    }
    collapsedParents.value[key] = false
}

const sectionHasVisibleItems = section => {
    if (!section?.items?.length) return false
    return section.items.some(
        item =>
            item.type !== 'divider' &&
            (hasPermission(item.permission) || (item.children && hasVisibleChildren(item)))
    )
}

const navigationSections = computed(() => {
    const items = reactive([
        {
            items: [
                {
                    name: 'Dashboard',
                    route: 'dashboard',
                    icon: markRaw(HomeIcon),
                },
                {
                    name: 'Charts',
                    route: 'chart.index',
                    icon: markRaw(ChartBarSquareIcon),
                },
                ...(page.props.settings?.notificationEnabled
                    ? [
                          { type: 'divider', permission: 'manage-notifications' },
                          {
                              name: 'Notifications',
                              route: 'admin.notifications.index',
                              activeRoutes: ['admin.notifications.*'],
                              permission: 'manage-notifications',
                              icon: markRaw(BellIcon),
                          },
                      ]
                    : []),
            ],
        },
    ])

    const usermanagement = {
        name: 'User Management',
        route: 'admin.user.index',
        children: [],
    }

    if (
        (page.props.deletedUsers && page.props.deletedUsers > 0) ||
        isCurrentRoute('admin.user.deleted.index')
    ) {
        usermanagement.children.push({ name: 'Deleted Users', route: 'admin.user.deleted.index' })
    }

    const systemSettingsItems = {
        items: [
            { type: 'divider' },
            {
                name: 'System Settings',
                icon: markRaw(Cog8ToothIcon),
                route: 'admin.setting.index',
                children: [
                    { name: 'System Activity', route: 'admin.audit.index' },
                    { name: 'Theme Settings', route: 'admin.personalization.index' },
                    usermanagement,
                    { name: 'Data Backup', route: 'admin.backup.index' },
                    { name: 'Access Control', route: 'admin.permission.role.index' },
                    { name: 'Login History', route: 'admin.login.history.index' },
                    { name: 'Security Settings', route: 'admin.setting.show' },
                    { name: 'Session Management', route: 'admin.sessions.index' },
                    { name: 'Health Status', route: 'admin.health.index' },
                ],
            },
        ],
    }

    if (hasPermission('manage-settings')) {
        items.push(systemSettingsItems)
    }

    return items
})

const showDivider = item =>
    item.type === 'divider' && (!item.permission || hasPermission(item.permission))
</script>

<template>
    <aside data-sidebar-content class="nav-sidebar" @click.stop>
        <nav class="flex-1 overflow-y-auto px-2 py-2">
            <ul class="space-y-0.5">
                <template v-for="(section, si) in navigationSections" :key="si">
                    <template v-if="sectionHasVisibleItems(section)">
                        <template v-for="(item, ii) in section.items" :key="ii">

                            <!-- Divider -->
                            <li v-if="showDivider(item)" class="my-2 px-2.5" role="separator">
                                <div class="nav-divider"></div>
                            </li>

                            <!-- Nav item -->
                            <li v-else>
                                <Link
                                    v-if="hasPermission(item.permission) && item.route"
                                    :href="route(item.route)"
                                    @click="onParentClick($event, item)"
                                    :class="[
                                        'nav-item',
                                        isParentOrChildActive(item) ? 'nav-item-active' : 'nav-item-default',
                                    ]">
                                    <component
                                        :is="item.icon"
                                        :class="['nav-icon', isParentOrChildActive(item) ? 'nav-icon-active' : 'nav-icon-default']"
                                        aria-hidden="true" />

                                    <span class="flex-1">{{ item.name }}</span>

                                    <ChevronDownIcon
                                        v-if="item.children && hasVisibleChildren(item)"
                                        :class="['h-3.5 w-3.5 shrink-0 text-(--color-text-muted) transition-transform duration-150', isParentExpanded(item) ? 'rotate-180' : '']"
                                        aria-hidden="true" />
                                </Link>
                            </li>

                            <!-- Children -->
                            <li v-if="item.children && hasVisibleChildren(item) && isParentExpanded(item)">
                                <ul class="ml-5 space-y-0.5 border-l border-(--card-border) pl-2.5">
                                    <li v-for="child in item.children" :key="child.name">
                                        <Link
                                            v-if="hasPermission(child.permission)"
                                            :href="route(child.route)"
                                            @click="onParentClick($event, child)"
                                            :class="[
                                                'nav-item',
                                                isCurrentRoute(child.route) ? 'nav-item-active' : 'nav-item-default',
                                            ]">
                                            <span>{{ child.name }}</span>
                                        </Link>

                                        <!-- Grandchildren -->
                                        <ul
                                            v-if="child.children && hasVisibleChildren(child) && isParentExpanded(child)"
                                            class="ml-3 space-y-0.5 border-l border-(--card-border) pl-2.5">
                                            <li v-for="gc in child.children" :key="gc.name">
                                                <Link
                                                    v-if="hasPermission(gc.permission)"
                                                    :href="route(gc.route)"
                                                    :class="[
                                                        'nav-item',
                                                        isCurrentRoute(gc.route) ? 'nav-item-active' : 'nav-item-default',
                                                    ]">
                                                    <span>{{ gc.name }}</span>
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </template>
                    </template>
                </template>
            </ul>
        </nav>
    </aside>
</template>

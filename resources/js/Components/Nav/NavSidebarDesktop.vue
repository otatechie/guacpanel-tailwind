<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { reactive, computed, ref } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

/**
 * Check if the current user has a given permission.
 * If no permission name is provided, it's treated as "no restriction".
 */
const hasPermission = permissionName =>
  !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

/**
 * Check if a given named route is the current route.
 * Uses both exact URL comparison and Inertia's route().current().
 */
const isCurrentRoute = routeName => {
  if (!routeName) return false

  const currentUrl = page.url.value
  const routeUrl = route(routeName)
  return currentUrl === routeUrl || route().current(routeName)
}

/**
 * Check if an item has children that are actually visible
 * based on the current user's permissions.
 */
const hasVisibleChildren = item => {
  if (!item?.children?.length) return false
  return item.children.some(child => hasPermission(child.permission))
}

/**
 * Check if ANY descendant route of this item is the current route.
 * Only considers children the user has permission to see.
 */
const isChildCurrentRoute = item => {
  if (!item?.children?.length) return false

  return item.children.some(child => {
    if (!hasPermission(child.permission)) return false
    if (isCurrentRoute(child.route)) return true
    return isChildCurrentRoute(child)
  })
}

/**
 * Convenience helper: true if either the parent route OR any child route is active.
 */
const isParentOrChildActive = item => {
  return isCurrentRoute(item.route) || isChildCurrentRoute(item)
}

const collapsedParents = ref({})

/**
 * Generate a stable key for a parent item.
 * Prefer the route name, fallback to the item's name.
 */
const getParentKey = item => item.route || item.name

/**
 * Determine if a parent should be rendered as expanded (children visible).
 */
const isParentExpanded = item => {
  if (!item?.children?.length || !hasVisibleChildren(item)) return false

  const key = getParentKey(item)

  if (isCurrentRoute(item.route)) {
    const collapsed = collapsedParents.value[key] === true
    return !collapsed
  }

  if (isChildCurrentRoute(item)) {
    return true
  }

  return false
}

/**
 * Handle clicks on the parent link.
 */
const onParentClick = (event, item) => {
  const hasChildren = item.children && hasVisibleChildren(item)
  const key = getParentKey(item)

  if (!hasChildren) {
    return
  }

  if (isCurrentRoute(item.route)) {
    event.preventDefault()
    const collapsed = collapsedParents.value[key] === true
    collapsedParents.value[key] = !collapsed
    return
  }

  collapsedParents.value[key] = false
}

/**
 * Check if a section has at least one visible item.
 */
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
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        },
        { type: 'divider' },
      ],
    },
    {
      items: [
        {
          name: 'Charts',
          route: 'chart.index',
          icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        },
        { type: 'divider' },
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
      {
        name: 'System Settings',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />',
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
      { type: 'divider' },
    ],
  }

  if (hasPermission('manage-settings')) {
    items.push(systemSettingsItems)
  }

  return items
})
</script>

<template>
  <aside
    data-sidebar-content
    class="nav-sidebar border-r border-[var(--color-border)]"
    @click.stop
    style="box-shadow: 1px 0 2px rgba(0, 0, 0, 0.05)">
    <nav class="flex-1 overflow-y-auto px-2 py-2" aria-labelledby="nav-heading">
      <ul class="space-y-1">
        <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
          <template v-if="sectionHasVisibleItems(section)">
            <template v-for="(item, itemIndex) in section.items" :key="itemIndex">
              <li v-if="item.type === 'divider'" class="my-1.5 px-2" role="separator">
                <div class="nav-divider"></div>
              </li>

              <li
                v-else
                :class="[
                  item.children && isCurrentRoute(item.route)
                    ? 'nav-item-active bg-[var(--color-surface-muted)]'
                    : '',
                  item.children && !isCurrentRoute(item.route) && isChildCurrentRoute(item)
                    ? 'nav-item-active bg-[var(--color-surface-muted)] opacity-80'
                    : '',
                ]">
                <Link
                  v-if="hasPermission(item.permission) && item.route"
                  :href="route(item.route)"
                  @click="onParentClick($event, item)"
                  :class="[
                    'nav-item transition-colors duration-200 hover:bg-[var(--color-surface-muted)]',
                    isCurrentRoute(item.route)
                      ? 'nav-item-active bg-[var(--color-surface-muted)]'
                      : isChildCurrentRoute(item)
                        ? 'nav-item-active bg-[var(--color-surface-muted)] opacity-80'
                        : 'nav-item-default',
                  ]">
                  <svg
                    :class="[
                      'nav-icon',
                      isParentOrChildActive(item) ? 'nav-icon-active' : 'nav-icon-default',
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                    v-html="item.icon"></svg>

                  <span class="flex-1 text-sm font-medium">
                    {{ item.name }}
                  </span>

                  <svg
                    v-if="item.children && hasVisibleChildren(item)"
                    :class="[
                      'ml-2 h-4 w-4 shrink-0 transition-transform duration-200',
                      isParentExpanded(item) ? 'rotate-180' : '',
                    ]"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </Link>
              </li>

              <li v-if="item.children && hasVisibleChildren(item) && isParentExpanded(item)">
                <ul class="ml-7 space-y-1">
                  <li v-for="child in item.children" :key="child.name">
                    <Link
                      v-if="hasPermission(child.permission)"
                      :href="route(child.route)"
                      @click="onParentClick($event, child)"
                      :class="[
                        'nav-item pl-4 transition-colors duration-200 hover:bg-[var(--color-surface-muted)]',
                        isCurrentRoute(child.route)
                          ? 'nav-item-active bg-[var(--color-surface-muted)]'
                          : 'nav-item-default',
                      ]">
                      <span class="text-sm font-medium">
                        {{ child.name }}
                      </span>
                    </Link>

                    <ul
                      v-if="child.children && hasVisibleChildren(child) && isParentExpanded(child)"
                      class="ml-7 space-y-1">
                      <li v-for="grandchild in child.children" :key="grandchild.name">
                        <Link
                          v-if="hasPermission(grandchild.permission)"
                          :href="route(grandchild.route)"
                          :class="[
                            'nav-item pl-4 transition-colors duration-200 hover:bg-[var(--color-surface-muted)]',
                            isCurrentRoute(grandchild.route)
                              ? 'nav-item-active bg-[var(--color-surface-muted)]'
                              : 'nav-item-default',
                          ]">
                          <span class="text-sm font-medium">
                            {{ grandchild.name }}
                          </span>
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

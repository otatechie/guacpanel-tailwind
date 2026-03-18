<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CommandPaletteItem from './CommandPaletteItem.vue'
import FederatedSearch from '@js/Components/Typesense/FederatedSearch.vue'
import axios from 'axios'

const page = usePage()
const isOpen = ref(false)
const query = ref('')
const selectedIndex = ref(0)
const inputRef = ref(null)

const typesenseApiKey = ref(null)
const hasValidApiKey = ref(false)
const typesenseResults = ref([])
const isTypesenseSearching = ref(false)

const user = computed(() => page.props.auth?.user)

const hasPermission = permissionName =>
    !permissionName || (user.value?.permissions?.includes(permissionName) ?? false)

const pagesConfig = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home', keywords: ['home', 'main'] },
    {
        name: 'Charts',
        route: 'chart.index',
        icon: 'chart',
        keywords: ['analytics', 'graphs', 'data'],
    },
]

const conditionalPages = [
    {
        name: 'Admin Notifications',
        route: 'admin.notifications.index',
        icon: 'bell',
        permission: 'manage-notifications',
        keywords: ['alerts', 'messages'],
        condition: () => page.props.settings?.notificationEnabled,
    },
]

const settingsPages = [
    {
        name: 'System Settings',
        route: 'admin.setting.index',
        icon: 'cog',
        keywords: ['config', 'preferences'],
    },
    {
        name: 'System Activity',
        route: 'admin.audit.index',
        icon: 'activity',
        keywords: ['logs', 'audit'],
    },
    {
        name: 'Theme Settings',
        route: 'admin.personalization.index',
        icon: 'palette',
        keywords: ['colors', 'appearance'],
    },
    {
        name: 'User Management',
        route: 'admin.user.index',
        icon: 'users',
        keywords: ['accounts', 'members'],
    },
    {
        name: 'Data Backup',
        route: 'admin.backup.index',
        icon: 'database',
        keywords: ['restore', 'export'],
    },
    {
        name: 'Access Control',
        route: 'admin.permission.role.index',
        icon: 'shield',
        keywords: ['roles', 'permissions'],
    },
    {
        name: 'Login History',
        route: 'admin.login.history.index',
        icon: 'history',
        keywords: ['access', 'logins'],
    },
    {
        name: 'Security Settings',
        route: 'admin.setting.show',
        icon: 'lock',
        keywords: ['password', 'auth'],
    },
    {
        name: 'Session Management',
        route: 'admin.sessions.index',
        icon: 'monitor',
        keywords: ['devices', 'active'],
    },
    {
        name: 'Health Status',
        route: 'admin.health.index',
        icon: 'heart',
        keywords: ['status', 'monitoring'],
    },
]

const actionsConfig = [
    {
        name: 'Toggle Dark Mode',
        action: 'toggleDarkMode',
        icon: 'moon',
        keywords: ['theme', 'light', 'dark', 'night'],
    },
    {
        name: 'View My Profile',
        route: 'user.account.index',
        icon: 'user',
        keywords: ['account', 'settings'],
    },
    {
        name: 'Two-Factor Authentication',
        route: 'user.two.factor.authentication.index',
        icon: 'shield',
        keywords: ['2fa', 'security', 'totp'],
    },
    {
        name: 'Active Sessions',
        route: 'user.session.index',
        icon: 'monitor',
        keywords: ['devices', 'logged in'],
    },
]

const allPages = computed(() => {
    const pages = [...pagesConfig]

    conditionalPages.forEach(p => {
        if (p.condition?.() !== false) {
            pages.push(p)
        }
    })

    if (hasPermission('manage-settings')) {
        pages.push(...settingsPages)
    }

    return pages.filter(p => !p.permission || hasPermission(p.permission))
})

const allActions = computed(() => {
    const actions = [...actionsConfig]

    if (user.value) {
        actions.push({
            name: 'Logout',
            action: 'logout',
            icon: 'logout',
            keywords: ['sign out', 'exit'],
        })
    }

    return actions
})

const fuzzyMatch = (text, pattern) => {
    if (!pattern) return true
    const lowerText = text.toLowerCase()
    const lowerPattern = pattern.toLowerCase()

    if (lowerText.includes(lowerPattern)) return true

    let patternIdx = 0
    for (let i = 0; i < lowerText.length && patternIdx < lowerPattern.length; i++) {
        if (lowerText[i] === lowerPattern[patternIdx]) {
            patternIdx++
        }
    }
    return patternIdx === lowerPattern.length
}

const filterByQuery = (items, q) => {
    if (!q) return items.slice(0, 5)
    return items.filter(item => {
        const searchText = [item.name, ...(item.keywords || [])].join(' ')
        return fuzzyMatch(searchText, q)
    })
}

const filteredPages = computed(() => filterByQuery(allPages.value, query.value))
const filteredActions = computed(() => filterByQuery(allActions.value, query.value))

const allFilteredItems = computed(() => {
    const items = []

    filteredActions.value.forEach(action => {
        items.push({ ...action, type: 'action' })
    })

    filteredPages.value.forEach(p => {
        items.push({ ...p, type: 'page' })
    })

    typesenseResults.value.forEach(result => {
        items.push({
            name: result.displayTitle,
            subtitle: result.displaySubtitle,
            url: result.url,
            icon: result.collection_name === 'users' ? 'user' : 'database',
            type: 'data',
        })
    })

    return items
})

const hasResults = computed(() => allFilteredItems.value.length > 0)

const actionHandlers = {
    toggleDarkMode: () => {
        document.documentElement.classList.toggle('dark')
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'))
    },
    logout: () => {
        router.post(route('logout'))
    },
}

const executeItem = item => {
    close()

    if (item.action && actionHandlers[item.action]) {
        actionHandlers[item.action]()
    } else if (item.url) {
        router.visit(item.url)
    } else if (item.route) {
        router.visit(route(item.route))
    }
}

const close = () => {
    isOpen.value = false
}

const handleKeyDown = e => {
    const handlers = {
        ArrowDown: () => {
            e.preventDefault()
            selectedIndex.value =
                selectedIndex.value < allFilteredItems.value.length - 1
                    ? selectedIndex.value + 1
                    : 0
        },
        ArrowUp: () => {
            e.preventDefault()
            selectedIndex.value =
                selectedIndex.value > 0
                    ? selectedIndex.value - 1
                    : allFilteredItems.value.length - 1
        },
        Enter: () => {
            e.preventDefault()
            const item = allFilteredItems.value[selectedIndex.value]
            if (item) executeItem(item)
        },
        Escape: () => {
            e.preventDefault()
            close()
        },
    }

    handlers[e.key]?.()
}

const handleGlobalKeyDown = e => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault()
        isOpen.value = !isOpen.value
    }
}

const handleOverlayClick = e => {
    if (e.target === e.currentTarget) close()
}

const fetchTypesenseApiKey = async () => {
    try {
        const response = await axios.get('/typesense/scoped-key')
        if (response?.data?.apiKey) {
            typesenseApiKey.value = response.data.apiKey
            hasValidApiKey.value = true
        }
    } catch {
        hasValidApiKey.value = false
    }
}

const handleTypesenseResults = results => {
    typesenseResults.value = results
}

const handleTypesenseSearching = searching => {
    isTypesenseSearching.value = searching
}

watch(allFilteredItems, () => {
    selectedIndex.value = 0
})

watch(isOpen, async open => {
    if (open) {
        query.value = ''
        selectedIndex.value = 0
        typesenseResults.value = []
        await nextTick()
        inputRef.value?.focus()
    }
})

onMounted(() => {
    document.addEventListener('keydown', handleGlobalKeyDown)
    fetchTypesenseApiKey()
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleGlobalKeyDown)
})
</script>

<template>
    <Teleport to="body">
        <Transition name="command-palette">
            <div
                v-if="isOpen"
                class="command-palette-overlay"
                role="dialog"
                aria-modal="true"
                aria-label="Command palette"
                @click="handleOverlayClick">
                <div class="command-palette-modal" @click.stop>
                    <div class="command-palette-header">
                        <svg
                            class="command-palette-search-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>

                        <input
                            ref="inputRef"
                            v-model="query"
                            type="text"
                            class="command-palette-input"
                            placeholder="Search pages, actions, or data..."
                            autocomplete="off"
                            @keydown="handleKeyDown" />

                        <kbd class="command-palette-kbd">ESC</kbd>
                    </div>

                    <div class="command-palette-results" role="listbox">
                        <div
                            v-if="isTypesenseSearching && query.length > 0"
                            class="command-palette-loading">
                            <div class="command-palette-spinner" />
                        </div>

                        <div
                            v-else-if="!hasResults && query.length > 0"
                            class="command-palette-empty">
                            No results found for "{{ query }}"
                        </div>

                        <template v-else>
                            <div v-if="filteredActions.length > 0" class="command-group">
                                <div class="command-group-label">Quick Actions</div>
                                <CommandPaletteItem
                                    v-for="(action, i) in filteredActions"
                                    :key="`action-${i}`"
                                    :item="{ ...action, type: 'action' }"
                                    :selected="selectedIndex === i"
                                    @select="executeItem" />
                            </div>

                            <div v-if="filteredPages.length > 0" class="command-group">
                                <div class="command-group-label">Pages</div>
                                <CommandPaletteItem
                                    v-for="(p, i) in filteredPages"
                                    :key="`page-${i}`"
                                    :item="{ ...p, type: 'page' }"
                                    :selected="selectedIndex === filteredActions.length + i"
                                    @select="executeItem" />
                            </div>

                            <div v-if="typesenseResults.length > 0" class="command-group">
                                <div class="command-group-label">Search Results</div>
                                <CommandPaletteItem
                                    v-for="(result, i) in typesenseResults"
                                    :key="`data-${i}`"
                                    :item="{
                                        name: result.displayTitle,
                                        subtitle: result.displaySubtitle,
                                        url: result.url,
                                        icon:
                                            result.collection_name === 'users'
                                                ? 'user'
                                                : 'database',
                                        type: 'data',
                                    }"
                                    :selected="
                                        selectedIndex ===
                                        filteredActions.length + filteredPages.length + i
                                    "
                                    @select="executeItem" />
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </Transition>

        <FederatedSearch
            v-if="hasValidApiKey && typesenseApiKey && query.length >= 2"
            :search-query="query"
            :typesense-api-key="typesenseApiKey"
            @update:results="handleTypesenseResults"
            @searching="handleTypesenseSearching" />
    </Teleport>
</template>

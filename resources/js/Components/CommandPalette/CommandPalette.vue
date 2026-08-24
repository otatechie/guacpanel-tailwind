<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted, useId } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CommandPaletteItem from './CommandPaletteItem.vue'
import FederatedSearch from '@js/Components/Typesense/FederatedSearch.vue'
import axios from 'axios'
import { SearchIcon } from '@lucide/vue'
import { useCommandPalette } from '@js/composables/useCommandPalette'
import { usePermissions } from '@js/composables/usePermissions'
import { workspaceNav, accountNav, adminNav } from '@js/navigation'

const page = usePage()
// Shared with the header trigger, which is now the only other way in.
const { isOpen, open, close } = useCommandPalette()
const query = ref('')
const selectedIndex = ref(0)
const inputRef = ref(null)
const listboxId = useId()
const restoreFocusTo = ref(null)

const typesenseApiKey = ref(null)
const hasValidApiKey = ref(false)
const typesenseResults = ref([])
const isTypesenseSearching = ref(false)
const isDark = ref(false)

const { user, hasPermission } = usePermissions()

/* One manifest, shared with the sidebar. Removing a feature means deleting its
   entry in resources/js/navigation.js, not editing this file and the sidebar. */
const isAvailable = item =>
    (!item.feature || Boolean(page.props.settings?.[item.feature])) &&
    hasPermission(item.permission)

const allPages = computed(() => {
    const pages = [...workspaceNav, ...(user.value ? accountNav : [])]

    if (hasPermission('manage-settings')) {
        pages.push(...adminNav)
    }

    return pages.filter(isAvailable)
})

const allActions = computed(() => {
    const actions = [
        {
            // "Toggle Dark Mode" never said which way it was about to go.
            name: isDark.value ? 'Switch to light mode' : 'Switch to dark mode',
            action: 'toggleDarkMode',
            icon: 'moon',
            keywords: ['theme', 'light', 'dark', 'night', 'toggle'],
        },
    ]

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

    filteredPages.value.forEach(p => {
        items.push({ ...p })
    })

    filteredActions.value.forEach(action => {
        items.push({ ...action })
    })

    typesenseResults.value.forEach(result => {
        items.push({
            name: result.displayTitle,
            subtitle: result.displaySubtitle,
            url: result.url,
            icon: result.collection_name === 'users' ? 'user' : 'database',
        })
    })

    return items
})

const hasResults = computed(() => allFilteredItems.value.length > 0)

const actionHandlers = {
    toggleDarkMode: () => {
        isDark.value = document.documentElement.classList.toggle('dark')
        localStorage.setItem('darkMode', isDark.value)
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
        open()
        return
    }

    /* aria-modal="true" was a claim nothing enforced — Tab walked straight out
       into the page behind. The input is the only tabbable thing in here by
       design (options are driven by aria-activedescendant, not by Tab), so the
       whole trap is: Tab goes nowhere. */
    if (e.key === 'Tab' && isOpen.value) {
        e.preventDefault()
        inputRef.value?.focus()
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

const optionId = index => `${listboxId}-option-${index}`

// The results pane scrolls; without this the highlight walks off the bottom and
// the arrow keys appear to stop working.
watch(selectedIndex, async index => {
    await nextTick()
    document.getElementById(optionId(index))?.scrollIntoView({ block: 'nearest' })
})

/* Teleported to body, so the app root sits outside the palette and can be taken
   out of the accessibility tree wholesale. The Tab trap stops keyboard focus
   escaping; this stops a screen reader's virtual cursor wandering the page
   behind an overlay that calls itself modal. */
const setBackgroundInert = inert => document.getElementById('app')?.toggleAttribute('inert', inert)

watch(isOpen, async opened => {
    setBackgroundInert(opened)

    if (opened) {
        // Whatever had focus gets it back on close — otherwise dismissing the
        // palette dumps keyboard users at the top of the document.
        restoreFocusTo.value = document.activeElement
        query.value = ''
        selectedIndex.value = 0
        typesenseResults.value = []
        await nextTick()
        inputRef.value?.focus()
        return
    }

    restoreFocusTo.value?.focus?.()
    restoreFocusTo.value = null
})

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark')
    document.addEventListener('keydown', handleGlobalKeyDown)
    fetchTypesenseApiKey()
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleGlobalKeyDown)
    setBackgroundInert(false)
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
                        <SearchIcon class="command-palette-search-icon" aria-hidden="true" />

                        <!-- A listbox with `role="option"` rows needs the input
                             to say which one is active; without it a screen
                             reader hears nothing as the arrows move. -->
                        <input
                            ref="inputRef"
                            v-model="query"
                            type="text"
                            class="command-palette-input"
                            placeholder="Search pages, actions, or data..."
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="true"
                            :aria-controls="listboxId"
                            :aria-activedescendant="
                                hasResults ? optionId(selectedIndex) : undefined
                            "
                            @keydown="handleKeyDown" />
                    </div>

                    <div :id="listboxId" class="command-palette-results" role="listbox">
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
                            <!-- Pages first: it puts navigation under the
                                 default selection instead of Logout, and it is
                                 what the palette is reached for most. -->
                            <div v-if="filteredPages.length > 0" class="command-group">
                                <div class="command-group-label">Pages</div>
                                <CommandPaletteItem
                                    v-for="(p, i) in filteredPages"
                                    :key="`page-${i}`"
                                    :id="optionId(i)"
                                    :item="p"
                                    :selected="selectedIndex === i"
                                    @activate="selectedIndex = i"
                                    @select="executeItem" />
                            </div>

                            <div v-if="filteredActions.length > 0" class="command-group">
                                <div class="command-group-label">Actions</div>
                                <CommandPaletteItem
                                    v-for="(action, i) in filteredActions"
                                    :key="`action-${i}`"
                                    :id="optionId(filteredPages.length + i)"
                                    :item="action"
                                    :selected="selectedIndex === filteredPages.length + i"
                                    @activate="selectedIndex = filteredPages.length + i"
                                    @select="executeItem" />
                            </div>

                            <!-- Silence read as "no matches" when the search
                                 backend was simply unreachable. -->
                            <div
                                v-if="!hasValidApiKey && query.length >= 2"
                                class="command-palette-notice">
                                Data search is unavailable — showing pages and actions only.
                            </div>

                            <div v-if="typesenseResults.length > 0" class="command-group">
                                <div class="command-group-label">Results</div>
                                <CommandPaletteItem
                                    v-for="(result, i) in typesenseResults"
                                    :key="`data-${i}`"
                                    :id="
                                        optionId(filteredPages.length + filteredActions.length + i)
                                    "
                                    :item="{
                                        name: result.displayTitle,
                                        subtitle: result.displaySubtitle,
                                        url: result.url,
                                        icon:
                                            result.collection_name === 'users'
                                                ? 'user'
                                                : 'database',
                                    }"
                                    :selected="
                                        selectedIndex ===
                                        filteredPages.length + filteredActions.length + i
                                    "
                                    @activate="
                                        selectedIndex =
                                            filteredPages.length + filteredActions.length + i
                                    "
                                    @select="executeItem" />
                            </div>
                        </template>
                    </div>

                    <!-- ESC was the only shortcut on show, and it is the one
                         everybody already knows. -->
                    <div class="command-palette-footer">
                        <span>
                            <kbd class="command-palette-kbd">↑</kbd>
                            <kbd class="command-palette-kbd">↓</kbd>
                            navigate
                        </span>
                        <span>
                            <kbd class="command-palette-kbd">↵</kbd>
                            open
                        </span>
                        <span>
                            <kbd class="command-palette-kbd">esc</kbd>
                            close
                        </span>
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

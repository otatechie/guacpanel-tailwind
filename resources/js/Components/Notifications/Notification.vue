<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { BellIcon, XIcon } from '@lucide/vue'
import apiFetch from '@js/utils/apiFetch'
import DropdownMenu from '@js/Components/DropdownMenu.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const notificationsOpen = ref(false)
const notifications = ref([])
const isLoading = ref(false)

let userChannel = null
let systemChannel = null
let releaseChannel = null
let reconcileTimer = null

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length)
const hasAnyNotifications = computed(() => notifications.value.length > 0)
const hasUnreadNotifications = computed(() => unreadCount.value > 0)

const permissions = computed(() => page.props?.auth?.user?.permissions ?? [])
const canViewAll = computed(() => permissions.value.includes('view-notifications'))

const typeToPriority = type => {
    if (type === 'error') return 'critical'
    if (type === 'warning') return 'high'
    if (type === 'success') return 'normal'
    return 'low'
}

const relativeTime = iso => {
    if (!iso) return ''
    const then = new Date(iso).getTime()
    const now = Date.now()
    const diff = Math.max(0, Math.floor((now - then) / 1000))

    if (diff < 10) return 'just now'
    if (diff < 60) return `${diff}s ago`
    const mins = Math.floor(diff / 60)
    if (mins < 60) return `${mins}m ago`
    const hrs = Math.floor(mins / 60)
    if (hrs < 24) return `${hrs}h ago`
    const days = Math.floor(hrs / 24)
    return `${days}d ago`
}

const normalize = n => ({
    id: n.id,
    title:
        n.title ||
        (n.scope === 'system' ? 'System' : n.scope === 'release' ? 'Release' : 'Notification'),
    description: n.message,
    created_at: n.created_at,
    time: relativeTime(n.created_at),
    is_read: !!n.is_read,
    read_at: n.read_at ?? null,
    is_dismissed: !!n.is_dismissed,
    dismissed_at: n.dismissed_at ?? null,
    scope: n.scope,
    type: n.type,
    priority: typeToPriority(n.type),
    data: n.data ?? null,
})

const hydrateFromPageProps = () => {
    const initial = page.props?.notifications?.data

    notifications.value = Array.isArray(initial)
        ? initial.filter(n => !n.is_dismissed).map(normalize)
        : []
}

const fetchNotifications = async ({ silent = false } = {}) => {
    if (!silent) {
        isLoading.value = true
    }

    try {
        const res = await apiFetch('/notifications')

        if (!res.ok) {
            return
        }

        const json = await res.json()
        const next = Array.isArray(json?.data) ? json.data.map(normalize) : []
        notifications.value = next.filter(n => !n.is_dismissed)
    } finally {
        if (!silent) {
            isLoading.value = false
        }
    }
}

/* The menu owns the open state, so the refresh-on-open hangs off the state
   changing rather than off the click that changed it. */
watch(notificationsOpen, async open => {
    if (open) await fetchNotifications({ silent: true })
})

const markAsRead = async (notification, event) => {
    event?.stopPropagation()

    if (!notification || notification.is_read) return

    notification.is_read = true

    const res = await apiFetch(`/notifications/${notification.id}/read`, {
        method: 'POST',
        body: JSON.stringify({}),
    })

    if (!res.ok) {
        notification.is_read = false
    }
}

const markAllRead = async event => {
    event?.preventDefault()
    event?.stopPropagation()

    if (!hasUnreadNotifications.value) return

    const original = notifications.value
    notifications.value = notifications.value.map(n => ({ ...n, is_read: true }))

    const res = await apiFetch('/notifications/read-all', {
        method: 'POST',
        body: JSON.stringify({}),
    })

    if (!res.ok) {
        notifications.value = original
    }
}

const dismissNotification = async (notification, event) => {
    event?.preventDefault()
    event?.stopPropagation()

    if (!notification?.id) return

    const original = notifications.value
    notifications.value = notifications.value.filter(n => n.id !== notification.id)

    const res = await apiFetch(`/notifications/${notification.id}/dismiss`, {
        method: 'POST',
        body: JSON.stringify({}),
    })

    if (!res.ok) {
        notifications.value = original
    }
}

const dismissAll = async event => {
    event?.preventDefault()
    event?.stopPropagation()

    if (!hasAnyNotifications.value) return

    const original = notifications.value
    notifications.value = []

    const res = await apiFetch('/notifications/dismiss-all', {
        method: 'POST',
        body: JSON.stringify({}),
    })

    if (!res.ok) {
        notifications.value = original
    }
}

const getPayloadId = payload =>
    payload?.id ?? payload?.notification_id ?? payload?.app_notification_id

const isDeleteAction = action => {
    if (!action) return false
    return ['delete', 'deleted', 'destroy', 'destroyed', 'remove', 'removed'].includes(
        String(action)
    )
}

const handleStateChanged = payload => {
    const id = getPayloadId(payload)
    if (!id) return

    if (isDeleteAction(payload.action) || payload.action === 'dismiss') {
        notifications.value = notifications.value.filter(n => n.id !== id)
        return
    }

    if (payload.action === 'undismiss') {
        fetchNotifications({ silent: true })
        return
    }

    const idx = notifications.value.findIndex(n => n.id === id)

    if (idx === -1) {
        fetchNotifications({ silent: true })
        return
    }

    const next = { ...notifications.value[idx] }

    if (payload.action === 'read') {
        next.is_read = true
        next.read_at = payload.read_at ?? next.read_at
    }

    if (payload.action === 'unread') {
        next.is_read = false
        next.read_at = null
    }

    notifications.value.splice(idx, 1, next)
}

const handleBulkChanged = payload => {
    if (!payload?.action) return

    if (payload.action === 'dismiss-all') {
        notifications.value = []
        return
    }

    if (payload.action === 'read-all') {
        notifications.value = notifications.value.map(n => ({ ...n, is_read: true }))
        return
    }

    fetchNotifications({ silent: true })
}

const priorityIconClass = priority => {
    if (priority === 'critical') return 'text-red-500'
    if (priority === 'high') return 'text-yellow-500'
    if (priority === 'normal') return 'text-blue-500'
    return 'text-muted-foreground'
}

const closeDropdown = () => {
    notificationsOpen.value = false
}

const subscribeRealtime = () => {
    if (!window.Echo || !props.user?.id) return

    userChannel = window.Echo.private(`users.${props.user.id}`)
        .listen('.app-notification.created', handleStateChanged)
        .listen('.app-notification.state', handleStateChanged)
        .listen('.app-notification.bulk', handleBulkChanged)

    systemChannel = window.Echo.private('system')
        .listen('.app-notification.created', handleStateChanged)
        .listen('.app-notification.state', handleStateChanged)
        .listen('.app-notification.bulk', handleBulkChanged)

    releaseChannel = window.Echo.private('release')
        .listen('.app-notification.created', handleStateChanged)
        .listen('.app-notification.state', handleStateChanged)
        .listen('.app-notification.bulk', handleBulkChanged)
}

const unsubscribeRealtime = () => {
    if (!window.Echo || !props.user?.id) return

    window.Echo.leave(`private-users.${props.user.id}`)
    window.Echo.leave('private-system')
    window.Echo.leave('private-release')

    userChannel = null
    systemChannel = null
    releaseChannel = null
}

const startReconcile = () => {
    if (reconcileTimer) return

    reconcileTimer = setInterval(() => {
        fetchNotifications({ silent: true })
    }, 60000)
}

const stopReconcile = () => {
    if (!reconcileTimer) return
    clearInterval(reconcileTimer)
    reconcileTimer = null
}

const handleAppRefresh = () => {
    fetchNotifications({ silent: true })
}

onMounted(async () => {
    window.addEventListener('app-notifications:refresh', handleAppRefresh)

    hydrateFromPageProps()
    subscribeRealtime()
    startReconcile()

    setTimeout(() => {
        fetchNotifications({ silent: true })
    }, 750)
})

onUnmounted(() => {
    window.removeEventListener('app-notifications:refresh', handleAppRefresh)

    stopReconcile()
    unsubscribeRealtime()
})
</script>

<template>
    <!-- Near-full-bleed on a phone, a fixed panel on larger screens. Expressed as
         a width so the menu's own collision handling keeps it on screen,
         rather than as a `fixed` override fighting its positioning. -->
    <DropdownMenu
        v-model:open="notificationsOpen"
        align="end"
        width="w-[calc(100vw-1.5rem)] sm:w-80"
        class="overflow-hidden">
        <template #trigger>
            <button type="button" class="nav-bar-btn relative" aria-label="Notifications">
                <BellIcon class="nav-bar-icon" aria-hidden="true" />
                <span
                    v-if="unreadCount > 0"
                    class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-semibold text-white">
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
                <span class="nav-bar-tooltip">Notifications</span>
            </button>
        </template>

        <div>
            <!-- Header -->
            <div class="border-border flex items-center justify-between border-b px-4 py-2.5">
                <h3 class="text-foreground text-sm font-semibold">Notifications</h3>
                <div class="flex items-center gap-3">
                    <button
                        v-if="hasUnreadNotifications"
                        type="button"
                        class="text-muted-foreground hover:text-foreground cursor-pointer text-xs"
                        @click="markAllRead">
                        Read all
                    </button>
                    <button
                        v-if="hasAnyNotifications"
                        type="button"
                        class="text-muted-foreground hover:text-foreground cursor-pointer text-xs"
                        @click="dismissAll">
                        Clear
                    </button>
                </div>
            </div>

            <!-- List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="isLoading" class="text-muted-foreground px-4 py-6 text-center text-xs">
                    Loading...
                </div>

                <div
                    v-else-if="notifications.length === 0"
                    class="text-muted-foreground px-4 py-8 text-center text-xs">
                    No notifications
                </div>

                <div v-else class="divide-border divide-y">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="hover:bg-muted flex gap-3 px-4 py-3 transition-colors"
                        :class="!n.is_read ? 'cursor-pointer' : ''"
                        @click="markAsRead(n, $event)">
                        <!-- Priority dot -->
                        <span
                            class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                            :class="
                                priorityIconClass(n.priority)?.replace('text-', 'bg-') ||
                                'bg-border'
                            " />

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h4
                                    class="text-foreground truncate text-sm"
                                    :class="!n.is_read ? 'font-medium' : ''">
                                    {{ n.title }}
                                </h4>
                                <button
                                    type="button"
                                    class="text-muted-foreground hover:text-foreground shrink-0 cursor-pointer rounded p-0.5"
                                    aria-label="Dismiss"
                                    @click.stop="dismissNotification(n, $event)">
                                    <XIcon class="h-3 w-3" :stroke-width="2" />
                                </button>
                            </div>
                            <p
                                v-if="n.description"
                                class="text-muted-foreground mt-0.5 line-clamp-2 text-xs">
                                {{ n.description }}
                            </p>
                            <time class="text-muted-foreground mt-1 block text-xs">
                                {{ n.time }}
                            </time>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="canViewAll" class="border-border border-t">
                <Link
                    href="/notifications/all"
                    class="text-muted-foreground hover:bg-muted hover:text-foreground block px-4 py-2.5 text-center text-xs transition-colors"
                    @click="closeDropdown">
                    View all
                </Link>
            </div>
        </div>
    </DropdownMenu>
</template>

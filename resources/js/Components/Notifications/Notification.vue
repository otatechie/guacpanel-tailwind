<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import apiFetch from '@js/utils/apiFetch'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const rootEl = ref(null)

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

const toggleNotifications = async event => {
    event?.preventDefault()
    event?.stopPropagation()

    notificationsOpen.value = !notificationsOpen.value

    if (notificationsOpen.value) {
        await fetchNotifications({ silent: true })
    }
}

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

const undoMarkAsRead = async (notification, event) => {
    event?.preventDefault()
    event?.stopPropagation()

    if (!notification || !notification.is_read) return

    const original = notification.is_read
    notification.is_read = false

    const res = await apiFetch(`/notifications/${notification.id}/unread`, {
        method: 'POST',
        body: JSON.stringify({}),
    })

    if (!res.ok) {
        notification.is_read = original
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

const handleClickAway = event => {
    const el = rootEl.value
    if (!el) return
    if (!el.contains(event.target)) {
        closeDropdown()
    }
}

const handleEscapeKey = event => {
    if (event.key === 'Escape') {
        closeDropdown()
    }
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
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleEscapeKey)
    window.addEventListener('app-notifications:refresh', handleAppRefresh)

    hydrateFromPageProps()
    subscribeRealtime()
    startReconcile()

    setTimeout(() => {
        fetchNotifications({ silent: true })
    }, 750)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleEscapeKey)
    window.removeEventListener('app-notifications:refresh', handleAppRefresh)

    stopReconcile()
    unsubscribeRealtime()
})
</script>

<template>
    <div ref="rootEl" class="relative">
        <!-- Bell button -->
        <button
            type="button"
            data-notification-button
            class="nav-bar-btn relative"
            aria-label="Notifications"
            :aria-expanded="notificationsOpen"
            @click="toggleNotifications">
            <svg class="nav-bar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span v-if="unreadCount > 0" class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-semibold text-white">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
            <span class="nav-bar-tooltip">Notifications</span>
        </button>

        <!-- Dropdown -->
        <div
            v-show="notificationsOpen"
            data-notification-dropdown
            class="fixed inset-x-3 top-[80px] z-50 overflow-hidden rounded-lg border border-border bg-card shadow-lg sm:absolute sm:inset-auto sm:top-auto sm:right-0 sm:mt-2 sm:w-80"
            @click.stop>

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-border px-4 py-2.5">
                <h3 class="text-sm font-semibold text-foreground">Notifications</h3>
                <div class="flex items-center gap-3">
                    <button v-if="hasUnreadNotifications" type="button" class="cursor-pointer text-xs text-muted-foreground hover:text-foreground" @click="markAllRead">Read all</button>
                    <button v-if="hasAnyNotifications" type="button" class="cursor-pointer text-xs text-muted-foreground hover:text-foreground" @click="dismissAll">Clear</button>
                </div>
            </div>

            <!-- List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="isLoading" class="px-4 py-6 text-center text-xs text-muted-foreground">Loading...</div>

                <div v-else-if="notifications.length === 0" class="px-4 py-8 text-center text-xs text-muted-foreground">No notifications</div>

                <div v-else class="divide-y divide-border">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="flex gap-3 px-4 py-3 transition-colors hover:bg-muted"
                        :class="!n.is_read ? 'cursor-pointer' : ''"
                        @click="markAsRead(n, $event)">

                        <!-- Priority dot -->
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full" :class="priorityIconClass(n.priority)?.replace('text-', 'bg-') || 'bg-border'" />

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="truncate text-sm text-foreground" :class="!n.is_read ? 'font-medium' : ''">{{ n.title }}</h4>
                                <button
                                    type="button"
                                    class="shrink-0 cursor-pointer rounded p-0.5 text-muted-foreground hover:text-foreground"
                                    aria-label="Dismiss"
                                    @click.stop="dismissNotification(n, $event)">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <p v-if="n.description" class="mt-0.5 line-clamp-2 text-xs text-muted-foreground">{{ n.description }}</p>
                            <time class="mt-1 block text-xs text-muted-foreground">{{ n.time }}</time>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="canViewAll" class="border-t border-border">
                <Link
                    href="/notifications/all"
                    class="block px-4 py-2.5 text-center text-xs text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    @click="closeDropdown">
                    View all
                </Link>
            </div>
        </div>
    </div>
</template>

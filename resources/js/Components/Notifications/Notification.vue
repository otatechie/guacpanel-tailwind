<!-- resources/js/Components/Notifications/Notification.vue -->
<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
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
let reconcileTimer = null

let removeInertiaStart = null
let removeInertiaNavigate = null

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length)
const hasAnyNotifications = computed(() => notifications.value.length > 0)
const hasUnreadNotifications = computed(() => unreadCount.value > 0)

const permissions = computed(() => page.props?.auth?.user?.permissions ?? [])
const canViewAll = computed(() => permissions.value.includes('view-notifications'))

const isViewAllActive = computed(() => {
  const url = page.url || ''
  return url === '/notifications/all' || url.startsWith('/notifications/all?')
})

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
  title: n.title || (n.scope === 'system' ? 'System' : 'Notification'),
  description: n.message,
  created_at: n.created_at,
  time: relativeTime(n.created_at),
  is_read: !!n.is_read,
  read_at: n.read_at,
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
    notifications.value = next
  } finally {
    if (!silent) {
      isLoading.value = false
    }
  }
}

const toggleNotifications = async () => {
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
  if (event) {
    event.preventDefault()
    event.stopPropagation()
  }

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

const upsertIncoming = payload => {
  if (!payload?.id) return

  const item = normalize({
    id: payload.id,
    scope: payload.scope,
    type: payload.type,
    title: payload.title,
    message: payload.message,
    data: payload.data,
    created_at: payload.created_at,
    read_at: payload.read_at ?? null,
    is_read: false,
  })

  const existingIndex = notifications.value.findIndex(n => n.id === item.id)

  if (existingIndex >= 0) {
    notifications.value.splice(existingIndex, 1, item)
    return
  }

  notifications.value.unshift(item)
  notifications.value = notifications.value.slice(0, 25)
}

const handleStateChanged = payload => {
  if (!payload?.id) return

  if (payload.action === 'deleted') {
    notifications.value = notifications.value.filter(n => n.id !== payload.id)
    return
  }

  if (payload.action === 'dismiss') {
    notifications.value = notifications.value.filter(n => n.id !== payload.id)
    return
  }

  if (payload.action === 'undismiss') {
    fetchNotifications({ silent: true })
    return
  }

  const idx = notifications.value.findIndex(n => n.id === payload.id)

  if (idx === -1) {
    fetchNotifications({ silent: true })
    return
  }

  const next = { ...notifications.value[idx] }

  if (payload.action === 'read') next.is_read = true
  if (payload.action === 'unread') next.is_read = false

  notifications.value.splice(idx, 1, next)
}

const handleBulkChanged = payload => {
  if (!payload?.action) return
  fetchNotifications({ silent: true })
}

const scopeIconName = scope => {
  if (scope === 'system') return 'cpu'
  if (scope === 'user') return 'user'
  return 'tag'
}

const priorityIconName = priority => {
  if (priority === 'critical') return 'x-circle'
  if (priority === 'high') return 'exclamation-triangle'
  if (priority === 'normal') return 'information-circle'
  return 'bell'
}

const priorityIconClass = priority => {
  if (priority === 'critical') return 'text-red-500'
  if (priority === 'high') return 'text-yellow-500'
  if (priority === 'normal') return 'text-blue-500'
  return 'text-[var(--color-text-muted)]'
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
    .listen('.app-notification.created', upsertIncoming)
    .listen('.app-notification.state', handleStateChanged)
    .listen('.app-notification.bulk', handleBulkChanged)

  systemChannel = window.Echo.private('system').listen('.app-notification.created', upsertIncoming)
}

const unsubscribeRealtime = () => {
  if (!window.Echo || !props.user?.id) return

  window.Echo.leave(`private-users.${props.user.id}`)
  window.Echo.leave('private-system')

  userChannel = null
  systemChannel = null
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

const subscribeNavigationClose = () => {
  removeInertiaStart = router.on('start', () => closeDropdown())
  removeInertiaNavigate = router.on('navigate', () => closeDropdown())
}

const unsubscribeNavigationClose = () => {
  if (typeof removeInertiaStart === 'function') removeInertiaStart()
  if (typeof removeInertiaNavigate === 'function') removeInertiaNavigate()
  removeInertiaStart = null
  removeInertiaNavigate = null
}

onMounted(async () => {
  document.addEventListener('click', handleClickAway)
  document.addEventListener('keydown', handleEscapeKey)

  subscribeNavigationClose()

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

  unsubscribeNavigationClose()

  stopReconcile()
  unsubscribeRealtime()
})
</script>

<template>
  <div ref="rootEl" class="relative">
    <button
      type="button"
      data-notification-button
      class="group relative cursor-pointer rounded-lg p-1.5 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)] focus:outline-none"
      aria-label="Notifications"
      :aria-expanded="notificationsOpen"
      @click="toggleNotifications">
      <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>

      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 inline-flex min-h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>

      <span
        class="absolute -bottom-8 left-1/2 -translate-x-1/2 rounded bg-[var(--color-text)] px-2 py-1 text-xs whitespace-nowrap text-[var(--color-bg)] opacity-0 transition-opacity group-hover:opacity-100">
        Notifications
      </span>
    </button>

    <div
      v-show="notificationsOpen"
      data-notification-dropdown
      class="ring-opacity-5 absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-[var(--color-surface)] shadow-lg ring-1 ring-[var(--color-border)]">
      <div
        class="flex items-center justify-between rounded-t-xl border-b border-[var(--color-border)] bg-gray-100 px-4 py-3 dark:bg-gray-900">
        <h3 class="text-sm font-semibold text-[var(--color-text)]">Notifications</h3>

        <div class="flex items-center gap-2">
          <button
            v-if="hasUnreadNotifications"
            type="button"
            class="text-xxs cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
            @click="markAllRead">
            Mark all read
          </button>

          <button
            v-if="hasAnyNotifications"
            type="button"
            class="text-xxs cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
            @click="dismissAll">
            Dismiss all
          </button>
        </div>
      </div>

      <div class="flex max-h-96 flex-col">
        <div class="min-h-0 flex-1 overflow-y-auto">
          <div v-if="isLoading" class="px-4 py-3 text-sm text-[var(--color-text-muted)]">
            Loading…
          </div>

          <div
            v-else-if="notifications.length === 0"
            class="flex flex-col items-center justify-center gap-2 px-4 py-5 text-sm text-[var(--color-text-muted)]">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-10">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
            </svg>
            No new notifications
          </div>

          <ul v-else class="divide-y divide-gray-400">
            <li
              v-for="notification in notifications"
              :key="notification.id"
              class="px-4 py-3 transition-colors hover:bg-[var(--color-surface-muted)]"
              :class="{
                'bg-blue-50/50 dark:bg-blue-900/20': !notification.is_read,
                'border-l-4': true,
                'border-red-500': notification.priority === 'critical',
                'border-yellow-500': notification.priority === 'high',
                'border-blue-500': notification.priority === 'normal',
                'border-gray-500': notification.priority === 'low',
                'cursor-pointer': notification.is_read == false,
                'cursor-default': notification.is_read == true,
              }"
              @click="markAsRead(notification, $event)">
              <div class="flex gap-3">
                <div class="min-w-0 flex-1">
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-2">
                      <span
                        class="mt-0.5 shrink-0 text-[var(--color-text-muted)]"
                        aria-hidden="true">
                        <svg
                          v-if="scopeIconName(notification.scope) === 'user'"
                          class="size-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                          <path d="M4.5 20.25a7.5 7.5 0 0115 0" />
                        </svg>

                        <svg
                          v-else-if="scopeIconName(notification.scope) === 'cpu'"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="size-4">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                        </svg>

                        <svg
                          v-else
                          class="size-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path
                            d="M9.568 3.75H6.75A3 3 0 003.75 6.75v2.818a3 3 0 00.879 2.121l8.38 8.38a3 3 0 004.242 0l2.999-2.999a3 3 0 000-4.242l-8.38-8.38A3 3 0 009.568 3.75z" />
                          <path d="M7.5 7.5h.008v.008H7.5V7.5z" />
                        </svg>
                      </span>

                      <span class="mt-0.5 shrink-0" aria-hidden="true">
                        <svg
                          v-if="priorityIconName(notification.priority) === 'x-circle'"
                          class="size-4"
                          :class="priorityIconClass(notification.priority)"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M9.75 9.75l4.5 4.5M14.25 9.75l-4.5 4.5" />
                        </svg>

                        <svg
                          v-else-if="
                            priorityIconName(notification.priority) === 'exclamation-triangle'
                          "
                          class="size-4"
                          :class="priorityIconClass(notification.priority)"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path
                            d="M12 9v4m0 4h.01M10.29 3.86a2.25 2.25 0 013.42 0l8.22 10.18A2.25 2.25 0 0120.22 18H3.78a2.25 2.25 0 01-1.71-3.96l8.22-10.18z" />
                        </svg>

                        <svg
                          v-else-if="
                            priorityIconName(notification.priority) === 'information-circle'
                          "
                          class="size-4"
                          :class="priorityIconClass(notification.priority)"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M12 11.25v5.25" />
                          <path d="M12 8.25h.01" />
                        </svg>

                        <svg
                          v-else
                          class="size-4"
                          :class="priorityIconClass(notification.priority)"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M14.25 18.75a2.25 2.25 0 01-4.5 0" />
                          <path d="M18 8.25a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7z" />
                        </svg>
                      </span>

                      <h4 class="min-w-0 truncate text-sm font-medium text-[var(--color-text)]">
                        {{ notification.title }}
                      </h4>
                    </div>

                    <div class="flex items-center gap-1">
                      <button
                        type="button"
                        class="cursor-pointer rounded-md p-1 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                        aria-label="Dismiss notification"
                        @click="dismissNotification(notification, $event)">
                        <svg
                          class="size-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          aria-hidden="true">
                          <path d="M18 6 6 18" />
                          <path d="M6 6 18 18" />
                        </svg>
                      </button>
                      <button
                        v-if="notification.is_read"
                        type="button"
                        class="-mt-0.5 mr-0.5 ml-2 cursor-pointer rounded-md p-0.5 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                        aria-label="Undo mark as read"
                        @click="undoMarkAsRead(notification, $event)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="size-3">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h9a6 6 0 1 1 0 12h-3" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <p class="truncate text-sm text-[var(--color-text-muted)]">
                    {{ notification.description }}
                  </p>
                  <time class="mt-1 text-xs text-[var(--color-text-muted)]">
                    {{ notification.time }}
                  </time>
                </div>

                <div
                  v-if="!notification.is_read"
                  class="mt-0 -mr-0.5 -ml-1 flex h-6 w-6 items-center justify-center rounded bg-gray-900">
                  <div class="h-2.5 w-2.5 rounded-full bg-blue-500" aria-hidden="true"></div>
                </div>
              </div>
            </li>
          </ul>
        </div>

        <div v-if="canViewAll" class="border-t border-[var(--color-border)]">
          <Link
            href="/notifications/all"
            class="btn btn-secondary block rounded-t-none rounded-b-xl text-center text-xs hover:text-[var(--color-text)]"
            :class="
              isViewAllActive
                ? 'font-semibold text-[var(--color-text)]'
                : 'text-[var(--color-text-muted)]'
            "
            @click="closeDropdown">
            View all notifications
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

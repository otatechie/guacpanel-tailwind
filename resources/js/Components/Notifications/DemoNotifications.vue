<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({
    user: { type: Object, required: true },
})

const notificationsOpen = ref(false)
const rootEl = ref(null)

const notifications = ref([
    { id: 1, title: 'New update available', description: 'A new software update is ready to install', time: '5 min ago', read: false, priority: 'high' },
    { id: 2, title: 'Welcome to the platform', description: 'Take a quick tour of the features', time: '1 hour ago', read: false, priority: 'normal' },
    { id: 3, title: 'System maintenance', description: 'Scheduled maintenance in 2 hours', time: '2 hours ago', read: true, priority: 'low' },
])

const unreadCount = ref(notifications.value.filter(n => !n.read).length)

const toggleNotifications = () => { notificationsOpen.value = !notificationsOpen.value }

const markAsRead = id => {
    const n = notifications.value.find(x => x.id === id)
    if (n && !n.read) {
        n.read = true
        unreadCount.value = notifications.value.filter(x => !x.read).length
    }
}

const priorityDot = p => {
    if (p === 'critical') return 'bg-red-500'
    if (p === 'high') return 'bg-amber-500'
    if (p === 'normal') return 'bg-blue-500'
    return 'bg-(--color-border-strong)'
}

const handleClickAway = e => {
    if (rootEl.value && !rootEl.value.contains(e.target)) notificationsOpen.value = false
}
const handleEscape = e => { if (e.key === 'Escape') notificationsOpen.value = false }

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleEscape)
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleEscape)
})
</script>

<template>
    <div ref="rootEl" class="relative">
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
                {{ unreadCount }}
            </span>
            <span class="nav-bar-tooltip">Notifications</span>
        </button>

        <div
            v-show="notificationsOpen"
            data-notification-dropdown
            class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-lg border border-(--card-border) bg-(--color-surface) shadow-lg"
            @click.stop>

            <div class="flex items-center justify-between border-b border-(--card-border) px-4 py-2.5">
                <h3 class="text-sm font-semibold text-(--color-text)">Notifications</h3>
                <span class="text-[10px] text-(--color-text-muted)">Demo</span>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-xs text-(--color-text-muted)">No notifications</div>

                <div v-else class="divide-y divide-(--card-border)">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="flex gap-3 px-4 py-3 transition-colors hover:bg-(--color-surface-muted)"
                        :class="!n.read ? 'cursor-pointer' : ''"
                        @click="markAsRead(n.id)">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full" :class="priorityDot(n.priority)" />
                        <div class="min-w-0 flex-1">
                            <h4 class="truncate text-sm text-(--color-text)" :class="!n.read ? 'font-medium' : ''">{{ n.title }}</h4>
                            <p class="mt-0.5 truncate text-xs text-(--color-text-muted)">{{ n.description }}</p>
                            <time class="mt-1 block text-[10px] text-(--color-text-muted)">{{ n.time }}</time>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

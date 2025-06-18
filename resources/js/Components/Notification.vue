<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const notificationsOpen = ref(false)
const notificationRef = ref(null)

const notifications = ref([
    {
        id: 1,
        title: 'New Update Available',
        description: 'A new software update is available for installation',
        time: '5 min ago',
        read: false,
        priority: 'high'
    },
    {
        id: 2,
        title: 'Welcome to Platform',
        description: 'Thanks for joining! Take a quick tour of our features',
        time: '1 hour ago',
        read: false,
        priority: 'normal'
    },
    {
        id: 3,
        title: 'System Maintenance',
        description: 'Scheduled maintenance in 2 hours',
        time: '2 hours ago',
        read: true,
        priority: 'low'
    }
])

const unreadCount = computed(() =>
    notifications.value.filter(n => !n.read).length
)

const toggleNotifications = () => {
    notificationsOpen.value = !notificationsOpen.value
}

const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId)
    if (notification) {
        notification.read = true
    }
}

const handleClickAway = (event) => {
    const notificationButton = document.querySelector('[data-notification-button]')
    const notificationDropdown = document.querySelector('[data-notification-dropdown]')

    if (!notificationButton?.contains(event.target) &&
        !notificationDropdown?.contains(event.target)) {
        notificationsOpen.value = false
    }
}

const handleEscapeKey = (event) => {
    if (event.key === 'Escape') {
        notificationsOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleEscapeKey)
})
</script>


<template>
    <div class="relative" ref="notificationRef">
        <!-- Notification Bell Button -->
        <button type="button" data-notification-button
            class="relative p-1.5 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white focus:outline-none rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer group"
            @click="toggleNotifications" aria-label="Notifications" :aria-expanded="notificationsOpen">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                Notifications
            </span>
        </button>

        <!-- Notification Dropdown -->
        <div v-show="notificationsOpen" data-notification-dropdown
            class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-300 dark:ring-gray-700 ring-opacity-5">
            <!-- Dropdown Header -->
            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
            </div>

            <!-- Notification List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    No notifications
                </div>

                <ul v-else>
                    <li v-for="notification in notifications" :key="notification.id"
                        @click="markAsRead(notification.id)"
                        class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                        :class="{
                            'bg-blue-50/50 dark:bg-blue-900/20': !notification.read,
                            'border-l-4': true,
                            'border-red-500': notification.priority === 'critical',
                            'border-yellow-500': notification.priority === 'high',
                            'border-blue-500': notification.priority === 'normal',
                            'border-gray-500': notification.priority === 'low'
                        }">
                        <div class="flex gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ notification.title }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ notification.description }}
                                </p>
                                <time class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ notification.time }}
                                </time>
                            </div>
                            <div v-if="!notification.read" class="w-2 h-2 mt-2 bg-blue-500 rounded-full"
                                aria-hidden="true"></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

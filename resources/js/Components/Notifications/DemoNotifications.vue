<script setup>
import { ref } from 'vue'
import { BellIcon } from '@lucide/vue'
import DropdownMenu from '@js/Components/DropdownMenu.vue'

defineProps({
    user: { type: Object, required: true },
})

const notificationsOpen = ref(false)

const notifications = ref([
    {
        id: 1,
        title: 'New update available',
        description: 'A new software update is ready to install',
        time: '5 min ago',
        read: false,
        priority: 'high',
    },
    {
        id: 2,
        title: 'Welcome to the platform',
        description: 'Take a quick tour of the features',
        time: '1 hour ago',
        read: false,
        priority: 'normal',
    },
    {
        id: 3,
        title: 'System maintenance',
        description: 'Scheduled maintenance in 2 hours',
        time: '2 hours ago',
        read: true,
        priority: 'low',
    },
])

const unreadCount = ref(notifications.value.filter(n => !n.read).length)

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
    return 'bg-border'
}
</script>

<template>
    <DropdownMenu v-model:open="notificationsOpen" align="end" width="w-80" class="overflow-hidden">
        <template #trigger>
            <button type="button" class="nav-bar-btn relative" aria-label="Notifications">
                <BellIcon class="nav-bar-icon" aria-hidden="true" />
                <span
                    v-if="unreadCount > 0"
                    class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-semibold text-white">
                    {{ unreadCount }}
                </span>
                <span class="nav-bar-tooltip">Notifications</span>
            </button>
        </template>

        <div>
            <div class="border-border flex items-center justify-between border-b px-4 py-2.5">
                <h3 class="text-foreground text-sm font-semibold">Notifications</h3>
                <span class="text-muted-foreground text-[10px]">Demo</span>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div
                    v-if="notifications.length === 0"
                    class="text-muted-foreground px-4 py-8 text-center text-xs">
                    No notifications
                </div>

                <div v-else class="divide-border divide-y">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        class="hover:bg-muted flex gap-3 px-4 py-3 transition-colors"
                        :class="!n.read ? 'cursor-pointer' : ''"
                        @click="markAsRead(n.id)">
                        <span
                            class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                            :class="priorityDot(n.priority)" />
                        <div class="min-w-0 flex-1">
                            <h4
                                class="text-foreground truncate text-sm"
                                :class="!n.read ? 'font-medium' : ''">
                                {{ n.title }}
                            </h4>
                            <p class="text-muted-foreground mt-0.5 truncate text-xs">
                                {{ n.description }}
                            </p>
                            <time class="text-muted-foreground mt-1 block text-[10px]">
                                {{ n.time }}
                            </time>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DropdownMenu>
</template>

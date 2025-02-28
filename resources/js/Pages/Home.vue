<template>

    <Head title="Dashboard" />

    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="py-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    {{ greeting }}, {{ userName }}
                    <span role="img" aria-label="wave">👋</span>
                </h1>
                <time datetime="" class="text-sm text-gray-600 dark:text-gray-400 mt-2 font-mono">
                    {{ formattedDate.display }}
                </time>
            </header>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" aria-label="Key Statistics">
                <article v-for="card in statCards" :key="card.title"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div :class="`shrink-0 bg-${card.color}-50 dark:bg-${card.color}-900/20 rounded-xl p-3`">
                            <svg aria-hidden="true"
                                class="h-8 w-8 transition-transform duration-200 group-hover:scale-110"
                                :class="`text-${card.color}-600 dark:text-${card.color}-400`"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-html="card.icon">
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ card.title }}</h2>
                            <div class="flex items-baseline gap-2 mt-1">
                                <strong class="text-2xl font-bold text-gray-900 dark:text-white">{{ card.value }}</strong>
                                <span :class="`text-xs font-medium ${card.growth.startsWith('+') ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}`"
                                    role="status">
                                    {{ card.growth }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div :class="`h-full ${`bg-${card.color}-500`} rounded-full`"
                            :style="`width: ${Math.random() * 100}%`"
                            role="progressbar"
                            :aria-valuenow="Math.random() * 100"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </article>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Tasks -->
                <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Tasks</h2>
                        <button class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">View All</button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-for="task in upcomingTasks" :key="task.id"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-gray-900 dark:text-white font-medium">{{ task.title }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Due {{ new Date(task.dueDate).toLocaleDateString() }}</p>
                                </div>
                                <span :class="{
                                    'bg-rose-100 text-rose-700': task.priority === 'high',
                                    'bg-amber-100 text-amber-700': task.priority === 'medium'
                                }" class="text-xs px-2 py-1 rounded-full">
                                    {{ task.priority }}
                                </span>
                            </div>
                            <div class="flex items-center mt-4">
                                <div class="flex -space-x-2">
                                    <img v-for="assignee in task.assignees" :key="assignee.name"
                                        :src="assignee.avatar"
                                        :alt="assignee.name"
                                        class="w-8 h-8 rounded-full border-2 border-white">
                                </div>
                                <button class="ml-2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Notifications Panel -->
                <section class="bg-white dark:bg-gray-800 rounded-xl shadow-md" aria-label="Notifications">
                    <header class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Notifications</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 ring-2 ring-blue-500/20">
                            {{ notifications.filter(n => !n.read).length }} new
                        </span>
                    </header>
                    <ul class="p-6 space-y-4">
                        <li v-for="notification in notifications" :key="notification.id"
                            :class="[
                                'p-4 rounded-xl transition-all duration-200',
                                notification.read ? 'bg-gray-50' : 'bg-blue-50 ring-1 ring-blue-100'
                            ]">
                            <div class="flex items-start gap-3">
                                <div :class="[
                                    'p-2 rounded-lg',
                                    notification.type === 'mention' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300'
                                ]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path v-if="notification.type === 'mention'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-gray-800">{{ notification.message }}</p>
                                    <span class="text-xs text-gray-500 mt-1 block">{{ notification.time }}</span>
                                </div>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>
                </section>
            </div>

            <!-- Add after notifications panel -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions with search -->
                <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search transactions..."
                                    class="pl-8 pr-4 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-700
                                    bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                    focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none
                                    placeholder-gray-500 dark:placeholder-gray-400"
                                >
                                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button
                                @click="refreshData"
                                :disabled="isLoading"
                                class="text-sm font-medium text-blue-600 hover:text-blue-700 disabled:opacity-50 flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4"
                                    :class="{ 'animate-spin': isLoading }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-if="filteredTransactions.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No transactions found</p>
                        </div>
                        <div v-else v-for="transaction in filteredTransactions" :key="transaction.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center gap-4">
                                <img :src="transaction.customer.avatar" :alt="transaction.customer.name"
                                    class="w-10 h-10 rounded-full">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ transaction.description }}</h3>
                                    <p class="text-xs text-gray-500">{{ transaction.customer.name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p :class="`text-sm font-medium ${transaction.type === 'credit' ? 'text-emerald-600' : 'text-gray-900'}`">
                                    {{ transaction.amount }}
                                </p>
                                <span class="text-xs text-gray-500">{{ formatDate(transaction.date) }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- System Health with refresh button -->
                <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time performance metrics</p>
                        </div>
                        <span class="text-xs text-gray-500">Press ⌘R to refresh</span>
                    </div>
                    <div class="p-6 space-y-6">
                        <div v-for="(value, metric) in systemHealth" :key="metric" class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 capitalize">{{ metric }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ value }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    :class="{
                                        'bg-emerald-500': value < 70,
                                        'bg-amber-500': value >= 70 && value < 90,
                                        'bg-rose-500': value >= 90
                                    }"
                                    :style="`width: ${value}%`">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Quick Stats Bar -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="(value, metric) in quickStats" :key="metric"
                        class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ metric.replace(/([A-Z])/g, ' $1').trim() }}</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ value }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Default from '../Layouts/Default.vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

defineOptions({
    layout: Default
})

const page = usePage()
const userName = computed(() => page.props.auth.user?.name || 'User')

const formattedDate = computed(() => {
    const date = new Date()
    const formatted = date.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric'
    })
    const isoDate = date.toISOString().split('T')[0]
    return { display: formatted, iso: isoDate }
})

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Good morning'
    if (hour < 17) return 'Good afternoon'
    return 'Good evening'
})

const statCards = [
    {
        title: 'Total Members',
        value: '12,768',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>`,
        color: 'emerald',
        growth: '+12.6%'
    },
    {
        title: 'Total Posts',
        value: '39,265',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>`,
        color: 'blue',
        growth: '+8.2%'
    },
    {
        title: 'Total Comments',
        value: '142,334',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>`,
        color: 'indigo',
        growth: '+5.4%'
    },
    {
        title: 'Server Load',
        value: '34.12%',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>`,
        color: 'rose',
        growth: '-2.4%'
    }
]

const recentActivity = ref([
    {
        id: 1,
        title: 'Project "Sunset" completed',
        time: '2 hours ago',
        icon: 'CheckCircleIcon',
        gradientClass: 'bg-gradient-to-br from-green-400 to-emerald-600',
        iconColor: 'text-white'
    },
    {
        id: 2,
        title: 'New client onboarded',
        time: '4 hours ago',
        icon: 'UserPlusIcon',
        gradientClass: 'bg-gradient-to-br from-blue-400 to-indigo-600',
        iconColor: 'text-white'
    }
])

// Helper function to generate Dicebear URL
const getAvatarUrl = (seed, style = 'avataaars') => {
    return `https://api.dicebear.com/7.x/${style}/svg?seed=${encodeURIComponent(seed)}&backgroundColor=b6e3f4,c0aede,d1d4f9`
}

// Update teamMembers with Dicebear avatars
const teamMembers = ref([
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Frontend Developer',
        avatar: getAvatarUrl('Sarah Johnson'),
        online: true
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'Backend Developer',
        avatar: getAvatarUrl('Michael Chen'),
        online: false
    }
])

const quickActions = [
    {
        label: 'New Project',
        icon: 'PlusIcon',
        gradientClass: 'bg-gradient-to-br from-blue-500 to-indigo-600'
    },
    {
        label: 'Upload Files',
        icon: 'UploadIcon',
        gradientClass: 'bg-gradient-to-br from-emerald-500 to-teal-600'
    },
    {
        label: 'Add Member',
        icon: 'UserPlusIcon',
        gradientClass: 'bg-gradient-to-br from-purple-500 to-pink-600'
    },
    {
        label: 'Generate Report',
        icon: 'DocumentIcon',
        gradientClass: 'bg-gradient-to-br from-amber-500 to-orange-600'
    }
]

// Add new data for charts
const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    values: [30, 40, 35, 50, 49, 60]
}

// Update upcomingTasks with Dicebear avatars
const upcomingTasks = ref([
    {
        id: 1,
        title: 'Dashboard Design Review',
        dueDate: '2024-03-20',
        priority: 'high',
        assignees: [
            { name: 'Sarah J', avatar: getAvatarUrl('Sarah J', 'bottts') },
            { name: 'Mike R', avatar: getAvatarUrl('Mike R', 'bottts') }
        ]
    },
    {
        id: 2,
        title: 'API Integration',
        dueDate: '2024-03-22',
        priority: 'medium',
        assignees: [
            { name: 'John D', avatar: getAvatarUrl('John D', 'bottts') }
        ]
    }
])

// Add notifications
const notifications = ref([
    {
        id: 1,
        type: 'mention',
        message: 'Alex mentioned you in a comment',
        time: '5 min ago',
        read: false
    },
    {
        id: 2,
        type: 'update',
        message: 'New version deployed successfully',
        time: '2 hours ago',
        read: true
    }
])

// Update recentTransactions with Dicebear avatars
const recentTransactions = ref([
    {
        id: 1,
        description: 'Payment Received',
        amount: '+$2,500.00',
        status: 'completed',
        date: '2024-03-15',
        type: 'credit',
        customer: {
            name: 'John Smith',
            email: 'john@example.com',
            avatar: getAvatarUrl('John Smith', 'initials')
        }
    },
    {
        id: 2,
        description: 'Server Hosting',
        amount: '-$899.00',
        status: 'pending',
        date: '2024-03-14',
        type: 'debit',
        customer: {
            name: 'AWS Cloud',
            email: 'billing@aws.com',
            avatar: getAvatarUrl('AWS Cloud', 'shapes')
        }
    }
])

// Add quick stats
const quickStats = ref({
    activeUsers: '1.2k',
    uptime: '99.9%',
    responseTime: '0.2s',
    pendingIssues: '23'
})

// Add system health
const systemHealth = ref({
    cpu: 45,
    memory: 72,
    storage: 63,
    network: 89
})

// Add loading states
const isLoading = ref(false)

// Add refresh functionality
const refreshData = async () => {
    isLoading.value = true
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        // Update random values for demo
        systemHealth.value = {
            cpu: Math.floor(Math.random() * 100),
            memory: Math.floor(Math.random() * 100),
            storage: Math.floor(Math.random() * 100),
            network: Math.floor(Math.random() * 100)
        }
    } finally {
        isLoading.value = false
    }
}

// Add search functionality
const searchQuery = ref('')
const filteredTransactions = computed(() => {
    if (!searchQuery.value) return recentTransactions.value
    const query = searchQuery.value.toLowerCase()
    return recentTransactions.value.filter(transaction =>
        transaction.description.toLowerCase().includes(query) ||
        transaction.customer.name.toLowerCase().includes(query)
    )
})

// Add date formatting helper
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

// Basic keyboard shortcut for refresh
onMounted(() => {
    const handleKeyPress = (event) => {
        if (event.key === 'r' && (event.ctrlKey || event.metaKey)) {
            event.preventDefault()
            refreshData()
        }
    }
    window.addEventListener('keydown', handleKeyPress)
    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeyPress)
    })
})
</script>

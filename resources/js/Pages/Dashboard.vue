<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Default from '../Layouts/Default.vue'
import StockWidget from '@/Components/Widgets/StockWidget.vue'
import AchievementWidget from '@/Components/Widgets/AchievementWidget.vue'
import MetricWidget from '@/Components/Widgets/MetricWidget.vue'
import StatWidget from '@/Components/Widgets/StatWidget.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    stats: {
        type: Array,
        required: true,
        default: () => []
    }
})

const page = usePage()
const userName = computed(() => page.props.auth.user?.name || 'User')

const formattedDate = computed(() => {
    const date = new Date()
    return {
        display: date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }),
        mobileDisplay: date.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit'
        }),
        dayOfWeek: date.toLocaleDateString('en-US', { weekday: 'short' })
    }
})

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Good morning'
    if (hour < 17) return 'Good afternoon'
    return 'Good evening'
})


const icons = {
    // Metric icons
    revenue: '<path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    conversion: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>',
    response: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    // Achievement icons
    chatMinutes: '<path d="M39.37 18.432c0 3.058-.906 5.862-2.466 8.203a14.728 14.728 0 0 1-10.079 6.367c-.717.127-1.455.19-2.214.19-.759 0-1.497-.063-2.214-.19a14.728 14.728 0 0 1-10.078-6.368 14.692 14.692 0 0 1-2.467-8.202c0-8.16 6.6-14.76 14.76-14.76s14.759 6.6 14.759 14.76Z"/><path d="m44.712 38.17-3.431.83a2.063 2.063 0 0 0-1.539 1.572l-.728 3.122c-.09.384-.281.734-.554 1.012a2.068 2.068 0 0 1-.992.564c-.375.09-.768.073-1.134-.052a2.078 2.078 0 0 1-.938-.653l-9.92-11.64-9.92 11.661a2.078 2.078 0 0 1-.938.653 2.038 2.038 0 0 1-1.134.052 2.067 2.067 0 0 1-.992-.563 2.137 2.137 0 0 1-.554-1.012l-.728-3.123a2.13 2.13 0 0 0-.55-1.01 2.06 2.06 0 0 0-.988-.562L6.24 38.19a2.073 2.073 0 0 1-.956-.533 2.14 2.14 0 0 1-.563-.953 2.175 2.175 0 0 1-.015-1.113c.091-.366.276-.7.536-.97l8.11-8.284a14.672 14.672 0 0 0 4.307 4.281 14.34 14.34 0 0 0 5.634 2.134 12.29 12.29 0 0 0 2.183.191c.749 0 1.477-.063 2.184-.19 4.138-.617 7.694-3.017 9.94-6.416l8.11 8.285c1.144 1.147.583 3.165-.998 3.547Z"/>',
    expertRating: '<path d="m26.91 5.776 4.483 10.683a1.544 1.544 0 0 0 1.287.942l11.474.992a1.544 1.544 0 0 1 .876 2.715L36.325 28.7a1.559 1.559 0 0 0-.49 1.523l2.61 11.296a1.544 1.544 0 0 1-2.295 1.677l-9.86-5.982a1.53 1.53 0 0 0-1.59 0l-9.861 5.982a1.544 1.544 0 0 1-2.295-1.677l2.609-11.296a1.56 1.56 0 0 0-.49-1.523l-8.705-7.593a1.544 1.544 0 0 1 .876-2.715l11.474-.992a1.544 1.544 0 0 0 1.287-.942l4.483-10.683a1.544 1.544 0 0 1 2.833 0Z"/>',
    sessionsCompleted: '<path d="M10.811 39.091c-1.775-1.775-.598-5.505-1.5-7.69-.939-2.255-4.377-4.089-4.377-6.5 0-2.413 3.438-4.246 4.376-6.502.903-2.182-.274-5.914 1.501-7.69 1.776-1.775 5.508-.598 7.69-1.5 2.266-.939 4.09-4.377 6.501-4.377 2.412 0 4.246 3.438 6.501 4.376 2.185.903 5.915-.274 7.69 1.501 1.776 1.776.598 5.506 1.502 7.69.937 2.266 4.376 4.09 4.376 6.501 0 2.412-3.439 4.246-4.377 6.501-.903 2.185.274 5.915-1.5 7.69-1.776 1.776-5.506.598-7.69 1.501-2.256.938-4.09 4.377-6.502 4.377s-4.245-3.439-6.5-4.377c-2.183-.903-5.915.275-7.69-1.5Z"/><path d="m17.281 26.444 4.632 4.631L32.718 20.27"/>',
    appDownloads: '<path d="M45.571 12.006 27.046 30.531l-7.719-7.718L5.434 36.706"/><path d="M45.569 24.356v-12.35h-12.35"/>',
    // Stat icons
    tasks: '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
    messages: '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
    projects: '<path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>',
    performance: '<path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/>'
}

// Stock data
const stocks = ref([
    {
        symbol: 'AAPL',
        name: 'Apple, Inc',
        price: '173.25',
        change: 0.86,
        icon: 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
        bgColor: 'gray'
    },
    {
        symbol: 'PYPL',
        name: 'Paypal, Inc',
        price: '65.23',
        change: -1.42,
        icon: 'https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg',
        bgColor: 'blue'
    },
    {
        symbol: 'TSLA',
        name: 'Tesla, Inc',
        price: '241.53',
        change: 2.76,
        icon: 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Tesla_logo.png',
        bgColor: 'red'
    },
    {
        symbol: 'HPQ',
        name: 'HP Inc',
        price: '29.78',
        change: 0.95,
        icon: 'https://logodownload.org/wp-content/uploads/2014/04/hp-logo-1.png',
        bgColor: 'blue'
    }
])
</script>

<template>

    <Head title="Dashboard" />

    <main class="min-h-screen">
        <div class="max-w-6xl mx-auto">
            <header class="mb-6 sm:mb-8 lg:mb-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-3 sm:mb-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2 sm:gap-3">
                        <span class="hidden sm:inline">{{ greeting }},</span>
                        <span class="sm:hidden">{{ greeting }}</span>
                        {{ userName }}
                        <span class="text-blue-500 dark:text-blue-400 animate-pulse">•</span>
                    </h1>
                    <time
                        class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 sm:px-4 py-2 sm:py-2.5 rounded-full shadow-sm self-start sm:self-auto">
                        <div class="hidden sm:block">{{ formattedDate.display }}</div>
                        <div class="sm:hidden flex items-center gap-1">
                            <span class="font-semibold">{{ formattedDate.dayOfWeek }}</span>
                            <span>{{ formattedDate.mobileDisplay }}</span>
                        </div>
                    </time>
                </div>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Here's what's happening with your portfolio today.</p>
            </header>

            <!-- Metrics Widgets -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Metric Widgets</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <MetricWidget title="Total Revenue" :value="84621" trend="up" :change="12.5" :svg="icons.revenue"
                        color="emerald" />
                    <MetricWidget title="Active Users" :value="2847" trend="up" :change="8.2" :svg="icons.users"
                        color="blue" />
                    <MetricWidget title="Conversion Rate" value="3.24%" trend="down" :change="-1.8"
                        :svg="icons.conversion" color="amber" />
                    <MetricWidget title="Avg. Response Time" value="284ms" trend="up" :change="15.3"
                        :svg="icons.response" color="rose" />
                </div>
            </section>

            <!-- Stock Widgets -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Stock Widgets</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StockWidget :stock="stocks[0]" :src="stocks[0].icon" :alt="stocks[0].name"
                        :bg-color="stocks[0].bgColor" size="lg" />
                    <StockWidget :stock="stocks[1]" :src="stocks[1].icon" :alt="stocks[1].name"
                        :bg-color="stocks[1].bgColor" size="lg" />
                    <StockWidget :stock="stocks[2]" :src="stocks[2].icon" :alt="stocks[2].name"
                        :bg-color="stocks[2].bgColor" size="lg" />
                    <StockWidget :stock="stocks[3]" :src="stocks[3].icon" :alt="stocks[3].name"
                        :bg-color="stocks[3].bgColor" size="lg" />
                </div>
            </section>

            <!-- Achievements Widget-->
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Achievement Widgets</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 w-full mx-auto max-w-8xl">
                    <AchievementWidget title="Expert Chat & Call Minutes" value="75K+" viewBox="0 0 50 50"
                        color="text-blue-500 dark:text-blue-400" :svg="icons.chatMinutes" />
                    <AchievementWidget title="Average Expert Rating" value="4.9" viewBox="0 0 51 50"
                        color="text-blue-500 dark:text-blue-400" :svg="icons.expertRating" />
                    <AchievementWidget title="Sessions Completed" value="8900+" viewBox="0 0 50 50"
                        color="text-blue-500 dark:text-blue-400" :svg="icons.sessionsCompleted" />
                    <AchievementWidget title="App Downloads" value="1.5M+" viewBox="0 0 51 50"
                        color="text-blue-500 dark:text-blue-400" :svg="icons.appDownloads" />
                </div>
            </section>

            <!-- Stats Widgets -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Stats Overview</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatWidget v-for="(stat, index) in stats" :key="index" :title="stat.title" :value="stat.value"
                        :trend="stat.growth.startsWith('+') ? 'up' : (stat.growth.startsWith('-') ? 'down' : 'neutral')"
                        :color="stat.growth.startsWith('+') ? 'green' : (stat.growth.startsWith('-') ? 'red' : 'blue')"
                        :icon="stat.title.includes('Member') ? icons.users :
                            stat.title.includes('Growth') ? icons.performance :
                                stat.title.includes('Session') ? icons.projects :
                                    icons.tasks" />
                </div>
            </section>
        </div>
    </main>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Default from '../Layouts/Default.vue'
import LineChart from '@/Components/LineChart.vue'

defineOptions({
    layout: Default
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
        })
    }
})

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Good morning'
    if (hour < 17) return 'Good afternoon'
    return 'Good evening'
})

// Stock data
const stocks = [
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
]

const statCards = [
    {
        title: 'Total Members',
        value: '12,768',
        growth: '+12.6%'
    },
    {
        title: 'Total Posts',
        value: '39,265',
        growth: '+8.2%'
    },
    {
        title: 'Comments',
        value: '142,334',
        growth: '+5.4%'
    },
    {
        title: 'Server Load',
        value: '34.12%',
        growth: '-2.4%'
    }
]

const upcomingTasks = ref([
    {
        id: 1,
        title: 'Dashboard Design Review',
        dueDate: '2024-03-20',
        priority: 'high'
    },
    {
        id: 2,
        title: 'API Integration',
        dueDate: '2024-03-22',
        priority: 'medium'
    }
])

const systemHealth = ref({
    cpu: 45,
    memory: 72,
    storage: 63,
    network: 89
})

// Chart data
const chartData = ref({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
        {
            label: 'Revenue',
            data: [65000, 59000, 80000, 81000, 76000, 85000]
        },
        {
            label: 'Expenses',
            data: [45000, 47000, 55000, 58000, 56000, 62000]
        }
    ]
})

// Format currency for y-axis
const formatCurrency = (value) => {
    return '$' + value.toLocaleString()
}

onMounted(() => {
    const handleKeyPress = (event) => {
        if (event.key === 'r' && (event.ctrlKey || event.metaKey)) {
            event.preventDefault()
            systemHealth.value = {
                cpu: Math.floor(Math.random() * 100),
                memory: Math.floor(Math.random() * 100),
                storage: Math.floor(Math.random() * 100),
                network: Math.floor(Math.random() * 100)
            }
        }
    }
    window.addEventListener('keydown', handleKeyPress)
    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeyPress)
    })
})
</script>


<template>

    <Head title="Dashboard" />

    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 py-10">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    {{ greeting }}, {{ userName }}
                    <span class="text-blue-500 dark:text-blue-400 animate-pulse">•</span>
                </h1>
                <time class="text-sm text-gray-600 dark:text-gray-400">
                    {{ formattedDate.display }}
                </time>
            </header>

            <!-- Stock Ticker -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <article v-for="stock in stocks" :key="stock.symbol"
                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-3">
                        <div :class="`bg-${stock.bgColor}-100 dark:bg-${stock.bgColor}-900/30 p-2 rounded-full`">
                            <img :src="stock.icon" :alt="stock.name" class="w-8 h-8">
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ stock.symbol }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ stock.name }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <strong class="text-xl font-bold text-gray-900 dark:text-white">${{ stock.price }}</strong>
                        <span :class="{
                            'text-emerald-600 dark:text-emerald-400': stock.change > 0,
                            'text-rose-600 dark:text-rose-400': stock.change < 0
                        }" class="flex items-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                :class="{ 'rotate-180': stock.change < 0 }">
                                <path d="M12 5L20 13L18.6 14.4L13 8.8V19H11V8.8L5.4 14.4L4 13L12 5Z"
                                    fill="currentColor" />
                            </svg>
                            {{ Math.abs(stock.change).toFixed(2) }}%
                        </span>
                    </div>
                </article>
            </section>

            <!-- Revenue Chart -->
            <section
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <LineChart :chart-data="chartData" title="Revenue vs Expenses" height="400px"
                    :y-axis-format="formatCurrency" :colors="['#3b82f6', '#ef4444']" :animation-duration="1500" />
            </section>

            <!-- Key Stats -->
            <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <article v-for="card in statCards" :key="card.title"
                    class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ card.title }}</h2>
                    <div class="mt-2 flex items-baseline">
                        <strong class="text-xl font-bold text-gray-900 dark:text-white">{{ card.value }}</strong>
                        <span class="ml-2 text-xs font-medium"
                            :class="card.growth.startsWith('+') ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                            {{ card.growth }}
                        </span>
                    </div>
                </article>
            </section>

            <!-- Main Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Upcoming Tasks -->
                <section
                    class="md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6 md:mb-0">
                    <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="font-medium text-gray-900 dark:text-white flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                            Upcoming Tasks
                        </h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div v-for="task in upcomingTasks" :key="task.id"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex justify-between">
                                <h3 class="text-gray-900 dark:text-white font-medium">{{ task.title }}</h3>
                                <span :class="{
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400': task.priority === 'high',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': task.priority === 'medium'
                                }" class="text-xs px-2 py-0.5 rounded-full">
                                    {{ task.priority }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Due {{ new Date(task.dueDate).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- System Health -->
                <section
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="font-medium text-gray-900 dark:text-white flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                            System Health
                        </h2>
                    </div>
                    <div class="p-4 space-y-4">
                        <div v-for="(value, metric) in systemHealth" :key="metric" class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ metric }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ value }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700" :class="{
                                    'bg-emerald-500': value < 70,
                                    'bg-amber-500': value >= 70 && value < 90,
                                    'bg-rose-500': value >= 90
                                }" :style="`width: ${value}%`"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</template>

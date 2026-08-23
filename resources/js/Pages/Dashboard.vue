<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import ChartWidget from '@js/Components/Widgets/ChartWidget.vue'
import ProgressWidget from '@js/Components/Widgets/ProgressWidget.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    stats: Object,
    userGrowth: Array,
})

const page = usePage()
const userName = computed(() => page.props.auth.user?.name?.split(' ')[0] || 'there')

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 17) return 'Good afternoon'
    return 'Good evening'
})

const today = computed(() =>
    new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
)

const sparklinePath = (data, key) => {
    if (!data?.length) return ''
    const vals = data.map(d => d[key])
    const max = Math.max(...vals, 1)
    return 'M ' + vals.map((v, i) => `${(i / (vals.length - 1)) * 100},${36 - (v / max) * 36}`).join(' L ')
}
const sparklineArea = (data, key) => {
    const p = sparklinePath(data, key)
    return p ? `${p} L 100,36 L 0,36 Z` : ''
}
</script>

<template>
    <Head title="Dashboard" />

    <main class="mx-auto max-w-7xl">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
            <h1 class="text-xl font-semibold text-foreground">{{ greeting }}, {{ userName }}</h1>
            <time class="text-xs text-muted-foreground">{{ today }}</time>
        </div>

        <!-- Primary metrics -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="card px-4 py-4 sm:px-5">
                <p class="text-xs font-medium text-muted-foreground">Total users</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-foreground">{{ stats?.totalUsers?.toLocaleString() || '0' }}</p>
                <p v-if="stats?.userGrowth != null" class="mt-1 text-xs tabular-nums"
                   :class="stats.userGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                    {{ stats.userGrowth >= 0 ? '+' : '' }}{{ stats.userGrowth }}% vs last week
                </p>
            </div>
            <div class="card px-4 py-4 sm:px-5">
                <p class="text-xs font-medium text-muted-foreground">Active now</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-foreground">{{ stats?.activeSessions?.toLocaleString() || '0' }}</p>
                <p class="mt-1 text-xs text-muted-foreground">sessions</p>
            </div>
            <div class="card px-4 py-4 sm:px-5">
                <p class="text-xs font-medium text-muted-foreground">Logins today</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-foreground">{{ stats?.loginsToday?.toLocaleString() || '0' }}</p>
                <p class="mt-1 text-xs text-muted-foreground">successful</p>
            </div>
            <div class="card px-4 py-4 sm:px-5">
                <p class="text-xs font-medium text-muted-foreground">New this week</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-foreground">{{ stats?.newUsersThisWeek?.toLocaleString() || '0' }}</p>
                <p class="mt-1 text-xs text-muted-foreground">sign-ups</p>
            </div>
        </div>

        <!-- Trend charts -->
        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
            <ChartWidget title="Revenue" value="$45,231" :change="12.5" :data="[30, 40, 35, 50, 49, 60, 70, 91, 125]" color="emerald" />
            <ChartWidget title="Visitors" value="8,234" :change="8.1" :data="[20, 30, 35, 45, 40, 55, 60, 70, 65]" color="blue" />
            <ChartWidget title="Orders" value="1,234" :change="-3.2" :data="[50, 45, 40, 42, 38, 35, 33, 30, 28]" color="red" />
            <ChartWidget title="Conversion" value="3.24%" :change="5.4" :data="[15, 20, 18, 25, 30, 28, 35, 40, 42]" color="purple" />
        </div>

        <!-- User growth + Progress -->
        <div class="mt-4 grid gap-3 lg:grid-cols-2">
            <!-- User growth -->
            <div class="card px-5 py-4">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-sm font-medium text-foreground">User growth</h2>
                    <span class="text-[11px] text-muted-foreground">6 months</span>
                </div>
                <div class="mt-4 h-20">
                    <svg v-if="userGrowth?.length" viewBox="0 0 100 36" class="h-full w-full" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="ug" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.08" />
                                <stop offset="100%" stop-color="var(--primary)" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path :d="sparklineArea(userGrowth, 'count')" fill="url(#ug)" />
                        <path :d="sparklinePath(userGrowth, 'count')" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p v-else class="flex h-full items-center justify-center text-xs text-muted-foreground">
                        Populates as users register
                    </p>
                </div>
                <div v-if="userGrowth?.length" class="mt-2 flex justify-between text-[10px] tabular-nums text-muted-foreground">
                    <span v-for="m in userGrowth" :key="m.month">{{ m.month }}</span>
                </div>
            </div>

            <!-- Progress -->
            <div class="grid grid-cols-2 gap-3">
                <ProgressWidget title="Storage" :value="75" :max="100" description="75 of 100 GB" color="blue" />
                <ProgressWidget title="Tasks" :value="42" :max="60" description="42 of 60 done" color="green" />
                <ProgressWidget title="Sprint" :value="88" :max="100" description="88% complete" color="purple" />
                <ProgressWidget title="Goals" :value="35" :max="50" description="70% achieved" color="indigo" />
            </div>
        </div>
    </main>
</template>

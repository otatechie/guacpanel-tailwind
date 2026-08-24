<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import AreaChart from '@js/Components/Charts/AreaChart.vue'
import BarChart from '@js/Components/Charts/BarChart.vue'
import DonutChart from '@js/Components/Charts/DonutChart.vue'
import LineChart from '@js/Components/Charts/LineChart.vue'
import Sparkline from '@js/Components/Charts/Sparkline.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    stats: Object,
    userGrowth: Array,
    financialMetrics: Object,
    trends: Object,
    usersByRole: Array,
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

const currentYear = new Date().getFullYear()

// Every tile states what it counts and over what period, so no number on the
// page needs a legend or a guess to interpret.
const metrics = computed(() => [
    {
        label: 'Total users',
        value: props.stats?.totalUsers,
        caption: 'all time',
        delta: props.stats?.userGrowth,
        trend: props.trends?.totalUsers,
    },
    // No sparkline: sessions are current state, so there is no history to plot.
    { label: 'Active now', value: props.stats?.activeSessions, caption: 'sessions' },
    {
        label: 'Logins today',
        value: props.stats?.loginsToday,
        caption: 'successful',
        trend: props.trends?.loginsToday,
    },
    {
        label: 'New this week',
        value: props.stats?.newUsersThisWeek,
        caption: 'sign-ups',
        trend: props.trends?.newUsersThisWeek,
    },
])

const roleChartData = computed(() => ({
    labels: (props.usersByRole || []).map(role => role.label),
    datasets: [{ label: 'Users', data: (props.usersByRole || []).map(role => role.value) }],
}))

const hasRoles = computed(() => (props.usersByRole || []).length > 0)

// A ranked bar chart grows with its list; a fixed height would give two roles
// bars thick enough to read as blocks and twenty roles slivers.
const roleChartHeight = computed(() => `${(props.usersByRole || []).length * 40 + 40}px`)

const months = computed(() => props.financialMetrics?.months || [])
const incomeByMonth = month => Number(props.financialMetrics?.income?.[month] || 0)
const expenseByMonth = month => Number(props.financialMetrics?.expense?.[month] || 0)

const income = computed(() => months.value.map(incomeByMonth))
const expenses = computed(() => months.value.map(expenseByMonth))

const hasFinancialData = computed(() =>
    [...income.value, ...expenses.value].some(amount => amount > 0)
)

const incomeVsExpenses = computed(() => ({
    labels: months.value,
    datasets: [
        { label: 'Income', data: income.value },
        { label: 'Expenses', data: expenses.value },
    ],
}))

const distribution = computed(() => ({
    labels: ['Income', 'Expenses'],
    datasets: [
        {
            label: 'Revenue distribution',
            data: [
                income.value.reduce((sum, amount) => sum + amount, 0),
                expenses.value.reduce((sum, amount) => sum + amount, 0),
            ],
        },
    ],
}))

const incomeTrend = computed(() => ({
    labels: months.value,
    datasets: [{ label: 'Income', data: income.value }],
}))

const userGrowthData = computed(() => ({
    labels: (props.userGrowth || []).map(month => month.month),
    datasets: [{ label: 'New users', data: (props.userGrowth || []).map(month => month.count) }],
}))

const hasUserGrowth = computed(() => (props.userGrowth || []).some(month => month.count > 0))
</script>

<template>
    <Head title="Dashboard" />

    <main class="mx-auto max-w-4xl" aria-labelledby="dashboard-heading">
        <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
            <h1 id="dashboard-heading" class="text-foreground text-xl font-semibold">
                {{ greeting }}, {{ userName }}
            </h1>
            <time class="text-muted-foreground text-xs">{{ today }}</time>
        </div>

        <section aria-labelledby="metrics-heading">
            <h2 id="metrics-heading" class="sr-only">Key metrics</h2>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div v-for="metric in metrics" :key="metric.label" class="card px-4 py-4 sm:px-5">
                    <p class="text-muted-foreground text-xs font-medium">{{ metric.label }}</p>
                    <p
                        class="text-foreground mt-2 text-3xl font-semibold tracking-tight tabular-nums">
                        {{ (metric.value ?? 0).toLocaleString() }}
                    </p>
                    <p
                        v-if="metric.delta != null"
                        class="mt-1 text-xs tabular-nums"
                        :class="
                            metric.delta >= 0
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-red-600 dark:text-red-400'
                        ">
                        {{ metric.delta >= 0 ? '+' : '' }}{{ metric.delta }}% vs last week
                    </p>
                    <p v-else class="text-muted-foreground mt-1 text-xs">{{ metric.caption }}</p>
                    <Sparkline v-if="metric.trend?.length" :data="metric.trend" class="mt-3" />
                </div>
            </div>
        </section>

        <section class="mt-8" aria-labelledby="financial-heading">
            <div class="mb-3 flex items-baseline justify-between">
                <h2 id="financial-heading" class="text-foreground text-sm font-medium">
                    Financial overview
                </h2>
                <span class="text-muted-foreground text-xs">{{ currentYear }}</span>
            </div>

            <p
                v-if="!hasFinancialData"
                class="card text-muted-foreground px-5 py-8 text-center text-sm">
                No income or expenses recorded for {{ currentYear }} yet.
            </p>

            <div v-else class="grid gap-3 lg:grid-cols-2">
                <div class="card px-5 py-4">
                    <h3 class="text-foreground text-sm font-medium">Income vs expenses</h3>
                    <p class="text-muted-foreground mt-0.5 text-xs">Monthly totals</p>
                    <LineChart :chart-data="incomeVsExpenses" height="240px" class="mt-4" />
                </div>

                <div class="card px-5 py-4">
                    <h3 class="text-foreground text-sm font-medium">Income vs expenses share</h3>
                    <p class="text-muted-foreground mt-0.5 text-xs">Year to date</p>
                    <DonutChart :chart-data="distribution" height="240px" class="mt-4" />
                </div>

                <div class="card px-5 py-4">
                    <h3 class="text-foreground text-sm font-medium">Monthly comparison</h3>
                    <p class="text-muted-foreground mt-0.5 text-xs">Income beside expenses</p>
                    <BarChart :chart-data="incomeVsExpenses" height="240px" class="mt-4" />
                </div>

                <div class="card px-5 py-4">
                    <h3 class="text-foreground text-sm font-medium">Income trend</h3>
                    <p class="text-muted-foreground mt-0.5 text-xs">Monthly income</p>
                    <AreaChart :chart-data="incomeTrend" height="240px" class="mt-4" />
                </div>
            </div>
        </section>

        <section v-if="hasRoles" class="mt-8" aria-labelledby="roles-heading">
            <div class="mb-3 flex items-baseline justify-between">
                <h2 id="roles-heading" class="text-foreground text-sm font-medium">
                    Users by role
                </h2>
                <span class="text-muted-foreground text-xs">All time</span>
            </div>
            <div class="card px-5 py-4">
                <BarChart
                    :chart-data="roleChartData"
                    orientation="horizontal"
                    :height="roleChartHeight" />
            </div>
        </section>

        <section class="mt-8" aria-labelledby="growth-heading">
            <div class="mb-3 flex items-baseline justify-between">
                <h2 id="growth-heading" class="text-foreground text-sm font-medium">User growth</h2>
                <span class="text-muted-foreground text-xs">Last 6 months</span>
            </div>
            <div class="card px-5 py-4">
                <AreaChart v-if="hasUserGrowth" :chart-data="userGrowthData" height="200px" />
                <p v-else class="text-muted-foreground py-8 text-center text-sm">
                    Populates as users register.
                </p>
            </div>
        </section>
    </main>
</template>

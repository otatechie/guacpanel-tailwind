<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import LineChart from '@js/Components/Charts/LineChart.vue'
import DonutChart from '@js/Components/Charts/DonutChart.vue'
import BarChart from '@js/Components/Charts/BarChart.vue'
import AreaChart from '@js/Components/Charts/AreaChart.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    financialMetrics: {
        type: Object,
        required: true,
        default: () => ({
            income: {},
            expense: {},
        }),
    },
})

const currentYear = new Date().getFullYear()

const months = computed(() => props.financialMetrics?.months || [])
const incomeByMonth = month => Number(props.financialMetrics?.income?.[month] || 0)
const expenseByMonth = month => Number(props.financialMetrics?.expense?.[month] || 0)

const lineChartData = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: 'Income',
            data: months.value.map(incomeByMonth),
        },
        {
            label: 'Expenses',
            data: months.value.map(expenseByMonth),
        },
    ],
}))

const doughnutData = computed(() => ({
    labels: ['Income', 'Expenses'],
    datasets: [
        {
            label: 'Revenue Distribution',
            data: [
                months.value.reduce((sum, m) => sum + incomeByMonth(m), 0),
                months.value.reduce((sum, m) => sum + expenseByMonth(m), 0),
            ],
        },
    ],
}))

const barChartData = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: 'Income',
            data: months.value.map(incomeByMonth),
        },
        {
            label: 'Expenses',
            data: months.value.map(expenseByMonth),
        },
    ],
}))

const hasFinancialData = computed(() =>
    months.value.some(month => incomeByMonth(month) > 0 || expenseByMonth(month) > 0)
)

const areaChartData = computed(() => ({
    labels: months.value,
    datasets: [{ label: 'Income', data: months.value.map(incomeByMonth) }],
}))
</script>

<template>
    <Head title="Charts" />

    <main class="mx-auto max-w-4xl" aria-labelledby="charts-heading">
        <div class="mb-6">
            <h1 id="charts-heading" class="text-foreground text-xl font-semibold">Charts</h1>
            <p class="text-muted-foreground mt-1 text-sm">Financial metrics overview</p>
        </div>

        <p
            v-if="!hasFinancialData"
            class="card text-muted-foreground px-5 py-8 text-center text-sm">
            No income or expenses recorded for {{ currentYear }} yet.
        </p>

        <div v-else class="grid gap-4 lg:grid-cols-2">
            <div class="card px-5 py-4">
                <h3 class="text-foreground text-sm font-medium">Revenue vs expenses</h3>
                <p class="text-muted-foreground mt-0.5 text-xs">Monthly totals</p>
                <LineChart :chart-data="lineChartData" height="320px" class="mt-4" />
            </div>
            <div class="card px-5 py-4">
                <h3 class="text-foreground text-sm font-medium">Revenue distribution</h3>
                <p class="text-muted-foreground mt-0.5 text-xs">Year to date</p>
                <DonutChart :chart-data="doughnutData" height="320px" class="mt-4" />
            </div>
            <div class="card px-5 py-4">
                <h3 class="text-foreground text-sm font-medium">Monthly comparison</h3>
                <p class="text-muted-foreground mt-0.5 text-xs">Income beside expenses</p>
                <BarChart :chart-data="barChartData" height="320px" class="mt-4" />
            </div>
            <div class="card px-5 py-4">
                <h3 class="text-foreground text-sm font-medium">Income trend</h3>
                <p class="text-muted-foreground mt-0.5 text-xs">Monthly income</p>
                <AreaChart :chart-data="areaChartData" height="320px" class="mt-4" />
            </div>
        </div>
    </main>
</template>

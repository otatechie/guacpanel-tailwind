<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import ApexLineChart from '@js/Components/Charts/ApexLineChart.vue'
import ApexDonutChart from '@js/Components/Charts/ApexDonutChart.vue'
import ApexBarChart from '@js/Components/Charts/ApexBarChart.vue'
import ApexAreaChart from '@js/Components/Charts/ApexAreaChart.vue'

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

const months = computed(() => props.financialMetrics?.months || [])
const incomeByMonth = month => Number(props.financialMetrics?.income?.[month] || 0)
const expenseByMonth = month => Number(props.financialMetrics?.expense?.[month] || 0)

const lineChartData = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: 'Income',
            data: months.value.map(incomeByMonth),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true,
        },
        {
            label: 'Expenses',
            data: months.value.map(expenseByMonth),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true,
        },
    ],
}))

const doughnutData = computed(() => ({
    labels: ['Total Income', 'Total Expenses'],
    datasets: [
        {
            label: 'Revenue Distribution',
            data: [
                months.value.reduce((sum, m) => sum + incomeByMonth(m), 0),
                months.value.reduce((sum, m) => sum + expenseByMonth(m), 0),
            ],
            backgroundColor: ['#10b981', '#ef4444'],
            borderWidth: 1,
        },
    ],
}))

const barChartData = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: 'Income',
            data: months.value.map(incomeByMonth),
            backgroundColor: '#10b981',
            borderColor: '#10b981',
            borderWidth: 1,
        },
        {
            label: 'Expenses',
            data: months.value.map(expenseByMonth),
            backgroundColor: '#ef4444',
            borderColor: '#ef4444',
            borderWidth: 1,
        },
    ],
}))

const areaChartData = computed(() => ({
    labels: months.value,
    datasets: [
        {
            label: 'Income',
            data: months.value.map(incomeByMonth),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true,
        },
        {
            label: 'Expenses',
            data: months.value.map(expenseByMonth),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true,
        },
    ],
}))
</script>

<template>
    <Head title="Charts" />

    <main class="mx-auto max-w-7xl">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-foreground">Charts</h1>
            <p class="mt-1 text-sm text-muted-foreground">Financial metrics overview</p>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="card p-5">
                <ApexLineChart :chart-data="lineChartData" title="Revenue vs Expenses" height="320px" />
            </div>
            <div class="card p-5">
                <ApexDonutChart :chart-data="doughnutData" title="Revenue distribution" height="320px" />
            </div>
            <div class="card p-5">
                <ApexBarChart :chart-data="barChartData" title="Monthly comparison" height="320px" />
            </div>
            <div class="card p-5">
                <ApexAreaChart :chart-data="areaChartData" title="Income trend" height="320px" />
            </div>
        </div>
    </main>
</template>

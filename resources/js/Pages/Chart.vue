<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Default from '../Layouts/Default.vue'
import ApexLineChart from '@/Components/Charts/ApexLineChart.vue'
import ApexDonutChart from '@/Components/Charts/ApexDonutChart.vue'
import ApexBarChart from '@/Components/Charts/ApexBarChart.vue'
import ApexAreaChart from '@/Components/Charts/ApexAreaChart.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    financialMetrics: {
        type: Object,
        required: true,
        default: () => ({
            income: {},
            expense: {}
        })
    }
})

const lineChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.income?.[month] || 0)
            ),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.expense?.[month] || 0)
            ),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        }
    ]
}))

const doughnutData = computed(() => ({
    labels: ['Total Income', 'Total Expenses'],
    datasets: [
        {
            label: 'Revenue Distribution',
            data: [
                (props.financialMetrics?.months || []).reduce(
                    (sum, month) => sum + Number(props.financialMetrics?.income?.[month] || 0),
                    0
                ),
                (props.financialMetrics?.months || []).reduce(
                    (sum, month) => sum + Number(props.financialMetrics?.expense?.[month] || 0),
                    0
                )
            ],
            backgroundColor: ['#10b981', '#ef4444'],
            borderWidth: 1
        }
    ]
}))

const barChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.income?.[month] || 0)
            ),
            backgroundColor: '#10b981',
            borderColor: '#10b981',
            borderWidth: 1
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.expense?.[month] || 0)
            ),
            backgroundColor: '#ef4444',
            borderColor: '#ef4444',
            borderWidth: 1
        }
    ]
}))

const areaChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.income?.[month] || 0)
            ),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || []).map(month =>
                Number(props.financialMetrics?.expense?.[month] || 0)
            ),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)'
        }
    ]
}))
</script>

<template>
    <Head title="Dashboard" />

    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-6xl mx-auto">
            <!-- Charts -->
            <section
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 space-y-8">
                    <div>
                        <ApexLineChart
                            :chart-data="lineChartData"
                            :title="'Revenue vs Expenses (Line Chart)'"
                            height="400px" />
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700" />
                    <div>
                        <ApexDonutChart
                            :chart-data="doughnutData"
                            :title="'Revenue Distribution (Doughnut Chart)'"
                            height="400px" />
                    </div>
                </div>
            </section>

            <section
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 space-y-8">
                    <div>
                        <ApexBarChart
                            :chart-data="barChartData"
                            :title="'Revenue vs Expenses (Bar Chart)'"
                            height="400px" />
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700" />
                    <div>
                        <ApexAreaChart
                            :chart-data="areaChartData"
                            :title="'Income Trend (Area Chart)'"
                            height="400px" />
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>

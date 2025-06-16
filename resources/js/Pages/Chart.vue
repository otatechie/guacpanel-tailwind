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

// Line chart data
const lineChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.income?.[month] || 0)),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.expense?.[month] || 0)),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        }
    ]
}));

// Doughnut chart data
const doughnutData = computed(() => ({
    labels: ['Total Income', 'Total Expenses'],
    datasets: [{
        label: 'Revenue Distribution',
        data: [
            (props.financialMetrics?.months || [])
                .reduce((sum, month) => sum + Number(props.financialMetrics?.income?.[month] || 0), 0),
            (props.financialMetrics?.months || [])
                .reduce((sum, month) => sum + Number(props.financialMetrics?.expense?.[month] || 0), 0)
        ],
        backgroundColor: ['#10b981', '#ef4444'],
        borderWidth: 1
    }]
}));

// Bar chart data   
const barChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.income?.[month] || 0)),
            backgroundColor: '#10b981',
            borderColor: '#10b981',
            borderWidth: 1
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.expense?.[month] || 0)),
            backgroundColor: '#ef4444',
            borderColor: '#ef4444',
            borderWidth: 1
        }
    ]
}));

// Area chart data
const areaChartData = computed(() => ({
    labels: props.financialMetrics?.months || [],
    datasets: [
        {
            label: 'Income',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.income?.[month] || 0)),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 1,
            tension: 0.4,
            fill: true
        },
        {
            label: 'Expenses',
            data: (props.financialMetrics?.months || [])
                .map(month => Number(props.financialMetrics?.expense?.[month] || 0)),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
        }
    ]
}));
</script>


<template>

    <Head title="Dashboard" />

    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Charts -->
            <section
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 space-y-8">
                    <div>
                        <ApexLineChart :chart-data="lineChartData" :title="'Revenue vs Expenses (Line Chart)'"
                            height="400px" />
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700">
                    <div>
                        <ApexDonutChart :chart-data="doughnutData" :title="'Revenue Distribution (Doughnut Chart)'"
                            height="400px" />
                    </div>
                </div>
            </section>

            <section
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 space-y-8">
                    <div>
                        <ApexBarChart :chart-data="barChartData" :title="'Revenue vs Expenses (Bar Chart)'"
                            height="400px" />
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700">
                    <div>
                        <ApexAreaChart :chart-data="areaChartData" :title="'Income Trend (Area Chart)'"
                            height="400px" />
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>

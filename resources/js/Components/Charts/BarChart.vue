<script setup>
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    defaults
} from 'chart.js'
import { computed } from 'vue'

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
)

// Set global defaults
defaults.font.family = 'Instrument Sans'
defaults.color = '#6b7280'
defaults.borderColor = '#e5e7eb'

const props = defineProps({
    // Chart data
    chartData: {
        type: Object,
        required: true
    },
    // Chart height
    height: {
        type: String,
        default: '400px'
    },
    // Chart title
    title: {
        type: String,
        default: ''
    },
    // Y-axis formatting
    yAxisFormat: {
        type: Function,
        default: value => value
    },
    // Enable/disable features
    enableGrid: {
        type: Boolean,
        default: true
    },
    enableLegend: {
        type: Boolean,
        default: true
    },
    enableTooltip: {
        type: Boolean,
        default: true
    },
    // Animation duration
    animationDuration: {
        type: Number,
        default: 1000
    },
    // Bar options
    horizontal: {
        type: Boolean,
        default: false
    },
    // Custom colors for datasets
    colors: {
        type: Array,
        default: () => ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#6366f1']
    }
})

const finalOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: props.horizontal ? 'y' : 'x',
    animation: {
        duration: props.animationDuration
    },
    plugins: {
        legend: {
            display: props.enableLegend,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                    size: 12
                }
            }
        },
        title: {
            display: !!props.title,
            text: props.title,
            padding: {
                top: 10,
                bottom: 20
            },
            font: {
                size: 16,
                weight: 'normal'
            }
        },
        tooltip: {
            enabled: props.enableTooltip
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                display: props.enableGrid,
                drawBorder: false
            },
            ticks: {
                padding: 10,
                callback: props.yAxisFormat
            }
        },
        x: {
            grid: {
                display: false,
                drawBorder: false
            },
            ticks: {
                padding: 10
            }
        }
    }
}))

const colorizedData = computed(() => {
    const data = { ...props.chartData }
    if (data.datasets) {
        data.datasets = data.datasets.map((dataset, index) => ({
            ...dataset,
            backgroundColor: dataset.backgroundColor || props.colors[index % props.colors.length]
        }))
    }
    return data
})
</script>

<template>
    <div class="chart-container" :style="{ position: 'relative', height: height, width: '100%' }">
        <Bar :data="colorizedData" :options="finalOptions" />
    </div>
</template> 
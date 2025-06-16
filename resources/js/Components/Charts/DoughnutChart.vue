<script setup>
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    defaults
} from 'chart.js'
import { computed } from 'vue'

// Register Chart.js components
ChartJS.register(
    ArcElement,
    Title,
    Tooltip,
    Legend
)

// Set global defaults
defaults.font.family = 'Instrument Sans'
defaults.color = '#6b7280'

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
    // Value formatting
    valueFormat: {
        type: Function,
        default: value => value
    },
    // Enable/disable features
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
    // Custom colors
    colors: {
        type: Array,
        default: () => [
            '#3b82f6',
            '#ef4444',
            '#10b981',
            '#f59e0b',
            '#6366f1',
            '#8b5cf6',
            '#ec4899'
        ]
    }
})

const finalOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
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
            enabled: props.enableTooltip,
            callbacks: {
                label: (context) => {
                    const label = context.label || '';
                    const value = props.valueFormat(context.raw);
                    const percentage = Math.round((context.raw / context.dataset.data.reduce((a, b) => a + b)) * 100);
                    return `${label}: ${value} (${percentage}%)`;
                }
            }
        }
    }
}))

const colorizedData = computed(() => {
    const data = { ...props.chartData }
    if (data.datasets) {
        data.datasets = data.datasets.map(dataset => ({
            ...dataset,
            backgroundColor: props.colors.slice(0, dataset.data.length)
        }))
    }
    return data
})
</script>

<template>
    <div class="chart-container" :style="{ position: 'relative', height: height, width: '100%' }">
        <Doughnut :data="colorizedData" :options="finalOptions" />
    </div>
</template> 
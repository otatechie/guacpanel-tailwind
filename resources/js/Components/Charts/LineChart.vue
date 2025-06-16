<script setup>
import { Line } from 'vue-chartjs'
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
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
	PointElement,
	LineElement,
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
	// Custom colors for datasets
	colors: {
		type: Array,
		default: () => ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#6366f1']
	},
	// Base chart options that can be extended
	chartOptions: {
		type: Object,
		default: () => ({})
	}
})

const finalOptions = computed(() => {
	const baseOptions = {
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
		},
		elements: {
			line: {
				tension: 0.4
			},
			point: {
				radius: 4,
				hoverRadius: 6
			}
		}
	}

	return {
		...baseOptions,
		...props.chartOptions
	}
})

const colorizedData = computed(() => {
	const data = { ...props.chartData }
	if (data.datasets) {
		data.datasets = data.datasets.map((dataset, index) => ({
			...dataset,
			borderColor: dataset.borderColor || props.colors[index % props.colors.length],
			backgroundColor: dataset.backgroundColor || props.colors[index % props.colors.length]
		}))
	}
	return data
})
</script>

<template>
	<div class="chart-container" :style="{ position: 'relative', height: height, width: '100%' }">
		<Line :data="colorizedData" :options="finalOptions" />
	</div>
</template>
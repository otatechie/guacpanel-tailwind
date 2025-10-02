<script setup>
import { computed, ref, onUnmounted } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
    chartData: { type: Object, required: true },
    height: { type: String, default: '400px' },
    title: { type: String, default: '' }
})

const isDark = ref(document.documentElement.classList.contains('dark'))

const observer = new MutationObserver(() => {
    isDark.value = document.documentElement.classList.contains('dark')
})

observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
})

onUnmounted(() => observer.disconnect())

const series = computed(() =>
    props.chartData.datasets.map(dataset => ({
        name: dataset.label,
        data: dataset.data
    }))
)

const fontFamily = 'Zalando Sans'
const chartOptions = computed(() => {
    const dark = isDark.value
    const textColor = dark ? '#ffffff' : '#111827'
    const axisColor = dark ? '#9ca3af' : '#6b7280'
    const gridColor = dark ? '#374151' : '#e5e7eb'

    return {
        chart: {
            type: 'line',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            animations: { enabled: false },
            redrawOnWindowResize: true,
            redrawOnParentResize: true,
            foreColor: axisColor
        },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#10b981', '#ef4444'],
        title: {
            text: props.title,
            align: 'center',
            style: {
                fontSize: '16px',
                fontWeight: '600',
                fontFamily,
                color: textColor
            }
        },
        xaxis: {
            categories: props.chartData.labels,
            labels: {
                style: {
                    colors: axisColor,
                    fontFamily
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: axisColor,
                    fontFamily
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        legend: {
            show: false,
            position: 'top',
            horizontalAlign: 'left',
            fontFamily,
            fontSize: '14px',
            labels: { colors: axisColor },
            markers: { radius: 12 }
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontFamily: fontFamily
            },
            x: { show: true }
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    title: { style: { fontSize: '14px', fontFamily } },
                    stroke: { width: 1.5 },
                    xaxis: {
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontFamily
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '10px',
                                fontFamily
                            }
                        }
                    }
                }
            }
        ]
    }
})
</script>

<template>
    <div class="w-full h-full">
        <VueApexCharts
            :key="isDark"
            type="line"
            :height="height"
            :options="chartOptions"
            :series="series"
            class="w-full" />
    </div>
</template>

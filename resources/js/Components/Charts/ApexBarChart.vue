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

const fontFamily = 'Zalando Sans'

const series = computed(() =>
    props.chartData.datasets.map(dataset => ({
        name: dataset.label,
        data: dataset.data
    }))
)

const chartOptions = computed(() => {
    const dark = isDark.value
    const textColor = dark ? '#ffffff' : '#111827'
    const axisColor = '#6b7280'
    const gridColor = '#e5e7eb'

    return {
        chart: {
            type: 'bar',
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
            foreColor: dark ? '#9ca3af' : axisColor
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 0
            }
        },
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
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.chartData.labels,
            labels: {
                style: { colors: axisColor, fontFamily }
            }
        },
        yaxis: {
            labels: {
                style: { colors: axisColor, fontFamily },
                formatter: val => val.toLocaleString()
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        legend: { show: false },
        tooltip: {
            theme: 'dark',
            style: {
                fontFamily: fontFamily
            },
            y: { formatter: val => val.toLocaleString() }
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    title: { style: { fontSize: '14px', fontFamily } },
                    plotOptions: { bar: { columnWidth: '70%' } },
                    xaxis: {
                        labels: { style: { fontSize: '10px', fontFamily } }
                    },
                    yaxis: {
                        labels: { style: { fontSize: '10px', fontFamily } }
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
            type="bar"
            :height="height"
            :options="chartOptions"
            :series="series"
            class="w-full" />
    </div>
</template>

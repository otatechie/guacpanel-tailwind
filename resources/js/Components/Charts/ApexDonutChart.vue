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

const series = computed(() => props.chartData.datasets[0].data)
const labels = computed(() => props.chartData.labels)

const fontFamily = 'Zalando Sans'
const chartOptions = computed(() => {
    const dark = isDark.value
    const textColor = dark ? '#ffffff' : '#111827'
    const axisColor = dark ? '#9ca3af' : '#6b7280'

    return {
        chart: {
            type: 'donut',
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
        labels: labels.value,
        legend: {
            show: false,
            position: 'top',
            horizontalAlign: 'left',
            fontFamily,
            fontSize: '14px',
            labels: { colors: axisColor },
            markers: { radius: 12 }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter(w) {
                                return w.globals.seriesTotals
                                    .reduce((a, b) => a + b, 0)
                                    .toLocaleString()
                            }
                        }
                    }
                },
                expandOnClick: true,
                startAngle: 0,
                endAngle: 360,
                dataLabels: { offset: -3 }
            }
        },
        dataLabels: {
            enabled: true,
            formatter(val, opts) {
                return opts.w.globals.seriesTotals[opts.seriesIndex].toLocaleString()
            }
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontFamily: fontFamily
            },
            y: { formatter: val => val.toLocaleString() }
        },
        states: {
            hover: { filter: { type: 'lighten', value: 0.1 } },
            active: { filter: { type: 'darken', value: 0.35 } }
        },
        grid: { borderColor: axisColor },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    title: { style: { fontSize: '14px', fontFamily } },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    value: { fontSize: '12px', fontFamily },
                                    total: { fontSize: '12px', fontFamily }
                                }
                            }
                        }
                    },
                    dataLabels: { style: { fontSize: '10px', fontFamily } }
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
            type="donut"
            :height="height"
            :options="chartOptions"
            :series="series"
            class="w-full" />
    </div>
</template>

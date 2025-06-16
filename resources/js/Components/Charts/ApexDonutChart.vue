<script setup>
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    },
    height: {
        type: String,
        default: '400px'
    },
    title: {
        type: String,
        default: ''
    }
})

const series = computed(() => props.chartData.datasets[0].data)
const labels = computed(() => props.chartData.labels)

const chartOptions = computed(() => ({
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
        animations: {
            enabled: false
        },
        redrawOnWindowResize: true,
        redrawOnParentResize: true,
        width: '100%',
        height: '100%'
    },
    colors: ['#10b981', '#ef4444'],
    title: {
        text: props.title,
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            fontFamily: 'Instrument Sans'
        }
    },
    labels: labels.value,
    legend: {
        show: false,
        position: 'top',
        horizontalAlign: 'left',
        fontFamily: 'Instrument Sans',
        fontSize: '14px',
        markers: {
            radius: 12
        }
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
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString()
                        }
                    }
                }
            },
            expandOnClick: true,
            startAngle: 0,
            endAngle: 360,
            offsetX: 0,
            offsetY: 0,
            customScale: 1,
            dataLabels: {
                offset: -3
            }
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val, opts) {
            return opts.w.globals.seriesTotals[opts.seriesIndex].toLocaleString()
        }
    },
    tooltip: {
        theme: 'light',
        y: {
            formatter: (value) => value.toLocaleString()
        }
    },
    states: {
        hover: {
            filter: {
                type: 'lighten',
                value: 0.1
            }
        },
        active: {
            filter: {
                type: 'darken',
                value: 0.35
            }
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            title: {
                style: {
                    fontSize: '14px'
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            value: {
                                fontSize: '12px'
                            },
                            total: {
                                fontSize: '12px'
                            }
                        }
                    }
                }
            },
            dataLabels: {
                style: {
                    fontSize: '10px'
                }
            }
        }
    }]
}))
</script>

<template>
    <div class="w-full h-full">
        <VueApexCharts
            :type="'donut'"
            :height="height"
            :options="chartOptions"
            :series="series"
            class="w-full"
        />
    </div>
</template> 
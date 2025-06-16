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

const series = computed(() => props.chartData.datasets.map(dataset => ({
    name: dataset.label,
    data: dataset.data
})))

const chartOptions = computed(() => ({
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
        animations: {
            enabled: false
        },
        redrawOnWindowResize: true,
        redrawOnParentResize: true,
        width: '100%',
        height: '100%'
    },
    stroke: {
        curve: 'smooth',
        width: 2
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
    xaxis: {
        categories: props.chartData.labels,
        labels: {
            style: {
                colors: '#6b7280',
                fontFamily: 'Instrument Sans'
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#6b7280',
                fontFamily: 'Instrument Sans'
            }
        }
    },
    grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4
    },
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
    tooltip: {
        theme: 'light',
        x: {
            show: true
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
            stroke: {
                width: 1.5
            },
            xaxis: {
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            }
        }
    }]
}))
</script>

<template>
    <div class="w-full h-full">
        <VueApexCharts
            :type="'line'"
            :height="height"
            :options="chartOptions"
            :series="series"
            class="w-full"
        />
    </div>
</template> 
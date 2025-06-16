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
        animations: {
            enabled: false
        },
        redrawOnWindowResize: true,
        redrawOnParentResize: true,
        width: '100%',
        height: '100%'
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
            fontWeight: 'bold',
            fontFamily: 'Instrument Sans'
        }
    },
    dataLabels: {
        enabled: false
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
            },
            formatter: (value) => {
                return value.toLocaleString()
            }
        }
    },
    grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4
    },
    legend: {
        show: false
    },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: (value) => {
                return value.toLocaleString()
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
                bar: {
                    columnWidth: '70%'
                }
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
            :type="'bar'" 
            :height="height" 
            :options="chartOptions" 
            :series="series"
            class="w-full" 
        />
    </div>
</template>
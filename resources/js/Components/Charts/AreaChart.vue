<script setup>
import { computed } from 'vue'
import { VisArea, VisAxis, VisCrosshair, VisTooltip, VisXYContainer } from '@unovis/vue'
import { colorAt, formatNumber, integerTickValues, toRecords, tooltipTemplate } from './chart'
import ChartLegend from './ChartLegend.vue'

const props = defineProps({
    chartData: { type: Object, required: true },
    height: { type: String, default: '400px' },
})

const records = computed(() => toRecords(props.chartData))
const datasets = computed(() => props.chartData.datasets || [])

const x = (d, i) => i
// One VisArea per series, so the areas overlap rather than stack — stacking
// income on top of expenses would misread the data.
const seriesY = series => d => d.values[series]
const y = computed(() => datasets.value.map((_, series) => seriesY(series)))
const color = (_, series) => colorAt(series)

const xTickFormat = i => props.chartData.labels?.[i] ?? ''
// Tick on every data point, otherwise the scale invents positions between months.
const xTickValues = computed(() => (props.chartData.labels || []).map((_, i) => i))
const yTickValues = computed(() => integerTickValues(records.value))
const crosshairTemplate = d => tooltipTemplate(d, datasets.value)
</script>

<template>
    <div class="h-full w-full">
        <VisXYContainer :data="records" :height="height">
            <VisArea
                v-for="(dataset, series) in datasets"
                :key="dataset.label"
                :x="x"
                :y="seriesY(series)"
                :color="colorAt(series)"
                :opacity="0.25"
                :line="true"
                :line-width="2"
                curve-type="monotoneX" />
            <VisAxis
                type="x"
                :tick-format="xTickFormat"
                :tick-values="xTickValues"
                :grid-line="false" />
            <VisAxis
                type="y"
                :tick-format="formatNumber"
                :tick-values="yTickValues"
                :domain-line="false" />
            <VisCrosshair :x="x" :y="y" :color="color" :template="crosshairTemplate" />
            <VisTooltip />
        </VisXYContainer>
        <ChartLegend v-if="datasets.length > 1" :items="datasets.map(d => ({ label: d.label }))" />
    </div>
</template>

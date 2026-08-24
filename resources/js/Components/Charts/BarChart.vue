<script setup>
import { computed } from 'vue'
import { VisAxis, VisCrosshair, VisGroupedBar, VisTooltip, VisXYContainer } from '@unovis/vue'
import { colorAt, formatNumber, integerTickValues, toRecords, tooltipTemplate } from './chart'
import ChartLegend from './ChartLegend.vue'

const props = defineProps({
    chartData: { type: Object, required: true },
    height: { type: String, default: '400px' },
    // 'horizontal' puts the category labels down the side — the readable choice
    // for ranked lists, where labels are words rather than months.
    orientation: {
        type: String,
        default: 'vertical',
        validator: value => ['vertical', 'horizontal'].includes(value),
    },
})

const records = computed(() => toRecords(props.chartData))
const datasets = computed(() => props.chartData.datasets || [])

const x = (d, i) => i
const y = computed(() => datasets.value.map((_, series) => d => d.values[series]))
const color = (_, series) => colorAt(series)

const horizontal = computed(() => props.orientation === 'horizontal')
const categoryAxis = computed(() => (horizontal.value ? 'y' : 'x'))
const valueAxis = computed(() => (horizontal.value ? 'x' : 'y'))

const xTickFormat = i => props.chartData.labels?.[i] ?? ''
// Tick on every data point, otherwise the scale invents positions between months.
const xTickValues = computed(() => (props.chartData.labels || []).map((_, i) => i))
const yTickValues = computed(() => integerTickValues(records.value))
const crosshairTemplate = d => tooltipTemplate(d, datasets.value)
</script>

<template>
    <div class="h-full w-full">
        <VisXYContainer :data="records" :height="height">
            <VisGroupedBar
                :x="x"
                :y="y"
                :color="color"
                :orientation="orientation"
                :group-padding="0.2"
                :bar-padding="0.05" />
            <VisAxis
                :type="categoryAxis"
                :tick-format="xTickFormat"
                :tick-values="xTickValues"
                :grid-line="false" />
            <VisAxis
                :type="valueAxis"
                :tick-format="formatNumber"
                :tick-values="yTickValues"
                :domain-line="false" />
            <VisCrosshair :x="x" :y="y" :color="color" :template="crosshairTemplate" />
            <VisTooltip />
        </VisXYContainer>
        <ChartLegend v-if="datasets.length > 1" :items="datasets.map(d => ({ label: d.label }))" />
    </div>
</template>

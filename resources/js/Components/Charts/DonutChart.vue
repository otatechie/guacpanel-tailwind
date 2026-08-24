<script setup>
import { computed } from 'vue'
import { VisDonut, VisDonutSelectors, VisSingleContainer, VisTooltip } from '@unovis/vue'
import { colorAt, formatNumber } from './chart'
import ChartLegend from './ChartLegend.vue'

const props = defineProps({
    chartData: { type: Object, required: true },
    height: { type: String, default: '400px' },
})

const segments = computed(() =>
    (props.chartData.labels || []).map((label, i) => ({
        label,
        value: Number(props.chartData.datasets?.[0]?.data?.[i] ?? 0),
    }))
)

const total = computed(() => segments.value.reduce((sum, segment) => sum + segment.value, 0))

const value = d => d.value
const color = (_, i) => colorAt(i)

// Apex sized the hole as a percentage of the radius; Unovis takes a pixel
// thickness, so derive it from the height to keep the ring proportional.
const arcWidth = computed(() => Math.max(24, Math.round(parseInt(props.height, 10) * 0.18)))

const triggers = {
    [VisDonutSelectors.segment]: d => {
        const segment = d.data ?? d
        return `<div class="text-xs"><span class="font-medium">${segment.label}</span>
            <span class="ml-2 tabular-nums">${formatNumber(segment.value)}</span></div>`
    },
}
</script>

<template>
    <div class="h-full w-full">
        <VisSingleContainer :data="segments" :height="height">
            <VisDonut
                :value="value"
                :color="color"
                :arc-width="arcWidth"
                :show-background="false"
                :central-label="formatNumber(total)"
                central-sub-label="Total" />
            <VisTooltip :triggers="triggers" />
        </VisSingleContainer>

        <ChartLegend :items="segments" />
    </div>
</template>

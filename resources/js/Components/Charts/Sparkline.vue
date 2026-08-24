<script setup>
import { computed } from 'vue'
import { VisArea, VisLine, VisXYContainer } from '@unovis/vue'
import { colorAt } from './chart'

const props = defineProps({
    data: { type: Array, required: true },
    height: { type: String, default: '32px' },
})

const records = computed(() => props.data.map((value, i) => ({ i, value: Number(value ?? 0) })))
const x = d => d.i
const y = d => d.value
</script>

<template>
    <VisXYContainer
        v-if="records.length > 1"
        :data="records"
        :height="height"
        :margin="{ top: 2, right: 0, bottom: 2, left: 0 }">
        <VisArea :x="x" :y="y" :color="colorAt(0)" :opacity="0.15" curve-type="monotoneX" />
        <VisLine :x="x" :y="y" :color="colorAt(0)" :line-width="1.5" curve-type="monotoneX" />
    </VisXYContainer>
</template>

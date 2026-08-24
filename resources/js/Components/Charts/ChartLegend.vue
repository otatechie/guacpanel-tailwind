<script setup>
import { colorAt, formatNumber } from './chart'

defineProps({
    // [{ label, value? }] — value is shown when the chart can't label points itself.
    items: { type: Array, required: true },
})
</script>

<template>
    <ul class="mt-3 flex flex-wrap justify-center gap-x-5 gap-y-1.5">
        <li v-for="(item, i) in items" :key="item.label" class="flex items-center gap-2 text-xs">
            <span class="size-2 shrink-0 rounded-full" :style="{ background: colorAt(i) }" />
            <span class="text-muted-foreground">{{ item.label }}</span>
            <span v-if="item.value != null" class="text-foreground font-medium tabular-nums">
                {{ formatNumber(item.value) }}
            </span>
        </li>
    </ul>
</template>

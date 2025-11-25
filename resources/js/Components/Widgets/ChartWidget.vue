<!--
  ChartWidget - Displays a sparkline chart with trend indicators

  Features:
  - Dynamic SVG line chart generated from data array
  - Auto-scales data to fit container
  - Shows percentage change with up/down indicator
  - Gradient fill area below line
  - Supports multiple color themes

  Usage:
  <ChartWidget
    title="Revenue"
    value="$45,231"
    :change="12.5"
    :data="[30, 40, 35, 50, 70]"
    color="emerald" />
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    change: {
        type: Number,
        default: 0
    },
    data: {
        type: Array,
        required: true,
        validator: (arr) => arr.every(item => typeof item === 'number')
    },
    color: {
        type: String,
        default: 'blue',
        validator: value => ['blue', 'green', 'red', 'yellow', 'purple', 'emerald'].includes(value)
    }
})

const max = computed(() => Math.max(...props.data))
const min = computed(() => Math.min(...props.data))

const normalizedData = computed(() => {
    const range = max.value - min.value
    if (range === 0) return props.data.map(() => 50)
    return props.data.map(val => ((val - min.value) / range) * 100)
})

const pathData = computed(() => {
    const width = 100
    const height = 60
    const points = normalizedData.value.map((val, i) => {
        const x = (i / (props.data.length - 1)) * width
        const y = height - (val / 100) * height
        return `${x},${y}`
    })
    return `M ${points.join(' L ')}`
})

const gradientId = computed(() => `gradient-${props.color}-${Math.random().toString(36).substr(2, 9)}`)

const colorClasses = {
    blue: 'text-blue-500',
    green: 'text-green-500',
    red: 'text-red-500',
    yellow: 'text-yellow-500',
    purple: 'text-purple-500',
    emerald: 'text-emerald-500'
}

const gradientColors = {
    blue: { from: '#3b82f6', to: '#93c5fd' },
    green: { from: '#22c55e', to: '#86efac' },
    red: { from: '#ef4444', to: '#fca5a5' },
    yellow: { from: '#eab308', to: '#fde047' },
    purple: { from: '#a855f7', to: '#d8b4fe' },
    emerald: { from: '#10b981', to: '#6ee7b7' }
}

const isPositiveChange = computed(() => props.change > 0)
</script>

<template>
    <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] shadow-xs p-4 sm:p-6">
        <!-- Header -->
        <div class="mb-3 sm:mb-4">
            <h3 class="text-xs sm:text-sm font-medium text-[var(--color-text-muted)] mb-2">
                {{ title }}
            </h3>
            <div class="flex items-baseline gap-2 sm:gap-3">
                <p class="text-2xl sm:text-3xl font-semibold text-[var(--color-text)]">
                    {{ value }}
                </p>
                <span
                    v-if="change !== 0"
                    class="text-xs sm:text-sm font-semibold flex items-center gap-1 shrink-0"
                    :class="isPositiveChange ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                    <svg
                        class="w-3 h-3 sm:w-4 sm:h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        :class="{ 'rotate-180': !isPositiveChange }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                    </svg>
                    {{ Math.abs(change) }}%
                </span>
            </div>
        </div>

        <!-- Chart -->
        <div class="relative h-12 sm:h-16">
            <svg viewBox="0 0 100 60" class="w-full h-full" preserveAspectRatio="none">
                <defs>
                    <linearGradient :id="gradientId" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" :stop-color="gradientColors[color].from" stop-opacity="0.3" />
                        <stop offset="100%" :stop-color="gradientColors[color].to" stop-opacity="0.05" />
                    </linearGradient>
                </defs>
                <!-- Fill area -->
                <path
                    :d="`${pathData} L 100,60 L 0,60 Z`"
                    :fill="`url(#${gradientId})`" />
                <!-- Line -->
                <path
                    :d="pathData"
                    fill="none"
                    :class="colorClasses[color]"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </div>
    </div>
</template>

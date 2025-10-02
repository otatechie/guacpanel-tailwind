<!--
  MetricWidget - Bold brutalist metric card with geometric accents

  Features:
  - Strong border and sharp edges for brutalist aesthetic
  - Large monospace numbers with tabular layout
  - Colored top bar and corner triangle accent
  - Trend indicators with up/down/neutral symbols
  - Auto-formats numbers with locale
  - Minimal icon display

  Usage:
  <MetricWidget
    title="Total Revenue"
    :value="84621"
    trend="up"
    :change="12.5"
    :svg="revenueIconSVG"
    color="emerald" />
-->
<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    trend: {
        type: String,
        default: null,
        validator: value => ['up', 'down', null].includes(value)
    },
    change: {
        type: Number,
        default: null
    },
    svg: {
        type: String,
        default: ''
    },
    viewBox: {
        type: String,
        default: '0 0 24 24'
    },
    color: {
        type: String,
        default: 'blue'
    }
})

const colorClasses = {
    blue: 'text-blue-600 dark:text-blue-400',
    green: 'text-green-600 dark:text-green-400',
    red: 'text-red-600 dark:text-red-400',
    yellow: 'text-yellow-600 dark:text-yellow-400',
    purple: 'text-purple-600 dark:text-purple-400',
    primary: 'text-blue-600 dark:text-blue-400'
}

const formatValue = value => {
    if (typeof value === 'number') {
        return value.toLocaleString('en-US')
    }
    return value || '0'
}

const formatChange = change => {
    if (!change) return null
    return `${change > 0 ? '+' : ''}${Math.abs(change).toFixed(1)}%`
}
</script>

<template>
    <div
        class="relative bg-[var(--color-surface)] border-2 border-[var(--color-text)] overflow-hidden">
        <div class="h-1" :class="colorClasses[color]"></div>
        <div class="p-4 sm:p-6">
            <div class="flex items-start justify-between mb-4 sm:mb-6">
                <div class="flex-1 min-w-0">
                    <h3
                        class="text-[9px] sm:text-[10px] font-semibold uppercase tracking-[0.15em] text-[var(--color-text-muted)] mb-1 leading-tight">
                        {{ title }}
                    </h3>
                </div>

                <!-- Icon - minimal square -->
                <div v-if="svg" class="ml-3 sm:ml-4 shrink-0">
                    <svg
                        class="w-4 h-4 sm:w-5 sm:h-5"
                        :class="colorClasses[color]"
                        xmlns="http://www.w3.org/2000/svg"
                        :viewBox="viewBox"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="square"
                        stroke-linejoin="miter"
                        v-html="svg"></svg>
                </div>
            </div>

            <!-- Main value - monospace, large -->
            <div class="mb-2 sm:mb-3">
                <span
                    class="font-mono text-3xl sm:text-4xl font-semibold text-[var(--color-text)] tracking-tight leading-none tabular-nums">
                    {{ formatValue(value) }}
                </span>
            </div>

            <div v-if="change" class="flex items-center gap-2">
                <div
                    class="inline-flex items-center gap-1 font-mono text-xs font-medium tabular-nums"
                    :class="{
                        'text-green-700 dark:text-green-400': trend === 'up',
                        'text-red-700 dark:text-red-400': trend === 'down',
                        'text-[var(--color-text-muted)]': !trend
                    }">
                    <span v-if="trend === 'up'">▲</span>
                    <span v-else-if="trend === 'down'">▼</span>
                    <span v-else>━</span>
                    <span>{{ formatChange(change) }}</span>
                </div>
            </div>
        </div>

        <div
            class="absolute bottom-0 right-0 w-0 h-0 border-l-[20px] border-l-transparent border-b-[20px]"
            :class="{
                'border-b-blue-600 dark:border-b-blue-400': color === 'blue' || color === 'primary',
                'border-b-green-600 dark:border-b-green-400': color === 'green',
                'border-b-red-600 dark:border-b-red-400': color === 'red',
                'border-b-yellow-600 dark:border-b-yellow-400': color === 'yellow',
                'border-b-purple-600 dark:border-b-purple-400': color === 'purple'
            }"></div>
    </div>
</template>

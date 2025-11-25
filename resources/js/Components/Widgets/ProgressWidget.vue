<!--
  ProgressWidget - Displays progress with animated bar

  Features:
  - Shows current value vs maximum with progress bar
  - Auto-calculates and displays percentage
  - Animated fill with smooth transitions
  - Optional description text
  - Multiple color themes

  Usage:
  <ProgressWidget
    title="Storage Used"
    :value="75"
    :max="100"
    description="75GB of 100GB used"
    color="blue" />
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: Number,
        required: true
    },
    max: {
        type: Number,
        default: 100
    },
    description: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'blue',
        validator: value => ['blue', 'green', 'red', 'yellow', 'purple', 'indigo'].includes(value)
    },
    showPercentage: {
        type: Boolean,
        default: true
    }
})

const percentage = computed(() => {
    return Math.min(Math.round((props.value / props.max) * 100), 100)
})

const colorClasses = {
    blue: 'bg-blue-500 dark:bg-blue-400',
    green: 'bg-green-500 dark:bg-green-400',
    red: 'bg-red-500 dark:bg-red-400',
    yellow: 'bg-yellow-500 dark:bg-yellow-400',
    purple: 'bg-purple-500 dark:bg-purple-400',
    indigo: 'bg-indigo-500 dark:bg-indigo-400'
}

const bgColorClasses = {
    blue: 'bg-blue-100 dark:bg-blue-900/20',
    green: 'bg-green-100 dark:bg-green-900/20',
    red: 'bg-red-100 dark:bg-red-900/20',
    yellow: 'bg-yellow-100 dark:bg-yellow-900/20',
    purple: 'bg-purple-100 dark:bg-purple-900/20',
    indigo: 'bg-indigo-100 dark:bg-indigo-900/20'
}
</script>

<template>
    <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] shadow-xs p-6">
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h3 class="text-sm font-medium text-[var(--color-text-muted)] mb-1">
                    {{ title }}
                </h3>
                <p class="text-2xl font-semibold text-[var(--color-text)]">
                    {{ value }}<span class="text-lg text-[var(--color-text-muted)]">/{{ max }}</span>
                </p>
            </div>
            <div v-if="showPercentage" class="text-right">
                <span class="text-2xl font-semibold text-[var(--color-text)]">{{ percentage }}%</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="relative w-full h-3 rounded-full overflow-hidden" :class="bgColorClasses[color]">
            <div
                class="h-full rounded-full transition-all duration-500 ease-out"
                :class="colorClasses[color]"
                :style="{ width: `${percentage}%` }"></div>
        </div>

        <!-- Description -->
        <p v-if="description" class="mt-3 text-sm text-[var(--color-text-muted)]">
            {{ description }}
        </p>
    </div>
</template>

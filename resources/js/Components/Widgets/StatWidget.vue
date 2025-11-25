<!--
  StatWidget - Clean stat card with icon and optional trend

  Features:
  - Displays title, value, and optional description
  - Icon on the right with themed background
  - Optional trend indicator at bottom (up/down)
  - Horizontal layout optimized for dashboards
  - Multiple color themes for icon

  Usage:
  <StatWidget
    title="Total Users"
    value="1,234"
    description="Active this month"
    :icon="userIconSVG"
    trend="up"
    color="blue" />
-->
<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        required: true
    },
    trend: {
        type: String,
        default: 'neutral', // can be 'up', 'down', or 'neutral'
        validator: value => ['up', 'down', 'neutral'].includes(value)
    },
    color: {
        type: String,
        default: 'blue'
    }
})

const colorClasses = {
    blue: 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-200 dark:border-blue-700/30',
    green: 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-700/30',
    red: 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border-red-200 dark:border-red-700/30',
    yellow: 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400 border-yellow-200 dark:border-yellow-700/30',
    purple: 'bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 border-purple-200 dark:border-purple-700/30'
}
</script>

<template>
    <div class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] shadow-xs p-4 sm:p-6">
        <div class="flex items-start justify-between">
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-[var(--color-text-muted)] mb-1">
                    {{ title }}
                </p>
                <p class="text-2xl sm:text-3xl font-semibold text-[var(--color-text)] mb-1">
                    {{ value }}
                </p>
                <p v-if="description" class="text-xs sm:text-sm text-[var(--color-text-muted)]">
                    {{ description }}
                </p>
            </div>

            <!-- Icon -->
            <div
                class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-lg border shrink-0"
                :class="colorClasses[color]">
                <svg
                    class="w-5 h-5 sm:w-6 sm:h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    v-html="icon"></svg>
            </div>
        </div>

        <!-- Trend indicator at bottom -->
        <div v-if="trend !== 'neutral'" class="mt-4 pt-4 border-t border-[var(--color-border)]">
            <div v-if="trend === 'up'" class="flex items-center gap-1 text-green-600 dark:text-green-400">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-sm font-medium">+12% from last month</span>
            </div>
            <div v-else class="flex items-center gap-1 text-red-600 dark:text-red-400">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                </svg>
                <span class="text-sm font-medium">-8% from last month</span>
            </div>
        </div>
    </div>
</template>

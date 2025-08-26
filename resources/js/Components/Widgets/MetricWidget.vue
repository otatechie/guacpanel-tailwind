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
        default: "0 0 24 24"
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
    purple: 'bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 border-purple-200 dark:border-purple-700/30',
    primary: 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-200 dark:border-blue-700/30'
}

const formatValue = (value) => {
    if (typeof value === 'number') {
        return value.toLocaleString('en-US')
    }
    return value || '0'
}

const formatChange = (change) => {
    if (!change) return null
    return `${change > 0 ? '+' : ''}${Math.abs(change).toFixed(1)}%`
}
</script>

<template>
    <div
        class="group bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600">
        <!-- Header with title and icon -->
        <div class="flex items-center justify-between mb-4">
            <!-- Title -->
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 capitalize tracking-wide">
                {{ title }}
            </h3>

            <!-- Icon container with enhanced styling -->
            <div v-if="svg"
                class="flex items-center justify-center w-10 h-10 rounded-lg border transition-all duration-300 group-hover:scale-105"
                :class="colorClasses[color]">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:rotate-3"
                    xmlns="http://www.w3.org/2000/svg" :viewBox="viewBox" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" v-html="svg">
                </svg>
            </div>
        </div>

        <!-- Value and Change section -->
        <div class="space-y-2">
            <!-- Main value -->
            <div class="flex items-baseline gap-3">
                <span class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ formatValue(value) }}
                </span>

                <!-- Change indicator with enhanced styling -->
                <div v-if="change" class="flex items-center">
                    <div class="flex items-center px-2 py-1 rounded-lg text-sm font-medium transition-all duration-300"
                        :class="{
                            'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700/30': trend === 'up',
                            'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700/30': trend === 'down',
                            'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700/30': !trend
                        }">
                        <!-- Trend Icon -->
                        <svg v-if="trend" class="w-3.5 h-3.5 mr-1.5" :class="{ 'rotate-180': trend === 'down' }"
                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L20 12L18.6 13.4L13 7.8V20H11V7.8L5.4 13.4L4 12L12 4Z" fill="currentColor" />
                        </svg>
                        {{ formatChange(change) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
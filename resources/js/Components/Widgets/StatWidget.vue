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
        validator: (value) => ['up', 'down', 'neutral'].includes(value)
    },
    color: {
        type: String,
        default: 'blue'
    }
})

const colorClasses = {
    blue: 'bg-blue-50 text-blue-500 dark:bg-blue-900/20 dark:text-blue-400',
    green: 'bg-green-50 text-green-500 dark:bg-green-900/20 dark:text-green-400',
    red: 'bg-red-50 text-red-500 dark:bg-red-900/20 dark:text-red-400',
    yellow: 'bg-yellow-50 text-yellow-500 dark:bg-yellow-900/20 dark:text-yellow-400',
    purple: 'bg-purple-50 text-purple-500 dark:bg-purple-900/20 dark:text-purple-400'
}
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div :class="[colorClasses[color], 'rounded-lg p-3']">
                <span class="sr-only">{{ title }} icon</span>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-html="icon"></svg>
            </div>
            <div v-if="trend !== 'neutral'" class="flex items-center">
                <svg v-if="trend === 'up'" class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <svg v-else class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ title }}</h3>
            <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ value }}</p>
            <p v-if="description" class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ description }}</p>
        </div>
    </div>
</template>
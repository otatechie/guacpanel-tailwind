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
    blue: 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-200 dark:border-blue-700/30',
    green: 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-700/30',
    red: 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border-red-200 dark:border-red-700/30',
    yellow: 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400 border-yellow-200 dark:border-yellow-700/30',
    purple: 'bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 border-purple-200 dark:border-purple-700/30'
}
</script>

<template>
    <div class="group bg-white dark:bg-gray-800 rounded-xl p-3 sm:p-4 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600">
        <!-- Header row with icon and trend -->
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <!-- Icon with subtle background -->
            <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg border transition-all duration-300 group-hover:scale-105"
                 :class="colorClasses[color]">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" v-html="icon"></svg>
            </div>

            <!-- Trend indicator -->
            <div v-if="trend !== 'neutral'" class="flex items-center">
                <div v-if="trend === 'up'" class="flex items-center space-x-1 px-2 py-1 rounded-lg bg-green-50 dark:bg-green-900/20">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span class="text-xs font-medium text-green-700 dark:text-green-300">+12%</span>
                </div>
                <div v-else class="flex items-center space-x-1 px-2 py-1 rounded-lg bg-red-50 dark:bg-red-900/20">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                    <span class="text-xs font-medium text-red-700 dark:text-red-300">-8%</span>
                </div>
            </div>
        </div>

        <!-- Content section -->
        <div>
            <!-- Title -->
            <h3 class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 mb-2 capitalize tracking-wide">{{ title }}</h3>
            
            <!-- Value -->
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-2">{{ value }}</p>
            
            <!-- Description -->
            <p v-if="description" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ description }}</p>
        </div>
    </div>
</template>
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
        default: 'primary'
    }
})

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
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700 p-6 relative group">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-br opacity-[0.03] dark:opacity-[0.05] transition-opacity duration-300 group-hover:opacity-[0.05] dark:group-hover:opacity-[0.08] rounded-xl"
            :class="[
                `from-${color}-100/50 to-${color}-500/50`,
                `dark:from-${color}-500/30 dark:to-${color}-900/30`
            ]"></div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ title }}
            </h3>
            <div v-if="svg"
                class="w-8 h-8 rounded-lg flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
                :class="`text-${color}-500 dark:text-${color}-400`">
                <svg xmlns="http://www.w3.org/2000/svg" :viewBox="viewBox" fill="none" stroke="currentColor" 
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" v-html="svg">
                </svg>
            </div>
        </div>

        <!-- Value and Change -->
        <div class="space-y-1">
            <div class="flex items-baseline gap-3">
                <span class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ formatValue(value) }}
                </span>
                <div v-if="change" class="flex items-center text-sm font-medium" :class="{
                    'text-emerald-500': trend === 'up',
                    'text-rose-500': trend === 'down',
                    'text-gray-400': !trend
                }">
                    <!-- Trend Icon -->
                    <svg v-if="trend" class="w-4 h-4 mr-1" :class="{ 'rotate-180': trend === 'down' }"
                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4L20 12L18.6 13.4L13 7.8V20H11V7.8L5.4 13.4L4 12L12 4Z" fill="currentColor" />
                    </svg>
                    {{ formatChange(change) }}
                </div>
            </div>
        </div>
    </div>
</template>
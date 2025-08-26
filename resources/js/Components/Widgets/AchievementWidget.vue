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
    svg: {
        type: String,
        required: true
    },
    viewBox: {
        type: String,
        default: "0 0 16 16"
    },
    color: {
        type: String,
        default: "text-primary dark:text-primary-400"
    },
    gradient: {
        type: String,
        default: "from-blue-500 to-purple-600"
    },
    iconBg: {
        type: String,
        default: "bg-blue-50 dark:bg-blue-900/20"
    },
    iconBorder: {
        type: String,
        default: "border-blue-200 dark:border-blue-700/30"
    }
})
</script>

<template>
    <div
        class="group relative overflow-hidden bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100/50 dark:border-gray-700/50 p-4 hover:scale-[1.02] hover:-translate-y-1">
        <!-- Background gradient overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-gray-50/30 dark:to-gray-700/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        </div>

        <!-- Icon container with enhanced styling -->
        <div class="relative z-10 flex flex-col items-center">
            <div class="relative mb-3">
                <!-- Icon background with glow effect -->
                <div
                    class="absolute inset-0 rounded-lg bg-gradient-to-br from-white/80 to-gray-50/50 dark:from-gray-800/80 dark:to-gray-700/50 blur-lg opacity-60 group-hover:opacity-80 transition-opacity duration-300">
                </div>

                <!-- Icon container with border -->
                <div class="relative flex items-center justify-center w-12 h-12 rounded-lg shadow-sm border transition-all duration-300 group-hover:scale-105"
                    :class="[iconBg, iconBorder]">
                    <svg :viewBox="viewBox" xmlns="http://www.w3.org/2000/svg" fill="none"
                        class="w-6 h-6 transition-transform duration-300 group-hover:rotate-3" :class="color"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        v-html="svg">
                    </svg>
                </div>
            </div>

            <!-- Value display with enhanced typography -->
            <div class="text-center mb-2">
                <p
                    class="font-semibold text-2xl leading-none mb-2 bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    {{ value }}
                </p>
                <div
                    class="w-10 h-0.5 mx-auto rounded-full bg-gradient-to-r from-blue-500 to-purple-600 opacity-60 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </div>

            <!-- Title with improved styling -->
            <p class="font-medium text-sm leading-relaxed text-center text-gray-700 dark:text-gray-200 max-w-[140px]">
                {{ title }}
            </p>
        </div>

        <!-- Decorative corner elements -->
        <div
            class="absolute top-0 right-0 w-10 h-10 bg-gradient-to-bl from-transparent via-blue-500/10 to-purple-500/20 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500">
        </div>
        <div
            class="absolute bottom-0 left-0 w-8 h-8 bg-gradient-to-tr from-transparent via-purple-500/10 to-blue-500/20 rounded-tr-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
        </div>
    </div>
</template>
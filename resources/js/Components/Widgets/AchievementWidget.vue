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
    }
})
</script>

<template>
    <div
        class="flex flex-col justify-center items-center bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex flex-row justify-center items-center">
            <svg :viewBox="viewBox" xmlns="http://www.w3.org/2000/svg" fill="none"
                :class="['w-[35px] h-[35px]', color]"
                stroke="currentColor" 
                stroke-width="3.473" 
                stroke-linecap="round" 
                stroke-linejoin="round"
                v-html="svg">
            </svg>
            <p class="font-semibold text-2xl sm:text-3xl lg:text-4xl leading-9 ml-2" :class="color">
                {{ value }}
            </p>
        </div>
        <p class="font-medium text-sm leading-6 mt-3 md:mt-6 text-center text-gray-700 dark:text-gray-300">
            {{ title }}
        </p>
    </div>
</template>
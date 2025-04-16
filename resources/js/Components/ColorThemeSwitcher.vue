<script setup>
import { ref, onMounted } from 'vue'

const colors = [
    {
        name: 'Purple',
        value: 'purple',
        gradientFrom: '#a855f7',
        gradientTo: '#c084fc',
        primary: '#a855f7',
        hover: '#9333ea',
        rgb: '168, 85, 247'
    },
    {
        name: 'Blue',
        value: 'blue',
        gradientFrom: '#3b82f6',
        gradientTo: '#60a5fa',
        primary: '#3b82f6',
        hover: '#2563eb',
        rgb: '59, 130, 246'
    },
    {
        name: 'Green',
        value: 'green',
        gradientFrom: '#22c55e',
        gradientTo: '#4ade80',
        primary: '#22c55e',
        hover: '#16a34a',
        rgb: '34, 197, 94'
    },
    {
        name: 'Orange',
        value: 'orange',
        gradientFrom: '#f97316',
        gradientTo: '#fb923c',
        primary: '#f97316',
        hover: '#ea580c',
        rgb: '249, 115, 22'
    }
]

const selectedColor = ref(localStorage.getItem('theme-color') || 'purple')
const isOpen = ref(false)

const updateTheme = (color) => {
    selectedColor.value = color
    localStorage.setItem('theme-color', color)
    isOpen.value = false

    const root = document.documentElement
    const selectedColorObj = colors.find(c => c.value === color)

    root.style.setProperty('--primary-gradient-from', selectedColorObj.gradientFrom)
    root.style.setProperty('--primary-gradient-to', selectedColorObj.gradientTo)
    root.style.setProperty('--primary-color', selectedColorObj.primary)
    root.style.setProperty('--primary-hover', selectedColorObj.hover)
    root.style.setProperty('--primary-color-rgb', selectedColorObj.rgb)
}

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const closeDropdown = (event) => {
    if (!event.target.closest('.theme-dropdown')) {
        isOpen.value = false
    }
}

onMounted(() => {
    updateTheme(selectedColor.value)
    document.addEventListener('click', closeDropdown)
})
</script>


<template>
    <div class="relative theme-dropdown">
        <button @click="toggleDropdown"
            class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white cursor-pointer">
            <span>Theme</span>
            <svg class="w-3.5 h-3.5 ml-1 text-gray-500 dark:text-gray-400" :class="{ 'transform rotate-180': isOpen }"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <div v-if="isOpen"
            class="absolute right-0 mt-1 w-44 rounded-lg shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1 z-50">
            <button v-for="color in colors" :key="color.value" @click="updateTheme(color.value)"
                class="w-full flex items-center px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/70 cursor-pointer"
                :class="{ 'bg-gray-50 dark:bg-gray-700': selectedColor === color.value }">
                <div class="flex items-center flex-1 min-w-0">
                    <div class="w-4 h-4 rounded-full mr-2.5" :style="{
                        background: `linear-gradient(to right, ${color.gradientFrom}, ${color.gradientTo})`
                    }" />
                    <span>{{ color.name }}</span>
                </div>
                <svg v-if="selectedColor === color.value" 
                    class="w-4 h-4 shrink-0"
                    :style="{ color: color.primary }"
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                    stroke-width="1.5">
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" 
                    />
                </svg>
            </button>
        </div>
    </div>
</template>
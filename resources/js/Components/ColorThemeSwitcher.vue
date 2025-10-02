<script setup>
import { ref, onMounted } from 'vue'
import { colors, applyThemeColor } from '@/utils/themeInit'

const selectedColor = ref(localStorage.getItem('theme-color') || 'teal')
const isOpen = ref(false)

const updateTheme = color => {
    selectedColor.value = color
    localStorage.setItem('theme-color', color)
    isOpen.value = false
    applyThemeColor(color)
}

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const closeDropdown = event => {
    if (!event.target.closest('.theme-dropdown')) {
        isOpen.value = false
    }
}

onMounted(() => {
    const savedColor = localStorage.getItem('theme-color') || 'teal'
    updateTheme(savedColor)
    document.addEventListener('click', closeDropdown)
})
</script>

<template>
    <div class="relative theme-dropdown hidden lg:block">
        <button
            class="group flex items-center text-sm p-1.5 rounded-lg cursor-pointer text-[var(--color-text-muted)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-muted)]"
            @click="toggleDropdown">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-5">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />
            </svg>
            <span
                class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap"
                :style="{ backgroundColor: 'var(--color-text)', color: 'var(--color-bg)' }"
                >Theme color</span>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 mt-1 w-44 rounded-lg shadow-lg bg-[var(--color-surface)] border border-[var(--color-border)] py-1 z-50">
            <button
                v-for="color in colors"
                :key="color.value"
                class="w-full flex items-center px-3 py-1.5 text-sm text-[var(--color-text)] hover:bg-[var(--color-surface-muted)] cursor-pointer"
                :class="{
                    'bg-[var(--color-surface-muted)]': selectedColor === color.value
                }"
                @click="updateTheme(color.value)">
                <div class="flex items-center flex-1 min-w-0">
                    <div
                        class="w-4 h-4 rounded-full mr-2.5"
                        :style="{
                            background: `linear-gradient(to right, ${color.gradientFrom}, ${color.gradientTo})`
                        }" />
                    <span>{{ color.name }}</span>
                </div>
                <svg
                    v-if="selectedColor === color.value"
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
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </button>
        </div>
    </div>
</template>

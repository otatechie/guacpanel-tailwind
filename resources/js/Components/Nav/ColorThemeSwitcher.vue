<script setup>
import { ref, onMounted } from 'vue'
import { colors, applyThemeColor } from '@js/utils/themeInit'
import { PaintBrushIcon } from '@heroicons/vue/24/outline'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'

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
    <div class="theme-dropdown relative hidden lg:block">
        <button
            class="nav-bar-btn"
            aria-label="Change theme color"
            @click="toggleDropdown">
            <PaintBrushIcon class="nav-bar-icon" />
            <span class="nav-bar-tooltip">Theme</span>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 z-50 mt-1 w-40 rounded-lg border border-(--card-border) bg-(--color-surface) py-1 shadow-lg">
            <button
                v-for="color in colors"
                :key="color.value"
                class="flex w-full cursor-pointer items-center gap-2.5 px-3 py-1.5 text-sm text-(--color-text) transition-colors hover:bg-(--color-surface-muted)"
                :class="{ 'bg-(--color-surface-muted)': selectedColor === color.value }"
                @click="updateTheme(color.value)">
                <div
                    class="h-3.5 w-3.5 shrink-0 rounded-full"
                    :style="{ background: `linear-gradient(135deg, ${color.gradientFrom}, ${color.gradientTo})` }" />
                <span class="flex-1 text-left">{{ color.name }}</span>
                <CheckCircleIcon
                    v-if="selectedColor === color.value"
                    class="h-4 w-4 shrink-0"
                    :style="{ color: color.primary }" />
            </button>
        </div>
    </div>
</template>

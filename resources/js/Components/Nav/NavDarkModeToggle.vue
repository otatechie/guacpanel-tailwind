<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { cycleTheme, getCurrentThemeState } from '@js/utils/darkMode'
import { SunIcon, MoonIcon, ComputerDesktopIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    showLabelText: { type: Boolean, default: false },
})

const themeState = ref(getCurrentThemeState())

const label = computed(() => themeState.value.nextThemeText)
const currentIcon = computed(() => themeState.value.currentThemeIcon)
const currentLabel = computed(() => themeState.value.currentThemeText)

const iconComponent = computed(() => {
    const map = { sun: SunIcon, moon: MoonIcon, system: ComputerDesktopIcon }
    return map[currentIcon.value] || SunIcon
})

let observer = null

const updateThemeState = () => {
    themeState.value = getCurrentThemeState()
}

const switchMode = () => {
    cycleTheme()
    updateThemeState()
}

onMounted(() => {
    observer = new MutationObserver(mutations => {
        if (mutations.some(m => m.attributeName === 'class')) updateThemeState()
    })
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
    window.addEventListener('themeChanged', updateThemeState)
})

onBeforeUnmount(() => {
    if (observer) observer.disconnect()
    window.removeEventListener('themeChanged', updateThemeState)
})
</script>

<template>
    <div class="relative">
        <button
            class="nav-bar-btn"
            :aria-label="`Switch to ${label}`"
            @click="switchMode">
            <component :is="iconComponent" class="nav-bar-icon" />
            <span class="nav-bar-tooltip">{{ label }}</span>
            <span v-if="showLabelText" class="ml-2 text-sm text-muted-foreground">{{ currentLabel }}</span>
        </button>
    </div>
</template>

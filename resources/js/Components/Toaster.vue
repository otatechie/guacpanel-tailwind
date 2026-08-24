<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { Toaster as UiToaster } from '@/Components/ui/sonner'

// Contract in docs/ui-contract.md. Pages import this, never @/Components/ui/sonner.
defineProps({
    /** Distance from the viewport edge; clears the fixed header by default */
    offset: { type: String, default: '72px' },
})

// rich-colors keeps a separate light and dark palette, and Sonner picks between
// them from its own `theme` prop — it does not read the `dark` class. Without
// this, dark mode gets the light palette: a near-white toast on a dark page.
const theme = ref('light')
let observer

const syncTheme = () => {
    theme.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light'
}

onMounted(() => {
    syncTheme()
    observer = new MutationObserver(syncTheme)
    observer.observe(document.documentElement, { attributeFilter: ['class'] })
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
    <UiToaster
        position="top-right"
        :offset="offset"
        :duration="5000"
        :theme="theme"
        rich-colors
        close-button
        :visible-toasts="3" />
</template>

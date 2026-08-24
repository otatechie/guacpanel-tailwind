<script setup>
import { onMounted, ref } from 'vue'
import { SearchIcon } from '@lucide/vue'
import { useCommandPalette } from '@js/composables/useCommandPalette'

// A button wearing an input's clothes. It used to be a real search field with
// its own results dropdown, which meant the Cmd+K badge sat on a control the
// shortcut did not open — press it and a different overlay took over, searching
// a superset of the same data. One surface now, and the badge tells the truth.
const { open } = useCommandPalette()

// `isMac` resolved in onMounted, so every Mac user watched the badge say Ctrl+K
// and then swap. Hold the badge back for the one tick instead of showing the
// wrong key.
const platformKey = ref('')

onMounted(() => {
    platformKey.value = /Mac|iPod|iPhone|iPad/.test(navigator.userAgent) ? 'Cmd+K' : 'Ctrl+K'
})
</script>

<template>
    <button
        type="button"
        class="border-border bg-muted text-muted-foreground hover:border-muted-foreground/40 focus-visible:border-primary focus-visible:ring-primary/15 flex w-full items-center gap-2 rounded-lg border py-2 pr-2.5 pl-2.5 text-sm transition-colors focus-visible:ring-2 focus-visible:outline-none"
        @click="open">
        <SearchIcon class="h-4 w-4 shrink-0" aria-hidden="true" />
        <span class="flex-1 text-left">Search</span>
        <kbd
            v-if="platformKey"
            class="border-border bg-card text-muted-foreground rounded border px-1.5 py-0.5 font-mono text-[10px]">
            {{ platformKey }}
        </kbd>
    </button>
</template>

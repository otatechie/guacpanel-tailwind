<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
    tabs: {
        type: Array,
        required: true,
    },
    /** id of the element holding the tab content, for aria-controls */
    panelId: {
        type: String,
        default: 'tabpanel',
    },
})

const emit = defineEmits(['update:modelValue'])
const activeTab = ref(props.modelValue)
const tabRefs = ref([])

watch(
    () => props.modelValue,
    v => {
        activeTab.value = v
    }
)

const switchTab = index => {
    activeTab.value = index
    emit('update:modelValue', index)
}

/* A tablist is one stop in the tab order, not one stop per tab: the arrows move
   between tabs, Tab itself moves past the strip into the panel. */
const moveTo = index => {
    const next = (index + props.tabs.length) % props.tabs.length
    switchTab(next)
    tabRefs.value[next]?.focus()
}

const KEYS = {
    ArrowRight: i => i + 1,
    ArrowLeft: i => i - 1,
    Home: () => 0,
    End: () => props.tabs.length - 1,
}

const onKeydown = (event, index) => {
    const move = KEYS[event.key]
    if (!move) return

    event.preventDefault()
    moveTo(move(index))
}
</script>

<template>
    <div>
        <nav class="-mb-px flex gap-4 overflow-x-auto sm:gap-6" role="tablist" aria-label="Tabs">
            <button
                v-for="(tab, index) in tabs"
                :key="index"
                :ref="el => (tabRefs[index] = el)"
                type="button"
                role="tab"
                :id="activeTab === index ? `${panelId}-active-tab` : undefined"
                :aria-selected="activeTab === index"
                :aria-controls="panelId"
                :tabindex="activeTab === index ? 0 : -1"
                class="shrink-0 border-b-2 py-3 text-sm font-medium whitespace-nowrap transition-colors"
                :class="[
                    activeTab === index
                        ? 'border-primary text-foreground'
                        : 'text-muted-foreground hover:text-foreground border-transparent',
                ]"
                @keydown="onKeydown($event, index)"
                @click="switchTab(index)">
                {{ typeof tab === 'string' ? tab : tab.name }}
            </button>
        </nav>
        <slot />
    </div>
</template>

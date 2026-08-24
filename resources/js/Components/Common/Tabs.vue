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
})

const emit = defineEmits(['update:modelValue'])
const activeTab = ref(props.modelValue)

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
</script>

<template>
    <div>
        <nav class="-mb-px flex gap-4 overflow-x-auto sm:gap-6" aria-label="Tabs">
            <button
                v-for="(tab, index) in tabs"
                :key="index"
                type="button"
                class="shrink-0 cursor-pointer border-b-2 py-3 text-sm font-medium whitespace-nowrap transition-colors"
                :class="[
                    activeTab === index
                        ? 'border-primary text-foreground'
                        : 'text-muted-foreground hover:text-foreground border-transparent',
                ]"
                :aria-current="activeTab === index ? 'page' : undefined"
                @click="switchTab(index)">
                {{ typeof tab === 'string' ? tab : tab.name }}
            </button>
        </nav>
        <slot />
    </div>
</template>

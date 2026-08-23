<script setup>
import { computed } from 'vue'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
    type: {
        type: [String, null],
        default: null,
    },
})

const TYPE_VARIANT = {
    success: 'success',
    info: 'info',
    warning: 'warning',
    error: 'danger',
    danger: 'danger',
}

const normalized = computed(() =>
    String(props.type ?? '')
        .trim()
        .toLowerCase()
)

const label = computed(() => {
    const value = normalized.value
    if (!value) return 'Info'
    return value.charAt(0).toUpperCase() + value.slice(1)
})

const variant = computed(() => TYPE_VARIANT[normalized.value] ?? 'neutral')
</script>

<template>
    <Badge :variant="variant">
        {{ label }}
    </Badge>
</template>

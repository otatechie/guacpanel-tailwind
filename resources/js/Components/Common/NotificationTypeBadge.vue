<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: [String, null],
        default: null,
    },
})

const normalizeType = t =>
    String(t ?? '')
        .trim()
        .toLowerCase()

const typeLabel = t => {
    const v = normalizeType(t)
    if (!v) return 'Info'
    if (v === 'success') return 'Success'
    if (v === 'info') return 'Info'
    if (v === 'warning') return 'Warning'
    if (v === 'error') return 'Error'
    if (v === 'danger') return 'Danger'
    return v.charAt(0).toUpperCase() + v.slice(1)
}

const typeBadgeClass = t => {
    const v = normalizeType(t)
    if (v === 'success') return 'notification-badge-success'
    if (v === 'info') return 'notification-badge-info'
    if (v === 'warning') return 'notification-badge-warning'
    if (v === 'error') return 'notification-badge-error'
    if (v === 'danger') return 'notification-badge-danger'
    return 'notification-badge-default'
}

const label = computed(() => typeLabel(props.type))
const badgeClass = computed(() => typeBadgeClass(props.type))
</script>

<template>
    <span class="notification-badge" :class="badgeClass">
        {{ label }}
    </span>
</template>

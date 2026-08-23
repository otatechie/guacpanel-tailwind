<script setup>
import { computed } from 'vue'
import Badge from '@/Components/Badge.vue'

// Public API unchanged — see docs/ui-contract.md.
const props = defineProps({
    role: {
        type: Object,
        default: () => ({}),
    },
    roleClass: {
        type: String,
        default: null,
    },
})

const ROLE_VARIANT = {
    user: 'info',
    admin: 'warning',
    superuser: 'danger',
    superadmin: 'danger',
}

const name = computed(() => String(props.role?.name ?? ''))
const variant = computed(() => ROLE_VARIANT[name.value.toLowerCase()] ?? 'neutral')
// A role without a name is possible (the prop defaults to {}); don't crash on it.
const label = computed(() => (name.value ? name.value.charAt(0).toUpperCase() + name.value.slice(1) : ''))
</script>

<template>
    <Badge :variant="variant" :class="roleClass">
        {{ label }}
    </Badge>
</template>

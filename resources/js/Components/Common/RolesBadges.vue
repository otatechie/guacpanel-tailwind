<script setup>
import RoleBadge from '@js/Components/Common/RoleBadge.vue'

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <RoleBadge v-for="role in props.roles" :key="`role_${role.id ?? role.name}`" :role="role" />
</template>

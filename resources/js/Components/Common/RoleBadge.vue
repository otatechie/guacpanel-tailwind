<script setup>
import { capitalize } from '@vue/shared'

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

const badgeClass = name => {
  if (props.roleClass) return props.roleClass
  if (!name) return ''

  const role = String(name).toLowerCase()

  const roleMap = {
    user: 'badge-info',
    admin: 'badge-warning',
    superuser: 'badge-danger',
    superadmin: 'badge-danger',
  }

  return roleMap[role] ?? ''
}
</script>

<template>
  <span class="badge" :class="badgeClass(role.name)">
    {{ capitalize(role.name) }}
  </span>
</template>

<script setup>
import { Switch as UiSwitch } from '@/Components/ui/switch'

defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <UiSwitch
        :model-value="modelValue"
        :disabled="disabled"
        :aria-label="label"
        @update:model-value="emit('update:modelValue', $event === true)" />
</template>

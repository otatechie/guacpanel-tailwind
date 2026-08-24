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
    /** id of the text explaining this control, so it is announced with it */
    describedBy: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <span class="inline-flex items-center gap-2.5">
        <!-- Thumb position alone carried the state, and in dark mode the unchecked
             thumb is the brightest part of the control — brightness read as "on"
             when it meant the opposite. The word is the state; aria-hidden because
             role="switch" already announces checked to a screen reader. -->
        <span
            aria-hidden="true"
            class="w-6 text-right text-xs"
            :class="[
                modelValue ? 'text-foreground' : 'text-muted-foreground',
                disabled && 'opacity-50',
            ]">
            {{ modelValue ? 'On' : 'Off' }}
        </span>
        <UiSwitch
            :model-value="modelValue"
            :disabled="disabled"
            :aria-label="label"
            :aria-describedby="describedBy || undefined"
            class="dark:data-[state=unchecked]:*:data-[slot=switch-thumb]:bg-muted-foreground"
            @update:model-value="emit('update:modelValue', $event === true)" />
    </span>
</template>

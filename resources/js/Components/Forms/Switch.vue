<script setup>
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

defineEmits(['update:modelValue'])
</script>

<template>
    <label
        class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition-colors duration-150"
        :class="[
            disabled ? 'cursor-not-allowed opacity-50' : '',
            modelValue ? '' : 'hover:bg-(--color-border-strong)',
        ]"
        :style="{ backgroundColor: modelValue ? 'var(--primary-color)' : 'var(--card-border)' }">
        <input
            type="checkbox"
            :checked="modelValue"
            :disabled="disabled"
            :aria-checked="modelValue"
            :aria-label="label"
            role="switch"
            class="peer sr-only"
            @change="$emit('update:modelValue', $event.target.checked)" />
        <span
            class="inline-block h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-150"
            :class="modelValue ? 'translate-x-5.5' : 'translate-x-0.5'"
            aria-hidden="true" />
    </label>
</template>

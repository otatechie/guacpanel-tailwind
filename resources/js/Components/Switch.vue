<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    }
})

defineEmits(['update:modelValue'])
</script>


<template>
    <div>
        <div class="flex items-center gap-3">
            <label :class="[
                'relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full',
                disabled ? 'cursor-not-allowed opacity-50' : modelValue ? '' : 'hover:bg-gray-300',
            ]" :style="modelValue ? { backgroundColor: 'var(--selection-color)' } : { backgroundColor: 'rgb(229, 231, 235)' }">

                <input type="checkbox" :checked="modelValue" :disabled="disabled" :aria-checked="modelValue" :aria-label="label"
                    role="switch" @change="$emit('update:modelValue', $event.target.checked)" class="peer sr-only" />
                <span :class="[
                    'inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform duration-150 ease-in-out',
                    modelValue ? 'translate-x-[1.375rem]' : 'translate-x-0.5'
                ]" aria-hidden="true" />
            </label>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>
        </div>
        <p v-if="error" role="alert" class="mt-1 text-red-600 text-xs">
            {{ error }}
        </p>
    </div>
</template>

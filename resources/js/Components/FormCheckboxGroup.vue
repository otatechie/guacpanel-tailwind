<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    options: {
        type: Array,
        required: true
    },
    optionLabel: {
        type: String,
        default: 'name'
    },
    optionValue: {
        type: String,
        default: 'id'
    },
    optionDescription: {
        type: String,
        default: 'description'
    },
    label: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    help: {
        type: String,
        default: null
    },
    columns: {
        type: [String, Number],
        default: 'auto'
    },
    name: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const gridCols = computed(() => {
    if (props.columns === 'auto') {
        return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
    }
    return `grid-cols-${props.columns}`
})

function updateValue(optionValue, checked) {
    const currentValues = [...props.modelValue]

    if (checked) {
        if (!currentValues.includes(optionValue)) {
            currentValues.push(optionValue)
        }
    } else {
        const index = currentValues.indexOf(optionValue)
        if (index > -1) {
            currentValues.splice(index, 1)
        }
    }

    emit('update:modelValue', currentValues)
}

function isChecked(optionValue) {
    return props.modelValue.includes(optionValue)
}
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-[var(--color-text)] mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div v-if="help" class="text-sm text-[var(--color-text-muted)] mb-2">
            {{ help }}
        </div>

        <div :class="['grid gap-3 sm:gap-4', gridCols]">
            <div
                v-for="option in options"
                :key="option[optionValue]"
                class="p-3 sm:p-4 bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)]">
                <label
                    :for="`${name}-${option[optionValue]}`"
                    class="flex items-start cursor-pointer">
                    <div class="flex items-center h-5">
                        <div class="group grid size-4 grid-cols-1">
                            <input
                                :id="`${name}-${option[optionValue]}`"
                                :checked="isChecked(option[optionValue])"
                                type="checkbox"
                                :disabled="disabled"
                                :aria-invalid="!!error"
                                :aria-describedby="error ? `${name}-error` : undefined"
                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto cursor-pointer disabled:cursor-not-allowed"
                                @change="updateValue(option[optionValue], $event.target.checked)" />
                            <svg
                                viewBox="0 0 14 14"
                                fill="none"
                                class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                                <path
                                    d="M3 8L6 11L11 3.5"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="opacity-0 group-has-checked:opacity-100" />
                                <path
                                    d="M3 7H11"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="opacity-0 group-has-indeterminate:opacity-100" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-[var(--color-text)]">
                            {{ option[optionLabel] }}
                        </span>
                        <p
                            v-if="option[optionDescription]"
                            class="mt-1 text-[var(--color-text-muted)] text-xs">
                            {{ option[optionDescription] }}
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <p
            v-if="!options?.length"
            class="text-sm text-[var(--color-text-muted)] text-center py-2">
            No options available
        </p>

        <p
            v-if="error"
            :id="`${name}-error`"
            role="alert"
            class="mt-1 text-red-600 dark:text-red-400 text-xs">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        required: true
    },
    id: {
        type: String,
        default: null
    },
    required: {
        type: Boolean,
        default: false
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
    }
})

const emit = defineEmits(['update:modelValue'])

const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

function updateValue(event) {
    emit('update:modelValue', event.target.checked)
}
</script>

<template>
    <div>
        <label :for="inputId" class="flex items-start cursor-pointer">
            <div class="flex items-center h-5">
                <div class="group grid size-4 grid-cols-1">
                    <input
                        :id="inputId"
                        type="checkbox"
                        :checked="modelValue"
                        :required="required"
                        :disabled="disabled"
                        :aria-invalid="!!error"
                        :aria-describedby="error ? `${inputId}-error` : undefined"
                        class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto cursor-pointer disabled:cursor-not-allowed"
                        @change="updateValue" />
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
                    {{ label }}{{ required ? ' *' : '' }}
                </span>
                <p v-if="help && !error" class="mt-1 text-[var(--color-text-muted)] text-xs">
                    {{ help }}
                </p>
            </div>
        </label>

        <p
            v-if="error"
            :id="`${inputId}-error`"
            role="alert"
            class="mt-1 text-red-600 dark:text-red-400 text-xs">
            {{ error }}
        </p>
    </div>
</template>

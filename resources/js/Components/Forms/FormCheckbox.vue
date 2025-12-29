<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        required: true,
    },
    id: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    help: {
        type: String,
        default: null,
    },
})

const emit = defineEmits(['update:modelValue'])

const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

function updateValue(event) {
    emit('update:modelValue', event.target.checked)
}
</script>

<template>
    <div>
        <label :for="inputId" class="flex cursor-pointer items-start">
            <div class="flex h-5 items-center">
                <div class="group grid size-4 grid-cols-1">
                    <input
                        :id="inputId"
                        type="checkbox"
                        :checked="modelValue"
                        :required="required"
                        :disabled="disabled"
                        :aria-invalid="!!error"
                        :aria-describedby="error ? `${inputId}-error` : undefined"
                        class="col-start-1 row-start-1 cursor-pointer appearance-none rounded-sm border border-gray-300 bg-white checked:border-[var(--primary-color)] checked:bg-[var(--primary-color)] indeterminate:border-[var(--primary-color)] indeterminate:bg-[var(--primary-color)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary-color)] disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-[var(--primary-color)] dark:checked:bg-[var(--primary-color)] dark:indeterminate:border-[var(--primary-color)] dark:indeterminate:bg-[var(--primary-color)] dark:focus-visible:outline-[var(--primary-color)] dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
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
                <p v-if="help && !error" class="mt-1 text-xs text-[var(--color-text-muted)]">
                    {{ help }}
                </p>
            </div>
        </label>

        <p
            v-if="error"
            :id="`${inputId}-error`"
            role="alert"
            class="mt-1 text-xs text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>

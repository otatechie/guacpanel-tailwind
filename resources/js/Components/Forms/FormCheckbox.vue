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
        <label :for="inputId" class="flex cursor-pointer items-start gap-3">
            <div class="flex h-5 items-center">
                <div class="group grid size-4 grid-cols-1">
                    <input
                        :id="inputId"
                        type="checkbox"
                        :checked="modelValue"
                        :required="required"
                        :disabled="disabled"
                        :aria-invalid="!!error"
                        :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
                        class="col-start-1 row-start-1 cursor-pointer appearance-none rounded-sm border transition-colors
                            border-(--card-border) bg-(--color-surface)
                            checked:border-(--primary-color) checked:bg-(--primary-color)
                            focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-(--primary-color)
                            disabled:cursor-not-allowed disabled:opacity-50"
                        @change="updateValue" />
                    <svg
                        viewBox="0 0 14 14"
                        fill="none"
                        class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-white/50">
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
            <div class="text-sm">
                <span class="font-medium text-(--color-text)">
                    {{ label }}{{ required ? ' *' : '' }}
                </span>
                <p v-if="help && !error" :id="`${inputId}-help`" class="mt-0.5 text-xs text-(--color-text-muted)">
                    {{ help }}
                </p>
            </div>
        </label>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-red-600">
            {{ error }}
        </p>
    </div>
</template>

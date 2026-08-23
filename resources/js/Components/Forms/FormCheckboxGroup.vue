<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        required: true,
    },
    optionLabel: {
        type: String,
        default: 'name',
    },
    optionValue: {
        type: String,
        default: 'id',
    },
    optionDescription: {
        type: String,
        default: 'description',
    },
    label: {
        type: String,
        default: '',
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
    columns: {
        type: [String, Number],
        default: 'auto',
    },
    name: {
        type: String,
        default: '',
    },
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
        if (!currentValues.includes(optionValue)) currentValues.push(optionValue)
    } else {
        const index = currentValues.indexOf(optionValue)
        if (index > -1) currentValues.splice(index, 1)
    }
    emit('update:modelValue', currentValues)
}

function isChecked(optionValue) {
    return props.modelValue.includes(optionValue)
}
</script>

<template>
    <div>
        <label v-if="label" class="mb-2 block text-sm font-medium text-foreground">
            {{ label }}
        </label>

        <p v-if="help" class="mb-2 text-sm text-muted-foreground">
            {{ help }}
        </p>

        <div :class="['grid gap-3', gridCols]">
            <div
                v-for="option in options"
                :key="option[optionValue]"
                class="rounded-lg border border-border bg-card p-3">
                <label
                    :for="`${name}-${option[optionValue]}`"
                    class="flex cursor-pointer items-start gap-3">
                    <div class="flex h-5 items-center">
                        <div class="group grid size-4 grid-cols-1">
                            <input
                                :id="`${name}-${option[optionValue]}`"
                                :checked="isChecked(option[optionValue])"
                                type="checkbox"
                                :disabled="disabled"
                                :aria-invalid="!!error"
                                :aria-describedby="error ? `${name}-error` : undefined"
                                class="col-start-1 row-start-1 cursor-pointer appearance-none rounded-sm border transition-colors
                                    border-border bg-card
                                    checked:border-primary checked:bg-primary
                                    focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
                                    disabled:cursor-not-allowed disabled:opacity-50"
                                @change="updateValue(option[optionValue], $event.target.checked)" />
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
                        <span class="font-medium text-foreground">
                            {{ option[optionLabel] }}
                        </span>
                        <p v-if="option[optionDescription]" class="mt-0.5 text-xs text-muted-foreground">
                            {{ option[optionDescription] }}
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <p v-if="!options?.length" class="py-2 text-center text-sm text-muted-foreground">
            No options available
        </p>

        <p v-if="error" :id="`${name}-error`" role="alert" class="mt-1.5 text-xs text-red-600">
            {{ error }}
        </p>
    </div>
</template>

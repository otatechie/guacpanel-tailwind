<script setup>
import { computed } from 'vue'
import { Checkbox } from '@/Components/ui/checkbox'
import { Label } from '@/Components/ui/label'

// Public API unchanged — see docs/ui-contract.md. Internals sit on shadcn Checkbox.
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

// reka-ui can emit `indeterminate`; the app contract is strictly boolean.
function onUpdate(value) {
    emit('update:modelValue', value === true)
}
</script>

<template>
    <div>
        <div class="flex items-start gap-3">
            <Checkbox
                :id="inputId"
                :model-value="modelValue"
                :required="required"
                :disabled="disabled"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
                class="mt-0.5"
                @update:model-value="onUpdate" />

            <div class="text-sm">
                <Label :for="inputId" class="cursor-pointer font-medium text-foreground">
                    {{ label }}{{ required ? ' *' : '' }}
                </Label>
                <p v-if="help && !error" :id="`${inputId}-help`" class="mt-0.5 text-xs text-muted-foreground">
                    {{ help }}
                </p>
            </div>
        </div>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-destructive">
            {{ error }}
        </p>
    </div>
</template>

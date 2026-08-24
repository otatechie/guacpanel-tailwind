<script setup>
import { computed } from 'vue'
import { Textarea } from '@/Components/ui/textarea'
import { Label } from '@/Components/ui/label'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
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
    placeholder: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    help: {
        type: String,
        default: null,
    },
    rows: {
        type: Number,
        default: 3,
    },
})

const emit = defineEmits(['update:modelValue'])

const inputPlaceholder = computed(() => props.placeholder || props.label)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))
</script>

<template>
    <div>
        <Label :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-destructive">*</span>
        </Label>

        <Textarea
            :id="inputId"
            :model-value="modelValue"
            :required="required"
            :disabled="disabled"
            :rows="rows"
            class="resize-y"
            :placeholder="inputPlaceholder"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
            @update:model-value="emit('update:modelValue', $event)" />

        <p
            v-if="error"
            :id="`${inputId}-error`"
            role="alert"
            class="text-destructive mt-1.5 text-xs">
            {{ error }}
        </p>
        <p
            v-if="help && !error"
            :id="`${inputId}-help`"
            class="text-muted-foreground mt-1.5 text-xs">
            {{ help }}
        </p>
    </div>
</template>

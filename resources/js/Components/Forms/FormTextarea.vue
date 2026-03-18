<script setup>
import { computed } from 'vue'

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

function updateValue(event) {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div>
        <label :for="inputId" class="form-label">
            {{ label }}<span v-if="required" class="text-red-500"> *</span>
        </label>

        <textarea
            :id="inputId"
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            :rows="rows"
            class="form-input resize-y"
            :class="{
                'form-input-error': error,
                'form-input-disabled': disabled,
            }"
            :placeholder="inputPlaceholder"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
            @input="updateValue" />

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-red-600">
            {{ error }}
        </p>
        <p v-if="help && !error" :id="`${inputId}-help`" class="mt-1.5 text-xs text-(--color-text-muted)">
            {{ help }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
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
    placeholder: {
        type: String,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    help: {
        type: String,
        default: null
    },
    rows: {
        type: Number,
        default: 3
    }
})

const emit = defineEmits(['update:modelValue'])

const inputPlaceholder = computed(() => props.placeholder || props.label)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const textareaClass = computed(
    () =>
        `w-full peer border rounded-md text-sm
    ${
        props.disabled
            ? 'cursor-not-allowed text-[var(--color-text-muted)]'
            : 'bg-[var(--color-surface)] text-[var(--color-text)] caret-[var(--color-text)]'
    }
    border-[var(--color-border-strong)] placeholder-transparent px-3 py-2
    ${props.error ? 'error' : ''}
    dark:[color-scheme:dark]`
)

function updateValue(event) {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div>
        <label :for="inputId" class="relative block">
            <textarea
                :id="inputId"
                :value="modelValue"
                :required="required"
                :disabled="disabled"
                :rows="rows"
                :class="textareaClass"
                :placeholder="inputPlaceholder"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${inputId}-error` : undefined"
                @input="updateValue" />

            <span
                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-[var(--color-surface)] px-1 text-xs font-medium text-[var(--color-text-muted)] transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1 text-red-600 text-xs">
            {{ error }}
        </p>
        <p v-if="help && !error" class="mt-1 text-[var(--color-text-muted)] text-xs">
            {{ help }}
        </p>
    </div>
</template>

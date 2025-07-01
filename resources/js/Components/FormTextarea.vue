<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
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
    rows: {
        type: Number,
        default: 4
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
    }
})

const emit = defineEmits(['update:modelValue'])

const inputPlaceholder = computed(() => props.placeholder || props.label)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const textareaClass = computed(() =>
    `w-full peer border rounded-md resize-y
    ${props.disabled
        ? 'cursor-not-allowed !bg-gray-100 dark:!bg-gray-700 text-gray-500 dark:text-gray-400'
        : 'bg-white dark:bg-gray-800 dark:text-white'}
    border-gray-300 dark:border-gray-600 placeholder-transparent px-3 py-2
    ${props.error ? 'error' : ''}`
)

function updateValue(event) {
    emit('update:modelValue', event.target.value)
}
</script>


<template>
    <div>
        <label :for="inputId" class="relative block">
            <textarea :id="inputId" :value="modelValue" :rows="rows" :required="required" :disabled="disabled"
                @input="updateValue" :class="textareaClass" :placeholder="inputPlaceholder" :aria-invalid="!!error"
                :aria-describedby="error ? `${inputId}-error` : undefined"></textarea>

            <span
                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 px-1 text-xs text-gray-700 dark:text-gray-400 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1 text-red-600 text-xs">
            {{ error }}
        </p>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

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
    type: {
        type: String,
        default: 'text',
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
})

const emit = defineEmits(['update:modelValue'])
const showPassword = ref(false)

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

        <div class="relative">
            <input
                :id="inputId"
                :type="showPassword ? 'text' : type"
                :value="modelValue"
                :required="required"
                :disabled="disabled"
                class="form-input"
                :class="{
                    'form-input-error': error,
                    'form-input-disabled': disabled,
                }"
                :placeholder="inputPlaceholder"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
                @input="updateValue" />

            <button
                v-if="type === 'password'"
                type="button"
                class="absolute inset-y-0 right-0 flex min-w-[44px] cursor-pointer items-center justify-center px-3 text-(--color-text-muted) transition-colors hover:text-(--color-text)"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showPassword"
                @click="showPassword = !showPassword">
                <EyeSlashIcon v-if="showPassword" class="h-4 w-4" aria-hidden="true" />
                <EyeIcon v-else class="h-4 w-4" aria-hidden="true" />
            </button>
        </div>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-red-600">
            {{ error }}
        </p>
        <p v-if="help && !error" :id="`${inputId}-help`" class="mt-1.5 text-xs text-(--color-text-muted)">
            {{ help }}
        </p>
    </div>
</template>

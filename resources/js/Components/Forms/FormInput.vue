<script setup>
import { ref, computed } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'

// Public API unchanged — see docs/ui-contract.md. Internals sit on shadcn Input.
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
</script>

<template>
    <div>
        <Label :for="inputId" class="form-label">
            {{ label }}<span v-if="required" class="text-destructive"> *</span>
        </Label>

        <div class="relative">
            <Input
                :id="inputId"
                :type="showPassword ? 'text' : type"
                :model-value="modelValue"
                :required="required"
                :disabled="disabled"
                :placeholder="inputPlaceholder"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${inputId}-error` : help ? `${inputId}-help` : undefined"
                :class="type === 'password' ? 'pr-11' : undefined"
                @update:model-value="emit('update:modelValue', $event)" />

            <button
                v-if="type === 'password'"
                type="button"
                class="absolute inset-y-0 right-0 flex min-w-[44px] cursor-pointer items-center justify-center px-3 text-muted-foreground transition-colors hover:text-foreground"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showPassword"
                @click="showPassword = !showPassword">
                <EyeSlashIcon v-if="showPassword" class="h-4 w-4" aria-hidden="true" />
                <EyeIcon v-else class="h-4 w-4" aria-hidden="true" />
            </button>
        </div>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-destructive">
            {{ error }}
        </p>
        <p v-if="help && !error" :id="`${inputId}-help`" class="mt-1.5 text-xs text-muted-foreground">
            {{ help }}
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { EyeIcon, EyeOffIcon } from '@lucide/vue'
import { Input } from '@/Components/ui/input'
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
    /**
     * Render the `*` next to the label. Turn off where every field in the form
     * is required and the marker carries no information (e.g. the auth pages).
     */
    showRequiredMarker: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['update:modelValue'])
const showPassword = ref(false)

// placeholder="" means no placeholder; omitting it falls back to the label.
const inputPlaceholder = computed(() =>
    props.placeholder === null ? props.label : props.placeholder || undefined
)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))
</script>

<template>
    <div>
        <Label :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required && showRequiredMarker" class="text-destructive">*</span>
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
                :aria-describedby="
                    error ? `${inputId}-error` : help ? `${inputId}-help` : undefined
                "
                :class="['h-8', type === 'password' ? 'pr-11' : '']"
                @update:model-value="emit('update:modelValue', $event)" />

            <button
                v-if="type === 'password'"
                type="button"
                class="text-muted-foreground hover:text-foreground absolute inset-y-0 right-0 flex min-w-[44px] cursor-pointer items-center justify-center px-3 transition-colors"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showPassword"
                @click="showPassword = !showPassword">
                <EyeOffIcon v-if="showPassword" class="h-4 w-4" aria-hidden="true" />
                <EyeIcon v-else class="h-4 w-4" aria-hidden="true" />
            </button>
        </div>

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

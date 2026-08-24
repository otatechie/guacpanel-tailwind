<script setup>
import { computed } from 'vue'
import { CheckIcon, MinusIcon } from '@lucide/vue'
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
    /**
     * Tri-state for a checkbox that governs others: some, but not all, of the
     * set below it is checked. Wins over `modelValue` while it is on.
     */
    indeterminate: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

// reka-ui reads and emits `indeterminate`; the app contract stays boolean, so
// the mixed state goes in as a prop and comes back out as a plain true/false.
const state = computed(() => (props.indeterminate ? 'indeterminate' : props.modelValue))

function onUpdate(value) {
    emit('update:modelValue', value === true)
}
</script>

<template>
    <div>
        <div class="flex gap-3" :class="help ? 'items-start' : 'items-center'">
            <Checkbox
                :id="inputId"
                :model-value="state"
                :required="required"
                :disabled="disabled"
                :aria-invalid="!!error"
                :aria-describedby="
                    error ? `${inputId}-error` : help ? `${inputId}-help` : undefined
                "
                :class="help ? 'mt-0.5' : undefined"
                @update:model-value="onUpdate">
                <MinusIcon v-if="indeterminate" />
                <CheckIcon v-else />
            </Checkbox>

            <!-- flex-1 + a block label so the click target is the whole row, not
                 just the width of the words. Rows of ragged-right targets are a
                 slog once a list runs past a handful of items. -->
            <div class="min-w-0 flex-1 text-sm">
                <Label :for="inputId" class="text-foreground block cursor-pointer font-normal">
                    {{ label }}{{ required ? ' *' : '' }}
                </Label>
                <p
                    v-if="help && !error"
                    :id="`${inputId}-help`"
                    class="text-muted-foreground mt-0.5 text-xs">
                    {{ help }}
                </p>
            </div>
        </div>

        <p
            v-if="error"
            :id="`${inputId}-error`"
            role="alert"
            class="text-destructive mt-1.5 text-xs">
            {{ error }}
        </p>
    </div>
</template>

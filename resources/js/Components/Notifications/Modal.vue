<script setup>
import { computed } from 'vue'
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog'
import { cn } from '@/lib/utils'

// Public API unchanged (show / @close / size / closeOnClickOutside) — see
// docs/ui-contract.md. Focus trap, escape handling, scroll lock and aria wiring
// now come from reka-ui instead of being hand-rolled here.
const props = defineProps({
    show: Boolean,
    size: { type: String, default: 'md' },
    closeOnClickOutside: { type: Boolean, default: true },
})

const emit = defineEmits(['close'])

const SIZE_CLASS = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
}

const contentClass = computed(() =>
    cn('max-h-[calc(100vh-2rem)] gap-0 p-0', SIZE_CLASS[props.size] ?? SIZE_CLASS.md)
)

function onOpenChange(open) {
    if (!open) emit('close')
}

function guardOutside(event) {
    if (!props.closeOnClickOutside) event.preventDefault()
}
</script>

<template>
    <Dialog :open="show" @update:open="onOpenChange">
        <DialogContent
            :class="contentClass"
            @pointer-down-outside="guardOutside"
            @interact-outside="guardOutside">
            <DialogHeader class="shrink-0 border-b border-border px-5 py-3.5">
                <DialogTitle class="text-sm font-semibold text-foreground">
                    <slot name="title" />
                </DialogTitle>
            </DialogHeader>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                <slot />
            </div>

            <DialogFooter v-if="$slots.footer" class="shrink-0 border-t border-border px-5 py-3.5">
                <slot name="footer" />
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { computed, useId } from 'vue'
import { XIcon } from '@lucide/vue'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'
import { cn } from '@/lib/utils'

// Focus trap, escape, scroll lock and aria wiring come from reka-ui.
const props = defineProps({
    show: Boolean,
    size: { type: String, default: 'md' },
    closeOnClickOutside: { type: Boolean, default: true },
    /**
     * Optional one-line subtitle under the title. The dialog's accessible
     * description is the body, not this — so a modal that puts its substance in
     * the body (the usual case) simply leaves this out.
     */
    description: { type: String, default: '' },
})

/* aria-describedby points at the body. Left to itself reka-ui aims it at a
   DialogDescription id, which warns and resolves to nothing whenever a modal has
   no subtitle — and the body was always the real description anyway. */
const bodyId = useId()

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
        <!-- shadcn floats its own close at `absolute top-4 right-4`, which knows
             nothing about this header's px-5 py-3.5: the X sat 8px below the
             title's centre and 4px inside its right edge. Ours lives in the
             header row, so it lines up by construction. -->
        <DialogContent
            :class="contentClass"
            :aria-describedby="bodyId"
            :show-close-button="false"
            @pointer-down-outside="guardOutside"
            @interact-outside="guardOutside">
            <DialogHeader
                class="border-border shrink-0 flex-row items-start justify-between gap-4 border-b px-5 py-3.5">
                <div class="min-w-0">
                    <DialogTitle class="text-foreground text-sm font-semibold">
                        <slot name="title" />
                    </DialogTitle>
                    <!-- Was text-xs under a text-sm title, so the subtitle read as
                         fine print while the body it explained was larger. One
                         scale; weight and colour carry the hierarchy. -->
                    <DialogDescription v-if="description" class="mt-1 text-sm">
                        {{ description }}
                    </DialogDescription>
                </div>

                <!-- h-5 matches the title's line box, so `items-start` centres the
                     two on each other. The inset pseudo-element restores a
                     comfortable hit area without changing the layout. -->
                <DialogClose
                    class="text-muted-foreground hover:text-foreground focus-visible:outline-ring relative flex h-5 w-5 shrink-0 cursor-pointer items-center justify-center rounded-sm transition-colors after:absolute after:-inset-2 focus-visible:outline-2">
                    <XIcon class="h-4 w-4" aria-hidden="true" />
                    <span class="sr-only">Close</span>
                </DialogClose>
            </DialogHeader>

            <div :id="bodyId" class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                <slot />
            </div>

            <DialogFooter v-if="$slots.footer" class="border-border shrink-0 border-t px-5 py-3.5">
                <slot name="footer" />
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

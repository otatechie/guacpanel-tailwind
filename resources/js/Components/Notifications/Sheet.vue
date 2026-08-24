<script setup>
import { computed, useId } from 'vue'
import { XIcon } from '@lucide/vue'
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/Components/ui/sheet'
import { cn } from '@/lib/utils'

// Same API as Modal — focus trap, escape, scroll lock and aria wiring come from
// reka-ui. Reach for this over Modal when the content is a form or a long list:
// a panel gives vertical room and leaves the record you came from on screen.
const props = defineProps({
    show: Boolean,
    /** right | left */
    side: { type: String, default: 'right' },
    size: { type: String, default: 'md' },
    closeOnClickOutside: { type: Boolean, default: true },
    /**
     * Optional one-line subtitle under the title. The panel's accessible
     * description is the body, not this.
     */
    description: { type: String, default: '' },
})

const emit = defineEmits(['close'])

/* Written out rather than interpolated: Tailwind only sees class names that
   appear literally in the source. The built-in width uses the same data-side
   variants, so these have to match it to win the merge. */
const SIZE_CLASS = {
    sm: 'data-[side=left]:sm:max-w-sm data-[side=right]:sm:max-w-sm',
    md: 'data-[side=left]:sm:max-w-md data-[side=right]:sm:max-w-md',
    lg: 'data-[side=left]:sm:max-w-lg data-[side=right]:sm:max-w-lg',
    xl: 'data-[side=left]:sm:max-w-xl data-[side=right]:sm:max-w-xl',
}

const contentClass = computed(() =>
    cn(
        'gap-0 p-0',
        // shadcn leaves a sheet at 75% width on phones, which strands a form
        // against the edge of the screen.
        'data-[side=left]:w-full data-[side=right]:w-full',
        SIZE_CLASS[props.size] ?? SIZE_CLASS.md
    )
)

const bodyId = useId()

function onOpenChange(open) {
    if (!open) emit('close')
}

function guardOutside(event) {
    if (!props.closeOnClickOutside) event.preventDefault()
}
</script>

<template>
    <Sheet :open="show" @update:open="onOpenChange">
        <!-- shadcn floats its close at `absolute top-4 right-4`, which knows
             nothing about this header's padding. Ours sits in the header row. -->
        <SheetContent
            :side="side"
            :class="contentClass"
            :aria-describedby="bodyId"
            :show-close-button="false"
            @pointer-down-outside="guardOutside"
            @interact-outside="guardOutside">
            <SheetHeader
                class="border-border shrink-0 flex-row items-start justify-between gap-4 border-b px-5 py-3.5">
                <div class="min-w-0">
                    <!-- A step above the body: at text-sm the title matched the
                         text inside the input fields, which is thin for a surface
                         this size. Still below the page h1, so a sheet never
                         competes with the page behind it. -->
                    <SheetTitle class="text-foreground text-base font-semibold">
                        <slot name="title" />
                    </SheetTitle>
                    <SheetDescription v-if="description" class="mt-1 text-sm">
                        {{ description }}
                    </SheetDescription>
                </div>

                <!-- h-6 matches the title's line box (text-base = 24px) so
                     `items-start` centres the two; the inset pseudo-element
                     restores the hit area. Resize this with the title. -->
                <SheetClose
                    class="text-muted-foreground hover:text-foreground focus-visible:outline-ring relative flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-sm transition-colors after:absolute after:-inset-2 focus-visible:outline-2">
                    <XIcon class="h-4 w-4" aria-hidden="true" />
                    <span class="sr-only">Close</span>
                </SheetClose>
            </SheetHeader>

            <div :id="bodyId" class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                <slot />
            </div>

            <SheetFooter
                v-if="$slots.footer"
                class="border-border shrink-0 flex-row border-t px-5 py-3.5">
                <slot name="footer" />
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

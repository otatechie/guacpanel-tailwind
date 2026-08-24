<script setup>
import { computed } from 'vue'
import {
    Popover as UiPopover,
    PopoverContent as UiPopoverContent,
    PopoverTrigger as UiPopoverTrigger,
} from '@/Components/ui/popover'
import { cn } from '@/lib/utils'

// Contract in docs/ui-contract.md. Pages import this, never @/Components/ui/popover.
const props = defineProps({
    /** start | center | end — the trigger edge the panel lines up with */
    align: { type: String, default: 'end' },
    /** Panel width utility */
    width: { type: String, default: 'w-72' },
    /**
     * Optional v-model:open. Left undefined the panel manages itself; bind it
     * when something inside has to close the panel, such as a link that
     * navigates without unmounting the trigger.
     * `type: null` rather than Boolean so an unbound panel stays uncontrolled
     * instead of being cast to a permanently-closed `false`.
     */
    open: { type: null, default: undefined },
    class: { type: null, default: undefined },
})

defineEmits(['update:open'])

/* shadcn's content is a padded, gapped flex column sized for prose. A panel of
   rows wants none of that, so the wrapper strips it and the caller's sections
   own their spacing. */
const classes = computed(() => cn('block gap-0 p-0', props.width, props.class))
</script>

<template>
    <UiPopover :open="open" @update:open="$emit('update:open', $event)">
        <UiPopoverTrigger as-child>
            <slot name="trigger" />
        </UiPopoverTrigger>

        <UiPopoverContent :align="align" :class="classes">
            <slot />
        </UiPopoverContent>
    </UiPopover>
</template>

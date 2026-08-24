<script setup>
import { computed } from 'vue'
import {
    DropdownMenu as UiDropdownMenu,
    DropdownMenuContent as UiDropdownMenuContent,
    DropdownMenuTrigger as UiDropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { cn } from '@/lib/utils'

// Contract in docs/ui-contract.md. Pages import this, never @/Components/ui/dropdown-menu.
const props = defineProps({
    /** start | center | end — the trigger edge the panel lines up with */
    align: { type: String, default: 'end' },
    /** Panel width utility */
    width: { type: String, default: 'w-56' },
    /**
     * Optional v-model:open. Left undefined the panel manages itself; bind it
     * when something inside has to close the panel, such as a link that
     * navigates without unmounting the trigger.
     * `type: null` rather than Boolean so an unbound panel stays uncontrolled
     * instead of being cast to a permanently-closed `false`.
     */
    open: { type: null, default: undefined },
    /**
     * Modal menus prevent Tab and make the rest of the page inert, which suits
     * a list of commands and nothing else. Panels that hold their own controls
     * — a dismiss button per row, a "Read all" action — need those controls in
     * normal tab order, so the wrapper defaults to non-modal.
     */
    modal: { type: Boolean, default: false },
    class: { type: null, default: undefined },
})

defineEmits(['update:open'])

/* shadcn sizes the panel to the trigger and pads it for single-line items.
   A panel of rows wants neither, so the wrapper strips both and the caller's
   sections own their spacing. */
const classes = computed(() => cn('p-0', props.width, props.class))
</script>

<template>
    <UiDropdownMenu :open="open" :modal="modal" @update:open="$emit('update:open', $event)">
        <UiDropdownMenuTrigger as-child>
            <slot name="trigger" />
        </UiDropdownMenuTrigger>

        <UiDropdownMenuContent :align="align" :class="classes">
            <slot />
        </UiDropdownMenuContent>
    </UiDropdownMenu>
</template>

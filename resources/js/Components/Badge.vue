<script setup>
import { computed } from 'vue'
import { Badge as UiBadge } from '@/Components/ui/badge'
import { cn } from '@/lib/utils'

const props = defineProps({
    /** neutral | primary | info | success | warning | danger */
    variant: { type: String, default: 'neutral' },
    /** Renders a leading status dot and drops the tint, for inline status labels */
    dot: { type: Boolean, default: false },
    class: { type: null, default: undefined },
})

const VARIANT_CLASS = {
    neutral: 'bg-muted text-muted-foreground',
    primary: 'bg-primary/10 text-primary dark:bg-primary/20',
    info: 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
    success: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    warning: 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
    danger: 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300',
}

// The dot form is the same semantics without the fill: a coloured marker and
// coloured text, for status shown inline in a table row rather than as a chip.
const DOT_TEXT = {
    neutral: 'text-muted-foreground',
    primary: 'text-primary',
    info: 'text-blue-600 dark:text-blue-400',
    success: 'text-green-600 dark:text-green-400',
    warning: 'text-amber-600 dark:text-amber-400',
    danger: 'text-red-600 dark:text-red-400',
}

const DOT_BG = {
    neutral: 'bg-muted-foreground',
    primary: 'bg-primary',
    info: 'bg-blue-500',
    success: 'bg-green-500',
    warning: 'bg-amber-500',
    danger: 'bg-red-500',
}

const variant = computed(() => (props.variant in VARIANT_CLASS ? props.variant : 'neutral'))

const classes = computed(() =>
    props.dot
        ? cn('border-0 bg-transparent px-0 font-normal', DOT_TEXT[variant.value], props.class)
        : cn(VARIANT_CLASS[variant.value], props.class)
)

const dotClass = computed(() => cn('h-1.5 w-1.5 shrink-0 rounded-full', DOT_BG[variant.value]))
</script>

<template>
    <UiBadge variant="secondary" :class="classes">
        <span v-if="dot" :class="dotClass" aria-hidden="true"></span>
        <slot />
    </UiBadge>
</template>

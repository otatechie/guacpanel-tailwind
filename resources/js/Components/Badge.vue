<script setup>
import { computed } from 'vue'
import { Badge as UiBadge } from '@/Components/ui/badge'
import { cn } from '@/lib/utils'

// GuacPanel badge contract — see docs/ui-contract.md.
const props = defineProps({
    /** neutral | primary | info | success | warning | danger */
    variant: { type: String, default: 'neutral' },
    class: { type: null, default: undefined },
})

// Semantic colours the shadcn palette has no equivalent for; the contract wins.
const VARIANT_CLASS = {
    neutral: 'bg-muted text-muted-foreground',
    primary: 'bg-primary/10 text-primary dark:bg-primary/20',
    info: 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
    success: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    warning: 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
    danger: 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300',
}

const classes = computed(() => cn(VARIANT_CLASS[props.variant] ?? VARIANT_CLASS.neutral, props.class))
</script>

<template>
    <UiBadge variant="secondary" :class="classes">
        <slot />
    </UiBadge>
</template>

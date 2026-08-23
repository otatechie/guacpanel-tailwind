<script setup>
import { computed } from 'vue'
import { Button as UiButton } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

// GuacPanel button contract — see docs/ui-contract.md.
// Pages import this wrapper, never @/Components/ui/button directly.
const props = defineProps({
    /** primary | secondary | danger | ghost | outline | link */
    variant: { type: String, default: 'primary' },
    /** xs | sm | md | lg | icon | icon-sm | icon-xs */
    size: { type: String, default: 'md' },
    /** Native button type; defaults to 'button' so forms don't submit by accident */
    type: { type: String, default: 'button' },
    /** Render as another element/component (e.g. Inertia's Link) */
    as: { type: [String, Object, Function], default: 'button' },
    class: { type: null, default: undefined },
})

const UI_VARIANT = {
    primary: 'default',
    secondary: 'outline',
    danger: 'destructive',
    ghost: 'ghost',
    outline: 'outline',
    link: 'link',
}

const UI_SIZE = {
    xs: 'xs',
    sm: 'sm',
    md: 'default',
    lg: 'lg',
    icon: 'icon',
    'icon-sm': 'icon-sm',
    'icon-xs': 'icon-xs',
}

// The app's danger button is solid red (see the old .btn-danger), while shadcn's
// destructive is a soft tint. The contract wins: override here, not in ui/.
const VARIANT_OVERRIDE = {
    danger: 'bg-destructive text-white hover:bg-destructive/90 dark:bg-destructive dark:text-white dark:hover:bg-destructive/90',
}

const uiVariant = computed(() => UI_VARIANT[props.variant] ?? 'default')
const uiSize = computed(() => UI_SIZE[props.size] ?? 'default')
const isNativeButton = computed(() => props.as === 'button')
const classes = computed(() => cn(VARIANT_OVERRIDE[props.variant], props.class))
</script>

<template>
    <UiButton
        :as="as"
        :variant="uiVariant"
        :size="uiSize"
        :type="isNativeButton ? type : undefined"
        :class="classes">
        <slot />
    </UiButton>
</template>

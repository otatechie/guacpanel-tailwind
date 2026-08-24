<script setup>
import { computed } from 'vue'
import { Button as UiButton } from '@/Components/ui/button'
import { cn } from '@/lib/utils'

// Contract in docs/ui-contract.md. Pages import this, never @/Components/ui/button.
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

// --primary-hover tracks the theme picker; see utils/themeInit.js.
const VARIANT_OVERRIDE = {
    primary: 'bg-primary text-primary-foreground shadow-xs hover:bg-[var(--primary-hover)]',
    danger: 'bg-destructive text-white shadow-xs hover:bg-destructive/90 dark:bg-destructive dark:text-white dark:hover:bg-destructive/90',
    secondary: 'bg-card text-foreground shadow-xs hover:bg-muted',
}

const SIZE_OVERRIDE = {
    xs: 'h-7 px-2.5',
    sm: 'px-3',
    md: 'px-3.5',
    lg: 'px-4',
}

const uiVariant = computed(() => UI_VARIANT[props.variant] ?? 'default')
const uiSize = computed(() => UI_SIZE[props.size] ?? 'default')
const isNativeButton = computed(() => props.as === 'button')
// Tailwind v4's preflight dropped the browser default `cursor: pointer` on
// <button>, so every button reads as non-interactive unless it says otherwise.
// Set here once rather than at each call site.
const classes = computed(() =>
    cn(
        'cursor-pointer rounded-lg',
        SIZE_OVERRIDE[props.size],
        VARIANT_OVERRIDE[props.variant],
        props.class
    )
)
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

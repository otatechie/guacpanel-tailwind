<script setup>
import { computed } from 'vue'
import { Button as UiButton } from '@/Components/ui/button'

// GuacPanel button contract — see docs/ui-contract.md.
// Pages import this wrapper, never @/Components/ui/button directly.
const props = defineProps({
    /** primary | secondary | danger | ghost | outline | link */
    variant: { type: String, default: 'primary' },
    /** xs | sm | md | lg | icon */
    size: { type: String, default: 'md' },
    /** Native button type; defaults to 'button' so forms don't submit by accident */
    type: { type: String, default: 'button' },
})

const UI_VARIANT = {
    primary: 'default',
    secondary: 'secondary',
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
}

const uiVariant = computed(() => UI_VARIANT[props.variant] ?? 'default')
const uiSize = computed(() => UI_SIZE[props.size] ?? 'default')
</script>

<template>
    <UiButton :variant="uiVariant" :size="uiSize" :type="type">
        <slot />
    </UiButton>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    breadcrumbs: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <nav
        v-if="breadcrumbs.length"
        class="bg-[var(--color-surface-muted)] px-3 sm:px-6 py-3 border-b border-[var(--color-border)]"
        aria-label="Breadcrumbs">
        <ol class="flex items-center space-x-1 sm:space-x-2 text-sm overflow-x-auto scrollbar-hide">
            <template v-for="(crumb, index) in breadcrumbs" :key="index">
                <li class="flex-shrink-0">
                    <Link
                        v-if="crumb.href"
                        :href="crumb.href"
                        class="text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
                        {{ crumb.label }}
                    </Link>
                    <span v-else class="text-[var(--color-text)]">{{ crumb.label }}</span>
                </li>
                <li
                    v-if="index < breadcrumbs.length - 1"
                    class="text-[var(--color-text-muted)] flex-shrink-0"
                    aria-hidden="true">
                    /
                </li>
            </template>
        </ol>
    </nav>

    <header
        class="px-3 sm:px-6 py-4 sm:py-5 border-b border-[var(--color-border)] bg-[var(--color-surface)]">
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
            <div class="flex-1 min-w-0">
                <h1 class="sub-heading text-lg sm:text-xl">
                    {{ title }}
                </h1>
                <p v-if="description" class="mt-1 text-sm text-[var(--color-text-muted)]">
                    {{ description }}
                </p>
            </div>
            <aside class="actions flex-shrink-0">
                <slot name="actions"></slot>
            </aside>
        </div>
    </header>
</template>

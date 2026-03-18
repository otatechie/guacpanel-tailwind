<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <div class="mb-4 sm:mb-6">
        <!-- Breadcrumbs -->
        <nav v-if="breadcrumbs.length" class="mb-3" aria-label="Breadcrumbs">
            <ol class="flex items-center gap-1.5 text-xs text-(--color-text-muted)">
                <template v-for="(crumb, index) in breadcrumbs" :key="index">
                    <li>
                        <Link
                            v-if="crumb.href"
                            :href="crumb.href"
                            class="hover:text-(--color-text) transition-colors">
                            {{ crumb.label }}
                        </Link>
                        <span v-else class="text-(--color-text)">{{ crumb.label }}</span>
                    </li>
                    <li v-if="index < breadcrumbs.length - 1" aria-hidden="true">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </li>
                </template>
            </ol>
        </nav>

        <!-- Title row -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl font-semibold text-(--color-text)">{{ title }}</h1>
                <p v-if="description" class="mt-1 text-sm text-(--color-text-muted)">{{ description }}</p>
            </div>
            <div class="shrink-0">
                <slot name="actions" />
            </div>
        </div>

        <slot name="bottom" />
    </div>
</template>

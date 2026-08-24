<script setup>
import { Link } from '@inertiajs/vue3'
import { ChevronRightIcon } from '@lucide/vue'

defineProps({
    title: {
        type: String,
        required: true,
    },
    /** Applied to the h1 so a page's `<main aria-labelledby>` has something to point at */
    id: {
        type: String,
        default: '',
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
            <ol class="text-muted-foreground flex items-center gap-1.5 text-xs">
                <template v-for="(crumb, index) in breadcrumbs" :key="index">
                    <li>
                        <Link
                            v-if="crumb.href"
                            :href="crumb.href"
                            class="hover:text-foreground transition-colors">
                            {{ crumb.label }}
                        </Link>
                        <span v-else class="text-foreground">{{ crumb.label }}</span>
                    </li>
                    <li v-if="index < breadcrumbs.length - 1" aria-hidden="true">
                        <ChevronRightIcon class="h-3 w-3" aria-hidden="true" />
                    </li>
                </template>
            </ol>
        </nav>

        <!-- Title row -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 :id="id || undefined" class="text-foreground text-xl font-semibold">
                    {{ title }}
                </h1>
                <p v-if="description" class="text-muted-foreground mt-1 text-sm">
                    {{ description }}
                </p>
            </div>
            <div class="shrink-0">
                <slot name="actions" />
            </div>
        </div>

        <slot name="bottom" />
    </div>
</template>

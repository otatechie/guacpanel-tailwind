<script setup>
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
    <nav class="bg-gray-50 px-3 sm:px-6 py-3 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        v-if="breadcrumbs.length" aria-label="Breadcrumbs">
        <ol class="flex items-center space-x-1 sm:space-x-2 text-sm overflow-x-auto scrollbar-hide">
            <template v-for="(crumb, index) in breadcrumbs" :key="index">
                <li class="flex-shrink-0">
                    <Link v-if="crumb.href" :href="crumb.href"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    {{ crumb.label }}
                    </Link>
                    <span v-else class="text-gray-800 dark:text-gray-200">{{ crumb.label }}</span>
                </li>
                <li v-if="index < breadcrumbs.length - 1" class="text-gray-400 dark:text-gray-500 flex-shrink-0" aria-hidden="true">
                    /
                </li>
            </template>
        </ol>
    </nav>

    <header class="px-3 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
            <div class="flex-1 min-w-0">
                <h1 class="sub-heading dark:text-white text-lg sm:text-xl">{{ title }}</h1>
                <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ description }}
                </p>
            </div>
            <aside class="actions flex-shrink-0">
                <slot name="actions"></slot>
            </aside>
        </div>
    </header>
</template>

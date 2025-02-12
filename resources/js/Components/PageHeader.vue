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
    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        v-if="breadcrumbs.length">
        <div class="flex items-center space-x-2 text-sm">
            <template v-for="(crumb, index) in breadcrumbs" :key="index">
                <Link v-if="crumb.href" :href="crumb.href"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                {{ crumb.label }}
                </Link>
                <span v-else class="text-gray-800 dark:text-gray-200">{{ crumb.label }}</span>
                <span v-if="index < breadcrumbs.length - 1" class="text-gray-400 dark:text-gray-500">/</span>
            </template>
        </div>
    </div>

    <header class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="sub-heading dark:text-white">{{ title }}</h1>
                <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ description }}
                </p>
            </div>
            <slot name="actions"></slot>
        </div>
    </header>
</template>

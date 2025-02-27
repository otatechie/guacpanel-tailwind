<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
    }
});
</script>

<template>
    <nav aria-label="Breadcrumbs" class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-b border-gray-300 dark:border-gray-700">
        <ol class="flex items-center space-x-2 text-sm">
            <template v-for="(item, index) in items" :key="index">
                <li>
                    <Link v-if="item.href" :href="item.href"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        {{ item.label }}
                    </Link>
                    <span v-else class="text-gray-800 dark:text-gray-200 font-medium">{{ item.label }}</span>
                </li>

                <li v-if="index < items.length - 1" class="text-gray-400 dark:text-gray-500" aria-hidden="true">/</li>
            </template>
        </ol>
    </nav>
</template>

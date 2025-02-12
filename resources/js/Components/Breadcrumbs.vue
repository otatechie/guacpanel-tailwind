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
    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-b border-gray-300 dark:border-gray-700">
        <div class="flex items-center space-x-2 text-sm">
            <template v-for="(item, index) in items" :key="index">
                <template v-if="item.href">
                    <Link :href="item.href"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    {{ item.label }}
                    </Link>
                </template>
                <template v-else>
                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.label }}</span>
                </template>

                <span v-if="index < items.length - 1" class="text-gray-400 dark:text-gray-500">/</span>
            </template>
        </div>
    </div>
</template>

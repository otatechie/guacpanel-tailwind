<template>

    <nav class="flex pb-5" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <template v-if="index !== 0">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5
                                 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <component :is="index === 0 ? 'a' : 'span'" :href="index === 0 ? item.href : undefined" :class="[
                        'text-sm font-medium',
                        index === items.length - 1 ? 'text-gray-400 font-medium' : 'text-gray-500 hover:text-gray-700'
                    ]" :aria-current="index === items.length - 1 ? 'page' : undefined">
                        {{ item.title }}
                    </component>
                </div>
            </li>
        </ol>
    </nav>

</template>

<script setup>
defineProps({
    items: {
        type: Array,
        required: true,
        validator: (value) => value.every(item => 'title' in item && 'href' in item)
    }
});
</script>

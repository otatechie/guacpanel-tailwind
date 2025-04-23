<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script>


<template>
    <nav v-if="links.length > 3" aria-label="Pagination" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <span v-if="link.url === null" class="mb-1 mr-1 px-4 py-3 text-gray-600 text-sm leading-4 border rounded"
                aria-disabled="true">
                {{ link.label }}
            </span>
            <Link v-else :href="link.url"
                class="mb-1 mr-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-gray-700"
                :class="{
                    'bg-gray-800 text-white': link.active,
                    'text-gray-800': !link.active
                }" :aria-current="link.active ? 'page' : undefined" :aria-label="`Go to page ${link.label}`"
                preserve-state preserve-scroll>
            {{ link.label }}
            </Link>
        </template>
    </nav>
</template>

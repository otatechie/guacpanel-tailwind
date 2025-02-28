<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link,
    },
    props: {
        links: Array,
    },
}
</script>

<template>
    <nav v-if="links.length > 3" aria-label="Pagination" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links">
            <span v-if="link.url === null" :key="key"
                class="mb-1 mr-1 px-4 py-3 text-gray-600 text-sm leading-4 border rounded" aria-disabled="true">
                {{ link.label }}
            </span>
            <Link v-else :key="`link-${key}`"
                class="mb-1 mr-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-gray-700"
                :class="{
                    'bg-gray-800 text-white': link.active,
                    'text-gray-800': !link.active
                }" :href="link.url" :aria-current="link.active ? 'page' : undefined"
                :aria-label="`Go to page ${link.label}`" preserve-state preserve-scroll>
            {{ link.label }}
            </Link>
        </template>
    </nav>
</template>

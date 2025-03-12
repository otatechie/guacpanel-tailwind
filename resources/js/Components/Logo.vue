<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => {
    return page.props.personalisation?.app_logo || null
})
</script>

<template>
    <header class="flex justify-center items-center">
        <figure v-if="logoUrl" class="flex justify-center">
            <img class="h-10 my-2 w-auto object-contain" :src="logoUrl" alt="Logo" />
        </figure>
    </header>
</template>

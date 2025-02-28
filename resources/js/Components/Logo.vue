<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => {
    return page.props.personalisation?.appLogo
})
const appName = computed(() => {
    return page.props.personalisation?.appName || ''
})
</script>

<template>
    <header class="flex justify-center">
        <figure v-if="logoUrl" class="flex justify-center">
            <img class="h-24 w-auto object-contain"
                :src="logoUrl"
                :alt="`${appName} logo`" />
        </figure>
        <h1 v-else class="text-3xl font-bold">
            {{ appName }}
        </h1>
    </header>
</template>

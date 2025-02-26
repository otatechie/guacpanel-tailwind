<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => {
    return page.props.personalisation?.appLogo
})
</script>

<template>
    <div class="flex justify-center">
        <div v-if="logoUrl" class="flex justify-center">
            <img class="h-24 w-auto object-contain" :src="logoUrl" :alt="page.props.personalisation?.appName" />
        </div>
        <div v-else class="text-3xl font-bold">
            {{ page.props.personalisation?.appName }}
        </div>
    </div>
</template>

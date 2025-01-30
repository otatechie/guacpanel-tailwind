<template>

    <Head title="Home" />

    <slot>

        <div class="lg:mx-auto lg:max-w-7xl">
            <!-- Main Container -->
            <div class="max-w-7xl mx-auto px-4">
            </div>
        </div>

    </slot>

</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import Default from '../Layouts/Default.vue'


defineProps({

})

defineOptions({
    layout: Default
})

const page = usePage()

</script>

<style>
/* Add any additional styling if needed */
</style>

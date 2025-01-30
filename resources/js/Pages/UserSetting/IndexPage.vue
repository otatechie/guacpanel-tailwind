<template>

    <Head title="Profile setting" />

    <slot>
        <div class="px-5">

        </div>

    </slot>

</template>

<script>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Default from '../../Layouts/Default.vue';

export default {
    components: {
        Head,

    },

    layout: Default,

};
</script>

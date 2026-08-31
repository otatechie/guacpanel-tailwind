<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import DevicesTab from '@js/Pages/UserAccount/Tabs/DevicesTab.vue'

/* The account page already manages sessions. This route is the command palette's
   way in, so it renders the same component rather than a second implementation
   that drifts from it. */
defineOptions({
    layout: Default,
})

defineProps({
    user: { type: Object, required: true },
    sessions: { type: Object },
})
</script>

<template>
    <Head title="Active sessions" />

    <main class="mx-auto max-w-4xl">
        <PageHeader
            title="Active sessions"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Account', href: route('user.index') },
                { label: 'Active sessions' },
            ]" />

        <DevicesTab :user="user" :sessions="sessions" :showHeading="false" />
    </main>
</template>

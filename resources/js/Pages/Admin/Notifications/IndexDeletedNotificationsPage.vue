<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'

defineOptions({
    layout: Default,
})

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'System settings', href: route('admin.setting.index') },
    { label: 'Notifications', href: route('admin.notifications.index') },
    { label: 'Deleted notifications' },
])
</script>

<template>
    <Head title="Deleted notifications" />

    <main class="mx-auto max-w-7xl" aria-labelledby="admin-notifications-deleted">
        <PageHeader
            title="Deleted notifications"
            description="Manage soft-deleted notifications (coming soon)"
            :breadcrumbs="breadcrumbs">
            <template #actions>
                <Button :as="Link" variant="secondary" :href="route('admin.notifications.index')">
                    Back
                </Button>
            </template>
        </PageHeader>

        <div class="border-border bg-card rounded-xl border p-6 shadow-sm">
            <p class="text-muted-foreground text-sm">
                This page will be implemented next for managing soft deleted notifications. We may
                not need it since we have a job to clean them up.
            </p>
        </div>
    </main>
</template>

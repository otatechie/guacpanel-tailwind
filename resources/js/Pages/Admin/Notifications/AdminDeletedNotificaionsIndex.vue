<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'

defineOptions({
  layout: Default,
})

const breadcrumbs = computed(() => [
  { label: 'Dashboard', href: route('dashboard') },
  { label: 'Admin Notifications', href: route('admin.notifications.index') },
  { label: 'Deleted' },
])
</script>

<template>
  <Head title="Deleted Notifications" />

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="admin-notifications-deleted">
    <div class="container-border">
      <PageHeader
        title="Deleted Notifications"
        description="Manage soft-deleted notifications (coming soon)"
        :breadcrumbs="breadcrumbs">
        <template #actions>
          <Link :href="route('admin.notifications.index')" class="btn btn-secondary">Back</Link>
        </template>
      </PageHeader>

      <section class="bg-[var(--color-bg)] p-6">
        <div
          class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
          <p class="text-sm text-[var(--color-text-muted)]">
            This page will be implemented next for managing soft deleted notifications. We may not
            need it since we have a job to clean them up.
          </p>
        </div>
      </section>
    </div>
  </main>
</template>

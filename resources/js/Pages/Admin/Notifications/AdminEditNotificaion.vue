<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormTextarea from '@js/Components/Forms/FormTextarea.vue'

defineOptions({
  layout: Default,
})

const props = defineProps({
  notification: {
    type: Object,
    required: true,
  },
  users: {
    type: Array,
    default: () => [],
  },
})

const scopeOptions = [
  { label: 'User', value: 'user' },
  { label: 'System', value: 'system' },
  { label: 'Release', value: 'release' },
]

const typeOptions = [
  { label: 'Success', value: 'success' },
  { label: 'Info', value: 'info' },
  { label: 'Warning', value: 'warning' },
  { label: 'Danger', value: 'danger' },
]

const userOptions = computed(() =>
  (props.users || []).map(u => ({
    label: u.email ? `${u.name} (${u.email})` : u.name,
    value: u.id,
  }))
)

const toDatetimeLocal = value => {
  if (!value) return ''
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return ''
  const pad = n => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const form = useForm({
  scope: props.notification.scope ?? 'user',
  user_id: props.notification.user_id ?? '',
  type: props.notification.type ?? 'success',
  title: props.notification.title ?? '',
  message: props.notification.message ?? '',
  scheduled_on: toDatetimeLocal(props.notification.scheduled_on),
  auto_expire_on: toDatetimeLocal(props.notification.auto_expire_on),
})

watch(
  () => form.scope,
  next => {
    if (next !== 'user') form.user_id = ''
  }
)

const submit = () => {
  form.put(route('admin.notifications.update', props.notification.id))
}

const breadcrumbs = computed(() => [
  { label: 'Dashboard', href: route('dashboard') },
  { label: 'Admin Notifications', href: route('admin.notifications.index') },
  { label: 'Edit' },
])
</script>

<template>
  <Head title="Edit Notification" />

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="admin-notifications-edit">
    <div class="container-border">
      <PageHeader
        title="Edit Notification"
        description="Update an existing app notification"
        :breadcrumbs="breadcrumbs">
        <template #actions>
          <Link :href="route('admin.notifications.index')" class="btn btn-secondary btn-sm">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="mr-1 size-3.5">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>

            Back
          </Link>
        </template>
      </PageHeader>

      <section class="bg-[var(--color-bg)] p-6">
        <div
          class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
          <form class="space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <FormSelect
                v-model="form.scope"
                label="Scope"
                :options="scopeOptions"
                :error="form.errors.scope" />

              <FormSelect
                v-model="form.type"
                label="Type"
                :options="typeOptions"
                :error="form.errors.type" />

              <FormSelect
                v-if="form.scope === 'user'"
                v-model="form.user_id"
                label="User"
                placeholder="Select user"
                :options="userOptions"
                :error="form.errors.user_id" />

              <div v-else class="hidden md:block"></div>

              <FormInput v-model="form.title" label="Title" :error="form.errors.title" required />

              <FormInput
                v-model="form.scheduled_on"
                label="Scheduled On"
                type="datetime-local"
                :error="form.errors.scheduled_on"
                help="Optional. Leave blank to send immediately." />

              <FormInput
                v-model="form.auto_expire_on"
                label="Auto Expire On"
                type="datetime-local"
                :error="form.errors.auto_expire_on"
                help="Optional." />
            </div>

            <FormTextarea
              v-model="form.message"
              label="Message"
              :error="form.errors.message"
              :rows="4"
              required />

            <div class="flex items-center justify-end gap-3">
              <Link :href="route('admin.notifications.index')" class="btn btn-secondary btn-md">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="mr-1 size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Cancel
              </Link>
              <button type="submit" class="btn btn-primary btn-md" :disabled="form.processing">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="mr-1 size-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>

                Save
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>
</template>

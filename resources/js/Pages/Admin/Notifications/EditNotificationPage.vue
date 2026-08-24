<script setup>
import Button from '@/Components/Button.vue'
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
    { label: 'System settings', href: route('admin.setting.index') },
    { label: 'Notifications', href: route('admin.notifications.index') },
    { label: 'Edit notification' },
])
</script>

<template>
    <Head title="Edit notification" />

    <main class="mx-auto max-w-7xl" aria-labelledby="admin-notifications-edit">
        <PageHeader
            title="Edit notification"
            description="Update an existing app notification"
            :breadcrumbs="breadcrumbs">
            <template #actions>
                <Button
                    :as="Link"
                    variant="secondary"
                    size="sm"
                    :href="route('admin.notifications.index')">
                    Back
                </Button>
            </template>
        </PageHeader>

        <div class="card max-w-3xl p-5">
            <form class="space-y-5" @submit.prevent="submit">
                <!-- Content -->
                <div class="space-y-4">
                    <FormInput
                        v-model="form.title"
                        label="Title"
                        :error="form.errors.title"
                        required />
                    <FormTextarea
                        v-model="form.message"
                        label="Message"
                        :error="form.errors.message"
                        :rows="3"
                        required />
                </div>

                <!-- Classification -->
                <div class="border-border grid grid-cols-1 gap-4 border-t pt-5 sm:grid-cols-3">
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
                </div>

                <!-- Timing -->
                <div class="border-border grid grid-cols-1 gap-4 border-t pt-5 sm:grid-cols-2">
                    <FormInput
                        v-model="form.scheduled_on"
                        label="Schedule"
                        type="datetime-local"
                        :error="form.errors.scheduled_on"
                        help="Leave blank to send immediately" />
                    <FormInput
                        v-model="form.auto_expire_on"
                        label="Auto expire"
                        type="datetime-local"
                        :error="form.errors.auto_expire_on"
                        help="Optional" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <Button
                        :as="Link"
                        variant="secondary"
                        size="sm"
                        :href="route('admin.notifications.index')">
                        Cancel
                    </Button>
                    <Button variant="primary" size="sm" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </main>
</template>

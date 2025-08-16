<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import FormInput from '@/Components/FormInput.vue'
import FormRadioGroup from '@/Components/FormRadioGroup.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import Switch from '@/Components/Switch.vue'

defineOptions({
    layout: Default
})

const form = useForm({
    title: '',
    content: '',
    is_active: true,
    is_dismissible: false,
    type: 'info',
    visible_from: '',
    expires_at: '',
})

const noticeTypes = [
    { label: 'Information', value: 'info' },
    { label: 'Success', value: 'success' },
    { label: 'Warning', value: 'warning' },
    { label: 'Error', value: 'error' },
]

const submit = () => {
    form.post(route('admin.system-notice.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Create System Notice" />
    <main class="max-w-7xl mx-auto" aria-labelledby="create-system-notice">
        <div class="container-border">
            <PageHeader title="Create System Notice" description="Add a new system-wide notice for all users."
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'System Notices', href: route('admin.system-notice.index') },
                    { label: 'Create' }
                ]" />
            <div class="p-6 dark:bg-gray-900">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <form id="create-notice-form" @submit.prevent="submit" class="max-w-2xl space-y-8">
                        <div class="space-y-6">
                            <FormInput label="Title" v-model="form.title" :error="form.errors.title" required
                                placeholder="Enter a clear, concise title" />
                            <FormRadioGroup label="Notice type" v-model="form.type" :options="noticeTypes"
                                :error="form.errors.type"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600" />
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Visibility Period</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput label="Start date & time" type="datetime-local" v-model="form.visible_from"
                                    :error="form.errors.visible_from" help="Leave empty for immediate visibility" />
                                <FormInput label="End date & time" type="datetime-local" v-model="form.expires_at"
                                    :error="form.errors.expires_at" help="Leave empty for no expiration" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <FormTextarea label="Content" v-model="form.content" :error="form.errors.content" required
                                rows="4" placeholder="Enter the notice message" />
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Notice Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="mb-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Active</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Make notice visible to users</p>
                                    </div>
                                    <Switch label="Active Notice" v-model="form.is_active" :error="form.errors.is_active"
                                        help="Enable to make the notice visible to users" />
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="mb-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Dismissible</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Allow users to hide notice</p>
                                    </div>
                                    <Switch label="Allow Users to Dismiss" v-model="form.is_dismissible"
                                        :error="form.errors.is_dismissible" help="Enable to let users hide this notice" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end -mx-6">
                <button type="submit" form="create-notice-form" class="btn btn-sm btn-primary" :disabled="form.processing"
                    :aria-busy="form.processing">
                    <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ form.processing ? 'Creating...' : 'Create notice' }}
                </button>
            </div>
        </div>
    </main>
</template>

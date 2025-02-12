<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import Switch from '@/Components/Switch.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout:
        Default
})

const props = defineProps({
    user: Object,
    roles: Object,
    permissions: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles?.[0]?.id || '',
    force_password_change: Boolean(props.user.force_password_change) || false,
    disable_account: Boolean(props.user.disable_account) || false,
    permissions: props.user.permissions?.map(permission => permission.id) || [],
})

const tabs = [
    { key: 'account', label: 'Account Settings' },
    { key: 'permissions', label: 'Permissions' }
]

const activeTab = ref('account')

const submit = () => {
    form.post(route('admin.user.update', props.user.id), {
        preserveScroll: true,
    })
}

const deleteUser = () => {
    if (confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')) {
        form.delete(route('admin.users.destroy', props.user.id))
    }
}
</script>

<template>
    <Head :title="`Edit User - ${props.user.name}`" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden dark:bg-gray-800 dark:border-gray-700">
            <PageHeader
                :title="`Edit User - ${props.user.name}`"
                description="Manage user information and permissions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/home' },
                    { label: 'Users', href: '/admin/users' },
                    { label: props.user.name }
                ]"
            />

            <form @submit.prevent="submit" class="divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                <div v-show="activeTab === 'account'" class="p-6 space-y-6">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-white">Basic Information</h2>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="w-full md:w-2/3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <FormInput v-model="form.name" label="Legal name" :error="form.errors.name" />
                                    <FormInput v-model="form.email" label="Email address" type="email"
                                        :error="form.errors.email" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-white">Role Assignment</h2>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:w-2/3">
                                <FormSelect v-model="form.role" :options="roles.data" option-label="name" option-value="id"
                                    label="Assigned role" :error="form.errors.role" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 dark:bg-green-900/50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-white">Account Settings</h2>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Account Status</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enable or disable user access</p>
                                    </div>
                                    <Switch v-model="form.disable_account" />
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Force Password Reset</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Require new password on next login</p>
                                    </div>
                                    <Switch v-model="form.force_password_change" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-50 dark:bg-red-950 rounded-lg">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-red-100">Danger Zone</h2>
                        </div>

                        <div class="bg-red-50 dark:bg-red-950 rounded-lg p-6 border border-red-200 dark:border-red-900 dark:ring-1 dark:ring-red-800">
                            <p class="text-sm text-red-600 dark:text-red-300 mb-4">
                                Permanently delete this user account. This action cannot be undone.
                            </p>
                            <button
                                type="button"
                                @click="deleteUser"
                                class="btn-danger-outline dark:border-red-800 dark:text-red-300 dark:hover:bg-red-900 dark:hover:border-red-700 dark:hover:text-red-200 transition-colors duration-150">
                                Delete account
                            </button>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'permissions'" class="p-6 space-y-6">
                    <div class="bg-amber-50 dark:bg-amber-900/50 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                        <p class="text-sm text-amber-700 dark:text-amber-400 font-medium flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Direct permissions override role-based permissions. Only assign direct permissions when necessary.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full md:w-2/3">
                            <div v-for="permission in permissions.data" :key="permission.id"
                                class="p-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <FormCheckbox v-model="form.permissions" :value="permission.id"
                                    :label="permission.name" />
                                <p v-if="permission.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-7">
                                    {{ permission.description }}
                                </p>
                            </div>
                        </div>
                        <p v-if="!permissions?.data?.length" class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">
                            No permissions available
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex items-center justify-end gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import Switch from '@/Components/Switch.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default
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
    {
        key: 'account',
        label: 'Account Settings',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
    },
    {
        key: 'permissions',
        label: 'Permissions',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
    }
]

const activeTab = ref('account')
const showDeleteModal = ref(false)

const closeModal = () => {
    showDeleteModal.value = false
}

const submit = () => {
    form.put(route('admin.user.update', props.user.id), {
        preserveScroll: true,
    })
}

const deleteUser = () => {
    form.delete(route('admin.users.destroy', props.user.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>


<template>

    <Head :title="`Edit User - ${props.user.name}`" />

    <main class="max-w-5xl mx-auto" role="main">
        <div class="container-border overflow-hidden dark:bg-gray-800">
            <PageHeader :title="`Edit User - ${props.user.name}`" description="Manage user information, roles, and permissions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Users', href: '/admin/users' },
                    { label: props.user.name }
                ]" />

            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="px-6 bg-gray-50 dark:bg-gray-800" aria-label="User tabs">
                    <ul class="flex -mb-px" role="tablist">
                        <li v-for="tab in tabs" :key="tab.key" class="mr-2" role="presentation">
                            <button type="button" @click="activeTab = tab.key" :class="[
                                'px-4 py-3 inline-flex items-center gap-2 font-medium text-sm whitespace-nowrap cursor-pointer',
                                activeTab === tab.key
                                    ? 'border-b-2 border-purple-500 text-purple-600 bg-white dark:bg-gray-700 dark:text-purple-400'
                                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700'
                            ]" :aria-selected="activeTab === tab.key" :aria-controls="`${tab.key}-panel`" role="tab">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="tab.icon" />
                                </svg>
                                {{ tab.label }}
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>

            <form @submit.prevent="submit">
                <div v-show="activeTab === 'account'" class="p-6 space-y-6">
                    <div class="w-full md:w-2/3 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <FormInput v-model="form.name" label="Legal name" :error="form.errors.name" name="name" />
                            <FormInput v-model="form.email" label="Email address" type="email"
                                :error="form.errors.email" name="email" />
                        </div>
                        <div>
                            <FormSelect v-model="form.role" :options="roles.data" option-label="name" option-value="id"
                                name="role" label="Assigned role" :error="form.errors.role" />
                        </div>
                        <div class="space-y-6">
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-white">Account Status</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Enable or disable user access
                                    </p>
                                </div>
                                <Switch v-model="form.disable_account" />
                            </div>

                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-white">Force Password Reset</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Require new password on next login
                                    </p>
                                </div>
                                <Switch v-model="form.force_password_change" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pt-6">
                            <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Danger Zone</h2>
                        </div>
                        <div
                            class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 border border-red-200 dark:border-red-800">
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400 mb-4">
                                Permanently delete this user account and all associated data
                            </p>
                            <button type="button" @click="showDeleteModal = true" class="btn-danger">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Delete account
                            </button>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'permissions'" class="p-6 space-y-6">
                    <div
                        class="bg-amber-50 dark:bg-amber-900/50 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                        <p class="text-sm text-amber-700 dark:text-amber-400 font-medium flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Direct permissions override role-based permissions. Only assign direct permissions when
                            necessary.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="permission in permissions.data" :key="permission.id"
                                class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <FormCheckbox v-model="form.permissions" :value="permission.id"
                                    :label="permission.name" :name="`permission-${permission.id}`" />
                                <p v-if="permission.description"
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-7">
                                    {{ permission.description }}
                                </p>
                            </div>
                        </div>
                        <p v-if="!permissions?.data?.length"
                            class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">
                            No permissions available
                        </p>
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </div>
    </main>

    <Modal :show="showDeleteModal" @close="closeModal" size="sm">
        <template #title>
            <div class="flex items-center gap-2 text-red-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Delete User Account
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this user account? This action cannot be undone and all associated
                    data will be permanently removed.
                </p>
                <div
                    class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            User: <span class="font-medium">{{ props.user.name }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="deleteUser" :disabled="form.processing" type="button" class="btn-danger">
                    {{ form.processing ? 'Deleting...' : 'Yes, delete account' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

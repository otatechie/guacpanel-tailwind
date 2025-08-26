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
        label: 'Account',
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
    form.delete(route('admin.user.destroy', props.user.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>


<template>

    <Head :title="`Edit User - ${props.user.name}`" />

    <main class="max-w-7xl mx-auto" aria-labelledby="edit-user">
        <div class="container-border overflow-hidden">
            <PageHeader :title="`Edit User - ${props.user.name}`"
                description="Manage user information, roles, and permissions" :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Users', href: '/admin/users' },
                    { label: props.user.name }
                ]" />

            <section class="p-3 sm:p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="px-3 sm:px-6 bg-gray-50 dark:bg-gray-800" aria-label="User tabs">
                            <ul class="flex -mb-px overflow-x-auto scrollbar-hide" role="tablist">
                                <li v-for="tab in tabs" :key="tab.key" class="mr-1 sm:mr-2 flex-shrink-0"
                                    role="presentation">
                                    <button type="button" @click="activeTab = tab.key" :class="[
                                        'px-3 sm:px-4 py-3 inline-flex items-center gap-1 sm:gap-2 font-medium text-sm whitespace-nowrap cursor-pointer',
                                        activeTab === tab.key
                                            ? 'border-b-2 border-blue-500 text-blue-600 bg-white dark:bg-gray-700 dark:text-blue-400'
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700'
                                    ]" :aria-selected="activeTab === tab.key" :aria-controls="`${tab.key}-panel`"
                                        role="tab">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="tab.icon" />
                                        </svg>
                                        {{ tab.label }}
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Tab content with smooth transitions -->
                    <section class="relative">
                        <div class="relative mt-4">
                            <form @submit.prevent="submit">
                                <div v-show="activeTab === 'account'" class="p-3 sm:p-6 space-y-6">
                                    <div class="w-full lg:w-2/3 space-y-6">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-6">
                                            <FormInput v-model="form.name" label="Legal name" :error="form.errors.name"
                                                name="name" />
                                            <FormInput v-model="form.email" label="Email address" type="email"
                                                :error="form.errors.email" name="email" />
                                        </div>

                                        <div class="space-y-4">
                                            <!-- Show read-only role for superuser -->
                                            <div v-if="props.user.is_superuser" class="space-y-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Assigned role
                                                </label>
                                                <div
                                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100 capitalize">
                                                    {{ props.user.roles?.[0]?.name || 'No role assigned' }}
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Superuser role is protected and cannot be changed
                                                </p>
                                            </div>

                                            <!-- Show editable role select for regular users -->
                                            <FormSelect v-else v-model="form.role" :options="roles.data"
                                                option-label="name" option-value="id" name="role" label="Assigned role"
                                                :error="form.errors.role" />
                                        </div>

                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                                                :class="{ 'opacity-50': props.user.is_superuser }">
                                                <div>
                                                    <h3 class="font-medium text-gray-800 dark:text-white">Account Status
                                                    </h3>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ props.user.is_superuser ? 'Superuser account cannot be disabled' : 'Enable or disable user access' }}
                                                    </p>
                                                </div>
                                                <Switch v-model="form.disable_account"
                                                    :disabled="props.user.is_superuser" />
                                            </div>

                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                                                :class="{ 'opacity-50': props.user.is_superuser }">
                                                <div>
                                                    <h3 class="font-medium text-gray-800 dark:text-white">Force Password
                                                        Reset</h3>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ props.user.is_superuser ? 'Superuser cannot be forced to reset password' : 'Require new password on next login' }}
                                                    </p>
                                                </div>
                                                <Switch v-model="form.force_password_change"
                                                    :disabled="props.user.is_superuser" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4" v-if="!props.user.is_superuser">
                                        <div
                                            class="bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                                            <h3
                                                class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                                                Danger Zone</h3>

                                            <!-- Delete Account -->
                                            <div
                                                class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                                                <h4
                                                    class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                                                    Delete Account Permanently</h4>
                                                <p
                                                    class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                                                    This action is permanent and cannot be undone. All user data will be
                                                    permanently deleted.
                                                </p>
                                                <button type="button" @click="showDeleteModal = true"
                                                    class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Permanently delete account</span>
                                                    <span class="sm:hidden">Delete Account</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-show="activeTab === 'permissions'" class="p-3 sm:p-6 space-y-6">
                                    <div
                                        class="bg-amber-50 dark:bg-amber-900/50 border border-amber-200 dark:border-amber-800 rounded-lg p-3 sm:p-4">
                                        <p
                                            class="text-sm text-amber-700 dark:text-amber-400 font-medium flex items-center gap-2">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Direct permissions override role-based permissions. Only assign direct
                                            permissions when
                                            necessary.
                                        </p>
                                    </div>

                                    <div class="space-y-4">
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                                            <div v-for="permission in permissions.data" :key="permission.id"
                                                class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
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
                                    class="px-3 sm:px-6 py-4 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                                    <button type="submit" class="btn btn-sm btn-primary w-full sm:w-auto"
                                        :disabled="form.processing">
                                        <svg v-if="form.processing" class="animate-spin h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        {{ form.processing ? 'Saving...' : 'Save changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </section>
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
            <div class="flex justify-end gap-8">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="deleteUser" :disabled="form.processing" type="button" class="btn btn-sm btn-danger">
                    {{ form.processing ? 'Deleting...' : 'Yes, delete account' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import FormCheckboxGroup from '@/Components/FormCheckboxGroup.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Tabs from '@/Components/Tabs.vue'
import Alert from '@/Components/Alert.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: Object,
    roles: Object,
    permissions: Object
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles?.[0]?.id || '',
    force_password_change: Boolean(props.user.force_password_change) || false,
    disable_account: Boolean(props.user.disable_account) || false,
    permissions: props.user.permissions?.map(permission => permission.id) || []
})

const tabs = [
    {
        name: 'Account',
        key: 'account'
    },
    {
        name: 'Permissions',
        key: 'permissions'
    }
]

const activeTab = ref(0)
const showDeleteModal = ref(false)

const closeModal = () => {
    showDeleteModal.value = false
}

const submit = () => {
    const submitData = {
        name: form.name,
        email: form.email,
        role: form.role,
        force_password_change: form.force_password_change,
        disable_account: form.disable_account
    }

    if (activeTab.value === 1) {
        submitData.permissions = form.permissions
    }

    form.transform(data => submitData).put(route('admin.user.update', props.user.id), {
        preserveScroll: true
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
        <div class="container-border">
            <PageHeader :title="`Edit User - ${props.user.name}`"
                description="Manage user information, roles, and permissions" :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Users', href: '/admin/users' },
                    { label: props.user.name }
                ]" />

            <section class="p-3 sm:p-6 bg-[var(--color-bg)]">
                <div
                    class="bg-[var(--color-surface)] rounded-xl shadow-sm border border-[var(--color-border)] overflow-hidden">
                    <!-- Tab Navigation -->
                    <div class="px-3 sm:px-6 bg-[var(--color-surface-muted)]">
                        <Tabs v-model="activeTab" :tabs="tabs" />
                    </div>

                    <!-- Tab content with smooth transitions -->
                    <section class="relative">
                        <div class="relative">
                            <form @submit.prevent="submit">
                                <Transition name="tab-fade" mode="out-in" appear>
                                    <div v-if="activeTab === 0" class="p-3 sm:p-6 space-y-6">
                                        <div class="w-full lg:w-2/3 space-y-6">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-6">
                                                <FormInput v-model="form.name" label="Legal name"
                                                    :error="form.errors.name" name="name" />
                                                <FormInput v-model="form.email" label="Email address" type="email"
                                                    :error="form.errors.email" name="email" />
                                            </div>

                                            <div class="space-y-4">
                                                <!-- Show read-only role for superuser -->
                                                <div v-if="props.user.is_superuser" class="space-y-2">
                                                    <label class="block text-sm font-medium text-[var(--color-text)]">
                                                        Assigned role
                                                    </label>
                                                    <div
                                                        class="px-3 py-2 bg-[var(--color-surface-muted)] border border-[var(--color-border-strong)] rounded-md text-[var(--color-text)] capitalize">
                                                        {{
                                                            props.user.roles?.[0]?.name ||
                                                            'No role assigned'
                                                        }}
                                                    </div>
                                                    <p class="text-sm text-[var(--color-text-muted)]">
                                                        Superuser role is protected and cannot be
                                                        changed
                                                    </p>
                                                </div>

                                                <!-- Show editable role select for regular users -->
                                                <FormSelect v-else v-model="form.role" :options="roles.data"
                                                    option-label="name" option-value="id" name="role"
                                                    label="Assigned role" :error="form.errors.role" />
                                            </div>

                                            <div class="space-y-4">
                                                <FormCheckbox v-model="form.disable_account"
                                                    :disabled="props.user.is_superuser" label="Disable Account" :help="props.user.is_superuser
                                                            ? 'Superuser account cannot be disabled'
                                                            : 'Enable or disable user access'
                                                        " :error="form.errors.disable_account" />

                                                <FormCheckbox v-model="form.force_password_change"
                                                    :disabled="props.user.is_superuser" label="Force Password Reset"
                                                    :help="props.user.is_superuser
                                                            ? 'Superuser cannot be forced to reset password'
                                                            : 'Require new password on next login'
                                                        " :error="form.errors.force_password_change" />
                                            </div>
                                        </div>
                                        
                                        <div class="border-t border-[var(--color-border)] py-2">
                                        </div>
                                        <div v-if="!props.user.is_superuser" class="space-y-4">
                                            <div
                                                class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                                                <h3
                                                    class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                                                    Danger Zone
                                                </h3>

                                                <!-- Delete Account -->
                                                <div
                                                    class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                                                    <h4
                                                        class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                                                        Delete Account Permanently
                                                    </h4>
                                                    <p
                                                        class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                                                        This action is permanent and cannot be
                                                        undone. All user data will be permanently
                                                        deleted.
                                                    </p>
                                                    <button type="button"
                                                        class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center gap-2"
                                                        @click="showDeleteModal = true">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span class="hidden sm:inline">
                                                            Permanently Delete Account
                                                        </span>
                                                        <span class="sm:hidden">
                                                            Delete Account
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else-if="activeTab === 1" class="p-3 sm:p-6 space-y-6">
                                        <Alert type="info">
                                            Direct permissions override role-based permissions. Only
                                            assign direct permissions when necessary.
                                        </Alert>

                                        <FormCheckboxGroup v-model="form.permissions" :options="permissions.data"
                                            option-label="name" option-value="id" option-description="description"
                                            name="permissions" :error="form.errors.permissions"
                                            help="Select the permissions you want to assign to this user" />
                                    </div>
                                </Transition>

                                <div
                                    class="px-3 sm:px-6 py-4 bg-[var(--color-surface-muted)] flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-[var(--color-border)]">
                                    <button type="submit" class="btn btn-sm btn-primary w-full sm:w-auto"
                                        :disabled="form.processing">
                                        <svg v-if="form.processing" class="animate-spin h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" size="sm" @close="closeModal">
        <template #title>
            <div class="flex items-center gap-2 text-red-600">
                Delete User Account
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this user account? This action cannot be undone
                    and all associated data will be permanently removed.
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
                            User:
                            <span class="font-medium">{{ props.user.name }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    @click="closeModal">
                    Cancel
                </button>
                <button :disabled="form.processing" type="button" class="btn btn-sm btn-danger" @click="deleteUser">
                    {{ form.processing ? 'Deleting...' : 'Yes, Delete Account' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<style scoped>
.tab-fade-enter-active,
.tab-fade-leave-active {
    transition: all 0.3s ease;
}

.tab-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.tab-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}
</style>

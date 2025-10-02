<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({})

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    location: props.user.location
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true
    })
}

const submitPasswordForm = () => {
    passwordForm.put('/user/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset()
    })
}

const confirmDeactivateAccount = () => {
    return
}

const confirmDeleteAccount = () => {
    return
}

const deactivateAccount = () => {
    deactivateForm.post(route('user.deactivate'), {
        preserveScroll: true,
        onSuccess: () => {
            deactivateModal.value = false
            window.location.href = route('home')
        }
    })
}

const deleteAccount = () => {
    deleteForm.post(route('user.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            deleteModal.value = false
            window.location.href = route('home')
        }
    })
}
</script>

<template>
    <Head title="Profile Settings" />
    <main class="max-w-7xl mx-auto" aria-labelledby="profile-settings">
        <div class="container-border">
            <PageHeader
                title="Account Settings"
                description="Manage your profile information, password, and account settings"
                :breadcrumbs="[{ label: 'Dashboard', href: '/' }, { label: 'Account Settings' }]" />

            <!-- Basic Information Section -->
            <div class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Basic Information
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Update your personal information and email address
                    </p>

                    <form
                        id="profile-form"
                        class="max-w-2xl space-y-8"
                        @submit.prevent="submitProfileForm">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput
                                    v-model="profileForm.name"
                                    label="Legal name"
                                    :error="profileForm.errors.name"
                                    required
                                    placeholder="Enter your full name" />
                                <FormInput
                                    v-model="profileForm.email"
                                    label="Email address"
                                    type="email"
                                    autocomplete="email"
                                    :error="profileForm.errors.email"
                                    disabled />
                            </div>
                            <FormInput
                                v-model="profileForm.location"
                                label="Location"
                                :error="profileForm.errors.location"
                                placeholder="Enter your location" />
                        </div>
                    </form>
                </div>
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button
                    type="submit"
                    form="profile-form"
                    class="btn btn-sm btn-primary"
                    :disabled="profileForm.processing"
                    :aria-busy="profileForm.processing">
                    <svg
                        v-if="profileForm.processing"
                        class="animate-spin h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4" />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ profileForm.processing ? 'Saving...' : 'Save profile' }}
                </button>
            </div>
            <!-- Password Section -->
            <div class="p-6 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Password
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Ensure your account is using a secure password
                    </p>

                    <form
                        id="password-form"
                        class="max-w-2xl space-y-8"
                        @submit.prevent="submitPasswordForm">
                        <div class="space-y-6">
                            <FormInput
                                v-model="passwordForm.current_password"
                                label="Current password"
                                type="password"
                                autocomplete="current-password"
                                :error="passwordForm.errors.current_password"
                                required />
                            <FormInput
                                v-model="passwordForm.password"
                                label="New password"
                                type="password"
                                autocomplete="new-password"
                                :error="passwordForm.errors.password"
                                required />
                            <FormInput
                                v-model="passwordForm.password_confirmation"
                                label="Confirm new password"
                                type="password"
                                autocomplete="new-password"
                                :error="passwordForm.errors.password_confirmation"
                                required />
                        </div>
                    </form>
                </div>
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button
                    type="submit"
                    form="password-form"
                    class="btn btn-sm btn-primary"
                    :disabled="passwordForm.processing"
                    :aria-busy="passwordForm.processing">
                    <svg
                        v-if="passwordForm.processing"
                        class="animate-spin h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4" />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ passwordForm.processing ? 'Updating...' : 'Update password' }}
                </button>
            </div>
            <!-- Danger Zone Section -->
            <div class="p-4 sm:p-6 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                    <h3
                        class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                        Danger Zone
                    </h3>

                    <!-- Deactivate Account -->
                    <div
                        class="mb-4 sm:mb-6 p-3 sm:p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-700">
                        <h4
                            class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Deactivate Account
                        </h4>
                        <p
                            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                            Hide your profile and data temporarily. You can reactivate your account
                            anytime.
                        </p>
                        <button
                            class="w-full sm:w-auto btn-secondary btn-sm inline-flex items-center gap-2"
                            @click="confirmDeactivateAccount">
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span class="hidden sm:inline">Deactivate account</span>
                            <span class="sm:hidden">Deactivate</span>
                        </button>
                    </div>

                    <!-- Delete Account -->
                    <div
                        class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                        <h4
                            class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Delete Account Permanently
                        </h4>
                        <p
                            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                            This action is permanent and cannot be undone. All your data will be
                            permanently deleted.
                        </p>
                        <button
                            class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center gap-2"
                            @click="confirmDeleteAccount">
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="hidden sm:inline">Permanently delete account</span>
                            <span class="sm:hidden">Delete Account</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="deactivateModal" size="sm" @close="deactivateModal = false">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Deactivate Account
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to deactivate your account? This action cannot be
                        undone.
                    </p>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="deactivateForm.processing"
                        @click="deactivateModal = false">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn-danger"
                        :disabled="deactivateForm.processing"
                        @click="deactivateAccount">
                        {{ deactivateForm.processing ? 'Deactivating...' : 'Yes, deactivate' }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="deleteModal" size="sm" @close="deleteModal = false">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Delete Account
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete your account? This action is permanent and
                        cannot be undone.
                    </p>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="deleteForm.processing"
                        @click="deleteModal = false">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn-danger"
                        :disabled="deleteForm.processing"
                        @click="deleteAccount">
                        {{ deleteForm.processing ? 'Deleting...' : 'Yes, delete' }}
                    </button>
                </div>
            </template>
        </Modal>
    </main>
</template>

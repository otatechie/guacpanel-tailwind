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
    },
})

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({})

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    location: props.user.location,
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true,
    })
}

const submitPasswordForm = () => {
    passwordForm.put('/user/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset()
    })
}

const confirmDeactivateAccount = () => {
    // Prevent deactivation
    return
}

const confirmDeleteAccount = () => {
    // Prevent deletion
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

    <main class="max-w-5xl mx-auto space-y-8" aria-labelledby="profile-settings">
        <h1 class="sr-only" id="profile-settings">Profile Settings</h1>

        <section class="container-border overflow-hidden" aria-labelledby="profile-info">
            <PageHeader title="Basic Information" description="Update your personal information and email address"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Account' },
                    { label: 'Basic Information' }
                ]" />

            <form @submit.prevent="submitProfileForm" class="divide-y divide-gray-200 dark:divide-gray-600">
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="basic-info">
                    <header class="flex items-center gap-3">
                        <span class="p-2 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <h2 id="basic-info" class="text-lg font-medium text-gray-800 dark:text-gray-200">Basic
                            Information</h2>
                    </header>

                    <div class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <fieldset class="w-full md:w-2/3 space-y-6">
                            <legend class="sr-only">Personal Information</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <FormInput v-model="profileForm.name" label="Legal name"
                                    :error="profileForm.errors.name" />
                                <FormInput v-model="profileForm.email" label="Email address" type="email"
                                    autocomplete="email" :error="profileForm.errors.email" />
                            </div>
                            <FormInput v-model="profileForm.location" label="Location"
                                :error="profileForm.errors.location" />
                        </fieldset>
                    </div>
                </section>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex justify-end">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="profileForm.processing" :aria-busy="profileForm.processing">
                        <svg v-if="profileForm.processing" class="animate-spin h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ profileForm.processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </section>

        <!-- Password Section -->
        <section class="container-border overflow-hidden" aria-labelledby="password-section">
            <PageHeader title="Password" description="Ensure your account is using a secure password" :breadcrumbs="[
                { label: 'Dashboard', href: '/' },
                { label: 'Account' },
                { label: 'Password' }
            ]" />

            <form @submit.prevent="submitPasswordForm" class="divide-y divide-gray-200 dark:divide-gray-600">
                <section class="p-6 space-y-6 dark:bg-gray-700" aria-labelledby="change-password" id="password-section">
                    <header class="flex items-center gap-3">
                        <span class="p-2 bg-purple-50 dark:bg-purple-900 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </span>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Change
                            Password</h2>
                    </header>

                    <div class="dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <fieldset class="w-full md:w-2/3 space-y-6">
                            <legend class="sr-only">Password Change Form</legend>
                            <FormInput v-model="passwordForm.current_password" label="Current password" type="password"
                                autocomplete="current-password" :error="passwordForm.errors.current_password" />
                            <FormInput v-model="passwordForm.password" label="New password" type="password"
                                autocomplete="new-password" :error="passwordForm.errors.password" />
                            <FormInput v-model="passwordForm.password_confirmation" label="Confirm new password"
                                type="password" autocomplete="new-password"
                                :error="passwordForm.errors.password_confirmation" />
                        </fieldset>
                    </div>
                </section>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex justify-end">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="passwordForm.processing" :aria-busy="passwordForm.processing">
                        <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ passwordForm.processing ? 'Updating...' : 'Update password' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 space-y-6"
            aria-labelledby="danger-zone">
            <header class="flex items-center gap-3">
                <span class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <h2 id="danger-zone" class="text-lg font-medium text-gray-800 dark:text-gray-200">
                    Danger Zone
                </h2>
            </header>

            <div
                class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 border border-red-200 dark:border-red-800 space-y-6">
                <div>
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">Deactivate Account</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Hide your profile and data temporarily. You can reactivate your account anytime.
                    </p>
                    <button @click="confirmDeactivateAccount" class="btn-secondary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Deactivate Account
                    </button>
                </div>

                <div class="pt-6 border-t border-red-200 dark:border-red-800">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">Delete Account Permanently
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        This action is permanent and cannot be undone. All your data will be permanently deleted.
                    </p>
                    <button @click="confirmDeleteAccount" class="btn-danger inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Permanently Delete Account
                    </button>
                </div>
            </div>
        </section>

        <Modal :show="deactivateModal" @close="deactivateModal = false" size="sm">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Deactivate Account
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to deactivate your account? This action cannot be undone.
                    </p>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <button @click="deactivateModal = false" type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="deactivateForm.processing">
                        Cancel
                    </button>
                    <button @click="deactivateAccount" type="button" class="btn-danger"
                        :disabled="deactivateForm.processing">
                        {{ deactivateForm.processing ? 'Deactivating...' : 'Yes, deactivate' }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="deleteModal" @close="deleteModal = false" size="sm">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Delete Account
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete your account? This action is permanent and cannot be undone.
                    </p>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <button @click="deleteModal = false" type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="deleteForm.processing">
                        Cancel
                    </button>
                    <button @click="deleteAccount" type="button" class="btn-danger" :disabled="deleteForm.processing">
                        {{ deleteForm.processing ? 'Deleting...' : 'Yes, delete' }}
                    </button>
                </div>
            </template>
        </Modal>
    </main>
</template>

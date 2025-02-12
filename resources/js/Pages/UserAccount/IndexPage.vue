<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import FormInput from '../../Components/FormInput.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({ layout: Default })

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
})

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
</script>

<template>

    <Head title="Profile" />

    <main class="max-w-5xl mx-auto space-y-6">
        <section class="container-border overflow-hidden">
            <PageHeader title="Basic Information" description="Update your personal information and email address"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/home' },
                    { label: 'Account' },
                    { label: 'Basic Information' }
                ]" />

            <form @submit.prevent="submitProfileForm" class="divide-y divide-gray-200">
                <div class="p-6 space-y-6 dark:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Basic Information</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="w-full md:w-2/3 space-y-6">
                            <FormInput v-model="profileForm.name" label="Legal name" :error="profileForm.errors.name" />
                            <FormInput v-model="profileForm.email" label="Email address" type="email"
                                :error="profileForm.errors.email" />
                            <FormInput v-model="profileForm.location" label="Location"
                                :error="profileForm.errors.location" />
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex items-center justify-end gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="profileForm.processing">
                        <svg v-if="profileForm.processing" class="animate-spin h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ profileForm.processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="container-border overflow-hidden mt-12" id="password-section">
            <PageHeader title="Password" description="Ensure your account is using a secure password" :breadcrumbs="[
                    { label: 'Dashboard', href: '/home' },
                    { label: 'Account' },
                    { label: 'Password' }
                ]" />

            <form @submit.prevent="submitPasswordForm" class="divide-y divide-gray-200">
                <div class="p-6 space-y-6 dark:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Change Password</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="w-full md:w-2/3 space-y-6">
                            <FormInput v-model="passwordForm.current_password" label="Current password" type="password"
                                :error="passwordForm.errors.current_password" />
                            <FormInput v-model="passwordForm.password" label="New password" type="password"
                                :error="passwordForm.errors.password" />
                            <FormInput v-model="passwordForm.password_confirmation" label="Confirm new password"
                                type="password" :error="passwordForm.errors.password_confirmation" />
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex items-center justify-end gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2"
                        :disabled="passwordForm.processing">
                        <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ passwordForm.processing ? 'Updating...' : 'Update password' }}
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>

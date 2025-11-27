<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import FormInput from '@/Components/FormInput.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
    passwordEnabled: {
        type: Boolean,
        default: false
    }
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const submitPasswordForm = () => {
    passwordForm.put('/user/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset()
    })
}

</script>

<template>
    <Head title="Security - Account Settings" />
    <div>
        <div class="p-6 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                <Alert
                    v-if="!passwordEnabled"
                    type="info"
                    class="mb-5"
                >
                    For demo purposes, password reset operations have been disabled in the Fortify
                    configuration.
                </Alert>

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
                :disabled="passwordForm.processing || !passwordEnabled"
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
    </div>
</template>

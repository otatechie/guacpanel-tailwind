<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'
import Switch from '@/Components/Switch.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    settings: {
        type: Object,
        required: true
    }
})

const form = useForm({
    force_password_change: Boolean(props.settings?.force_password_change ?? false),
    password_expiry: Boolean(props.settings?.password_expiry ?? false),
    passwordless_login: Boolean(props.settings?.passwordless_login ?? false),
    two_factor_authentication: Boolean(props.settings?.two_factor_authentication ?? false),
})

const submit = () => {
    form.post(route('admin.setting.update'))
}
</script>

<template>
    <Head title="Security & Authentication Settings" />

    <main class="max-w-5xl mx-auto">
        <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-sm">
                    <Link href="/admin" class="text-gray-500 hover:text-gray-700">Dashboard</Link>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-800">Security Settings</span>
                </div>
            </div>

            <header class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="sub-heading">Security & Authentication</h1>
                        <p class="mt-1 text-gray-500">
                            Configure system-wide security policies and authentication requirements
                        </p>
                    </div>
                </div>
            </header>

            <form @submit.prevent="submit" class="divide-y divide-gray-200">
                <div class="p-6 space-y-6">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800">Password Security</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-800">Initial Password Change</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Require users to set a new password when they first access their account
                                        </p>
                                    </div>
                                    <Switch v-model="form.force_password_change" />
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-800">Password Expiration Policy</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Require regular password updates to maintain security standards
                                        </p>
                                        <div v-if="form.password_expiry" class="mt-3 flex items-center gap-2 p-3 rounded-lg bg-amber-50 border border-amber-200">
                                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm text-amber-700 font-medium">
                                                Passwords must be changed every 90 days
                                            </span>
                                        </div>
                                    </div>
                                    <Switch v-model="form.password_expiry" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800">Authentication Methods</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-800">Two-Factor Authentication</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Add an extra layer of security by making two-factor authentication mandatory
                                        </p>
                                    </div>
                                    <Switch v-model="form.two_factor_authentication" />
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-800">Passwordless Login</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Allow users to log in without entering a password
                                        </p>
                                    </div>
                                    <Switch v-model="form.passwordless_login" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3">
                    <button
                        type="submit"
                        class="btn-primary inline-flex items-center gap-2"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving changes...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </section>
    </main>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import Switch from '@/Components/Switch.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    settings: {
        type: Object,
        required: true,
        default: () => ({})
    }
})

const form = useForm({
    password_expiry: Boolean(props.settings?.password_expiry ?? false),
    passwordless_login: Boolean(props.settings?.passwordless_login ?? true),
    two_factor_authentication: Boolean(props.settings?.two_factor_authentication ?? false),
})

const submit = () => {
    form.post(route('admin.setting.update'), {
        preserveScroll: true
    })
}
</script>


<template>

    <Head title="Security & Authentication Settings" />

    <main class="max-w-5xl mx-auto" role="main">
        <div class="container-border overflow-hidden">
            <PageHeader title="Security & Authentication"
                description="Configure system-wide security policies and authentication requirements" :breadcrumbs="[
                    { label: 'Dashboard', href: route('home') },
                    { label: 'Settings', href: route('admin.setting.index') },
                    { label: 'Security Settings' }
                ]" />

            <form @submit.prevent="submit" class="divide-y divide-gray-200 dark:divide-gray-700">
                <section class="p-6 space-y-6">
                    <header class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-white">Password Security</h2>
                    </header>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 dark:text-white">Password Expiration Policy</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Require regular password updates to maintain security standards
                                </p>
                                <div v-if="form.password_expiry"
                                    class="mt-3 flex items-center gap-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/50 border border-amber-200 dark:border-amber-800">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-amber-700 dark:text-amber-300 font-medium">
                                        Passwords must be changed every 90 days
                                    </span>
                                </div>
                            </div>
                            <Switch v-model="form.password_expiry" :aria-label="'Toggle password expiration policy'" />
                        </div>
                    </div>
                </section>

                <section class="p-6 space-y-6">
                    <header class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-white">Authentication Methods</h2>
                    </header>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 dark:text-white">Two-Factor Authentication</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Add an extra layer of security by making two-factor authentication mandatory
                                </p>
                            </div>
                            <Switch v-model="form.two_factor_authentication"
                                :aria-label="'Toggle two-factor authentication'" />
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 dark:text-white">Passwordless Login</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Allow users to log in without entering a password
                                </p>
                            </div>
                            <Switch v-model="form.passwordless_login" :aria-label="'Toggle passwordless login'" />
                        </div>
                    </div>
                </section>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex items-center justify-end gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2" :disabled="form.processing"
                        :aria-busy="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ form.processing ? 'Saving changes...' : 'Save changes' }}
                    </button>
                </div>
            </form>
        </div>
    </main>
</template>

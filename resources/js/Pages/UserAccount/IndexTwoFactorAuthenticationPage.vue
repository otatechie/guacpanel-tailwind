<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    qrCodeSvg: {
        type: String,
        required: false,
        default: null
    },
    recoveryCodes: {
        type: Array,
        required: false,
        default: () => []
    }
})

const showDisableModal = ref(false)
const enableForm = useForm({})
const regenerateForm = useForm({})
const disableForm = useForm({})

const enableTwoFactor = () => {
    enableForm.post(route('two-factor.enable'), {
        preserveScroll: true
    })
}

const regenerateCodes = () => {
    regenerateForm.post(route('two-factor.recovery-codes'), {
        preserveScroll: true
    })
}

const closeModal = () => {
    showDisableModal.value = false
}

const disableTwoFactor = () => {
    disableForm.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            showDisableModal.value = false
        }
    })
}

const benefits = [
    'Adds an additional security layer beyond passwords',
    'Protects against compromised credentials',
    'Required for sensitive account actions',
    'Meets industry security standards'
]
</script>


<template>

    <Head title="Two-Factor Authentication" />

    <main class="max-w-6xl mx-auto" aria-labelledby="2fa-settings">

        <article class="container-border overflow-hidden">
            <PageHeader title="Two-Factor Authentication" description="Add an extra layer of security to your account"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('home') },
                    { label: 'Settings', href: route('admin.setting.index') },
                    { label: 'Two-Factor Authentication' }
                ]" />

            <div class="p-6 dark:bg-gray-700">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-sm text-blue-700 dark:text-blue-400 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    For demo purposes, two-factor authentication operations have been disabled in the Fortify configuration.
                </div>
            </div>

            <!-- 2FA Setup Section -->
            <section v-if="!user.two_factor_secret" class="p-6 space-y-6 dark:bg-gray-700"
                aria-labelledby="security-benefits">
                <header class="flex items-center gap-3">
                    <span class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg" aria-hidden="true">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <h2 id="security-benefits" class="text-lg font-medium text-gray-800 dark:text-gray-200">Security
                        Benefits</h2>
                </header>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <ul class="space-y-3" aria-label="2FA Benefits">
                        <li v-for="(benefit, index) in benefits" :key="index"
                            class="flex items-center gap-3 bg-white dark:bg-gray-900 px-4 py-3 rounded-lg">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">{{ benefit }}</span>
                        </li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button @click="enableTwoFactor" :disabled="enableForm.processing" class="btn-primary"
                        :aria-busy="enableForm.processing">
                        {{ enableForm.processing ? 'Enabling...' : 'Enable 2FA' }}
                    </button>
                </div>
            </section>

            <!-- 2FA Management Section -->
            <div v-else class="divide-y divide-gray-200 dark:divide-gray-600 dark:bg-gray-700">
                <section class="p-6 space-y-6" aria-labelledby="setup-instructions">
                    <header class="flex items-center gap-3">
                        <span class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </span>
                        <h2 id="setup-instructions" class="text-lg font-medium text-gray-800 dark:text-gray-200">Setup
                            Instructions</h2>
                    </header>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-8">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="bg-gray-400 flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-primary-600 text-white font-medium">1</span>
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Download an
                                        Authenticator App</h3>
                                </div>
                                <div class="ml-11 space-y-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Choose and install one of these recommended authenticator apps:
                                    </p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <a href="https://apps.apple.com/us/app/ente-authenticator/id6444121398"
                                            target="_blank" rel="noopener noreferrer"
                                            class="flex flex-col items-center gap-2 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8"
                                                viewBox="0 0 384 512">
                                                <path fill="currentColor"
                                                    d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z" />
                                            </svg>
                                            <span
                                                class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                                App Store
                                            </span>
                                        </a>
                                        <a href="https://play.google.com/store/apps/details?id=io.ente.auth"
                                            target="_blank" rel="noopener noreferrer"
                                            class="flex flex-col items-center gap-2 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8"
                                                viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z" />
                                            </svg>
                                            <span
                                                class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                                Play Store
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="bg-gray-400 flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-white font-medium">2</span>
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Scan QR Code</h3>
                                </div>
                                <p class="ml-11 text-sm text-gray-600 dark:text-gray-400">
                                    Open your authenticator app and scan the QR code. The app will start generating
                                    security codes automatically.
                                </p>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-xl">
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm flex flex-col items-center">
                                <div v-html="qrCodeSvg" class="w-56 h-56" aria-label="QR Code for 2FA setup"></div>
                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    Scan this QR code with your authenticator app
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Recovery Codes -->
                <section class="p-6 space-y-6" aria-labelledby="recovery-codes">
                    <div class="flex items-center justify-between">
                        <header class="flex items-center gap-3">
                            <span class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg" aria-hidden="true">
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </span>
                            <h2 id="recovery-codes" class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                Recovery Codes</h2>
                        </header>
                        <button @click="regenerateCodes" :disabled="regenerateForm.processing" class="btn-primary"
                            :aria-busy="regenerateForm.processing" aria-label="Regenerate recovery codes">
                            {{ regenerateForm.processing ? 'Generating...' : 'Regenerate codes' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" role="list" aria-label="Recovery codes list">
                        <div v-for="(code, index) in recoveryCodes" :key="code"
                            class="group relative bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors"
                            role="listitem">
                            <span class="absolute top-1 left-2 text-xs text-gray-300 dark:text-gray-500"
                                aria-hidden="true">
                                {{ index + 1 }}
                            </span>
                            <code
                                class="block font-mono text-sm text-gray-800 dark:text-gray-200 text-center select-all">
                                {{ code }}
                            </code>
                        </div>
                    </div>
                </section>

                <section class="p-6 space-y-6" aria-labelledby="danger-zone">
                    <header class="flex items-center gap-3">
                        <span class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                        <h2 id="danger-zone" class="text-lg font-medium text-gray-800 dark:text-gray-200">Danger Zone
                        </h2>
                    </header>

                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 border border-red-200 dark:border-red-800">
                        <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                            Disabling two-factor authentication will significantly reduce your account security
                        </p>
                        <button @click="showDisableModal = true" class="btn-danger inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Disable 2FA
                        </button>
                    </div>
                </section>
            </div>
        </article>
    </main>

    <Modal :show="showDisableModal" @close="closeModal" size="md">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Disable Two-Factor Authentication
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to disable two-factor authentication? This will remove an important security
                    layer from your account.
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
                            This action will immediately disable all 2FA protections for your account.
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    :disabled="disableForm.processing">
                    Cancel
                </button>
                <button @click="disableTwoFactor" type="button" class="btn-danger" :disabled="disableForm.processing">
                    {{ disableForm.processing ? 'Disabling...' : 'Yes, disable 2FA' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

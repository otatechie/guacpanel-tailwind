<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Alert from '@/Components/Alert.vue'

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
    },
    twoFactorEnabled: {
        type: Boolean,
        default: false
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
    <Head title="Multi-Factor Authentication - Account Settings" />

    <section class="p-4 sm:p-6 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
            <Alert
                v-if="!twoFactorEnabled"
                type="info"
            >
                For demo purposes, two-factor authentication operations have been disabled in the Fortify
                configuration.
            </Alert>

            <section v-if="!user.two_factor_secret" class="mt-4 sm:mt-6 space-y-4 sm:space-y-6" aria-labelledby="security-benefits">
                <header class="flex items-center gap-2 sm:gap-3">
                    <h2 id="security-benefits" class="text-base sm:text-lg font-medium text-gray-800 dark:text-gray-200">Security Benefits</h2>
                </header>

                <div class="rounded-lg p-2 border border-gray-200 dark:border-gray-700">
                    <ul class="space-y-2" aria-label="2FA Benefits">
                        <li v-for="(benefit, index) in benefits" :key="index" class="flex items-center gap-2 sm:gap-3 bg-white dark:bg-gray-900 px-3 sm:px-4 py-2 sm:py-3 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ benefit }}</span>
                        </li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="enableTwoFactor"
                        :disabled="enableForm.processing || !twoFactorEnabled"
                        class="btn-primary btn-sm w-full sm:w-auto"
                        :aria-busy="enableForm.processing"
                    >
                        {{ enableForm.processing ? 'Enabling...' : 'Enable 2FA' }}
                    </button>
                </div>
            </section>

            <div v-else class="mt-4 sm:mt-6">
                <section class="pt-4 sm:pt-6 space-y-4 sm:space-y-6" aria-labelledby="setup-instructions">
                    <header class="flex items-center gap-2 sm:gap-3">
                        <span class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </span>
                        <h2 id="setup-instructions" class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">Setup Instructions</h2>
                    </header>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <div class="space-y-6 sm:space-y-8">
                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span
                                        class="bg-gray-400 flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center rounded-full bg-primary-600 text-white text-xs sm:text-sm font-medium">1</span>
                                    <h3 class="text-base font-medium text-gray-800 dark:text-gray-200">Download an Authenticator App</h3>
                                </div>
                                <div class="ml-7 sm:ml-11 space-y-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Download an authenticator app from your app store (we recommend Ente). Use the link for your mobile device below.</p>
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                                        <a href="https://apps.apple.com/us/app/ente-authenticator/id6444121398"
                                            target="_blank" rel="noopener noreferrer"
                                            class="flex items-center justify-center gap-2 px-4 py-3 sm:px-3 sm:py-2 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group text-sm min-h-[44px] sm:min-h-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-4 sm:h-4"
                                                viewBox="0 0 384 512">
                                                <path fill="currentColor"
                                                    d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z" />
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 font-medium">App Store</span>
                                        </a>
                                        <a href="https://play.google.com/store/apps/details?id=io.ente.auth"
                                            target="_blank" rel="noopener noreferrer"
                                            class="flex items-center justify-center gap-2 px-4 py-3 sm:px-3 sm:py-2 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group text-sm min-h-[44px] sm:min-h-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-4 sm:h-4"
                                                viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z" />
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 font-medium">Play Store</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span
                                        class="bg-gray-400 flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center rounded-full bg-primary-600 text-white text-xs sm:text-sm font-medium">2</span>
                                    <h3 class="text-base sm:text-lg font-medium text-gray-800 dark:text-gray-200">Scan QR Code</h3>
                                </div>
                                <div class="ml-7 sm:ml-11 space-y-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Open your app, tap "+" then "Scan QR Code", and point your camera at the code below.</p>
                                </div>
                            </div>

                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span
                                        class="bg-gray-400 flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center rounded-full bg-primary-600 text-white text-xs sm:text-sm font-medium">3</span>
                                    <h3 class="text-base sm:text-lg font-medium text-gray-800 dark:text-gray-200">Enter Verification Code</h3>
                                </div>
                                <div class="ml-7 sm:ml-11 space-y-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Copy the 6-digit code from your app and paste it in the field below.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-6 lg:p-8 rounded-xl">
                            <div class="bg-white dark:bg-gray-900 p-4 sm:p-6 rounded-lg shadow-sm flex flex-col items-center">
                                <div v-html="qrCodeSvg" class="w-full max-w-[200px] sm:max-w-[224px] aspect-square" aria-label="QR Code for 2FA setup"></div>
                                <p class="mt-3 sm:mt-4 text-sm text-gray-600 dark:text-gray-400 text-center px-2">Scan this QR code with your authenticator app</p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="my-6 sm:my-8 border-t border-gray-200 dark:border-gray-700"></div>

                <section class="pt-4 sm:pt-6 space-y-6 sm:space-y-8" aria-labelledby="recovery-codes">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <header class="flex items-start sm:items-center gap-3 flex-1 min-w-0">
                            <span class="p-2 bg-amber-50/50 dark:bg-amber-900/10 rounded-lg flex-shrink-0" aria-hidden="true">
                                <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h2 id="recovery-codes" class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recovery Codes</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Save these codes in a secure location. You'll need them if you lose access to your authenticator app.</p>
                            </div>
                        </header>
                        <button @click="regenerateCodes" :disabled="regenerateForm.processing" class="btn-secondary btn-sm inline-flex items-center justify-center gap-2 w-full sm:w-auto min-h-[44px] sm:min-h-0" :aria-busy="regenerateForm.processing" aria-label="Regenerate recovery codes">
                            <svg v-if="regenerateForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            {{ regenerateForm.processing ? 'Generating...' : 'Regenerate Codes' }}
                        </button>
                    </div>

                    <div class="bg-amber-50/30 dark:bg-amber-900/5 border border-amber-200/50 dark:border-amber-700/20 rounded-xl p-3 sm:p-4 lg:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3 lg:gap-4" role="list" aria-label="Recovery codes list">
                            <div v-for="(code, index) in recoveryCodes" :key="code"
                                class="group relative bg-white dark:bg-gray-800 hover:bg-amber-50/40 dark:hover:bg-amber-900/10 active:bg-amber-50/60 dark:active:bg-amber-900/20 p-3 sm:p-2.5 rounded-lg border border-amber-200/40 dark:border-amber-700/30 transition-all duration-200 hover:border-amber-300/60 dark:hover:border-amber-600/40 hover:shadow-sm touch-manipulation"
                                role="listitem">
                                <div class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 text-xs font-medium text-amber-600/70 dark:text-amber-400/60 bg-amber-100/60 dark:bg-amber-900/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full"
                                    aria-hidden="true">
                                    {{ index + 1 }}
                                </div>
                                <code
                                    class="block font-mono text-sm sm:text-base text-gray-800 dark:text-gray-200 text-center select-all pt-5 sm:pt-0.5 font-semibold tracking-wider break-all">
                                    {{ code }}
                                </code>
                                <div
                                    class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <svg class="w-4 h-4 text-amber-500/70 dark:text-amber-400/60" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <Alert type="warning" title="Keep these codes safe!" class="mt-6">
                            Store them securely and don't share them with anyone. Each code can only be used once.
                        </Alert>
                    </div>
                </section>

                <section class="pt-8 sm:pt-12 lg:pt-16 space-y-4 sm:space-y-6" aria-labelledby="danger-zone">
                    <div class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">Danger Zone</h3>
                        <div class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                            <h4 class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">Disable Multi-Factor Authentication</h4>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Disabling multi-factor authentication will significantly reduce your account security. This action will immediately remove all 2FA protections for your account.</p>
                            <button @click="showDisableModal = true" class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center justify-center min-h-[44px] sm:min-h-0">
                                <span class="hidden sm:inline">Disable Multi-Factor Authentication</span>
                                <span class="sm:hidden">Disable 2FA</span>
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <Modal :show="showDisableModal" @close="closeModal" size="md">
        <template #title>
            <div class="flex items-center text-red-600 dark:text-red-400">Disable Multi-Factor Authentication</div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to disable multi-factor authentication? This will remove an important security layer from your account.</p>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">This action will immediately disable all 2FA protections for your account.</p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button @click="closeModal" type="button" class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer" :disabled="disableForm.processing">Cancel</button>
                <button @click="disableTwoFactor" type="button" class="btn-danger btn-sm" :disabled="disableForm.processing">{{ disableForm.processing ? 'Disabling...' : 'Yes, Disable 2FA' }}</button>
            </div>
        </template>
    </Modal>

</template>

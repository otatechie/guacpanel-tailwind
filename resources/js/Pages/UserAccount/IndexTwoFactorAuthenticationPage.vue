<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import { Link } from '@inertiajs/vue3'

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
        required: true
    },
    recoveryCodes: {
        type: Array,
        required: true
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
    <Head title="Two Factor Authentication" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden">
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-sm">
                    <Link href="/account" class="text-gray-500 hover:text-gray-700">Account</Link>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-800">Two-Factor Authentication</span>
                </div>
            </div>

            <header class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="sub-heading">Two-Factor Authentication</h1>
                        <p class="mt-1 text-gray-500">
                            Add an extra layer of security to your account using 2FA
                        </p>
                    </div>
                    <div v-if="user.two_factor_secret" class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-full">Enabled</span>
                    </div>
                </div>
            </header>

            <div v-if="!user.two_factor_secret" class="p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-medium text-gray-800">Security Benefits</h2>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <ul class="space-y-3">
                        <li v-for="(benefit, index) in benefits" :key="index"
                            class="flex items-center gap-3 bg-white px-4 py-3 rounded-lg">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-gray-600">{{ benefit }}</span>
                        </li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button @click="enableTwoFactor" :disabled="enableForm.processing" class="btn-primary">
                        {{ enableForm.processing ? 'Enabling...' : 'Enable 2FA' }}
                    </button>
                </div>
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Setup Instructions</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-700 text-white text-sm">1</span>
                                <h3 class="font-medium text-gray-700">Download an Authenticator App</h3>
                            </div>
                            <div class="ml-9 space-y-3">
                                <a href="https://apps.apple.com/us/app/ente-authenticator/id6444121398" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 hover:text-gray-800 transition-colors">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Download for iOS</span>
                                </a>
                                <a href="https://play.google.com/store/apps/details?id=io.ente.auth" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 hover:text-gray-800 transition-colors">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 0 1-.61-.92V2.734a1 1 0 0 1 .609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198l2.807 1.626a1 1 0 0 1 0 1.73l-2.808 1.626L15.891 12l1.807-2.379zM5.864 2.658L16.802 8.99l-2.302 2.302-8.636-8.634z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Download for Android</span>
                                </a>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-gray-700 text-white text-sm">2</span>
                                <h3 class="font-medium text-gray-700">Scan QR Code</h3>
                            </div>
                            <p class="ml-9 text-sm text-gray-500">Open your authenticator app and scan the QR code</p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="bg-white p-4 rounded-lg shadow-sm flex flex-col items-center">
                                <div v-html="qrCodeSvg" class="w-48 h-48"></div>
                                <p class="mt-4 text-xs text-gray-500">Scan with your authenticator app</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-50 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800">Recovery Codes</h2>
                        </div>
                        <button @click="regenerateCodes" :disabled="regenerateForm.processing" class="btn-primary-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ regenerateForm.processing ? 'Generating...' : 'Generate new codes' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="code in recoveryCodes" :key="code"
                            class="group relative bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border border-gray-200 transition-colors">
                            <code class="block font-mono text-sm text-gray-800 text-center select-all">{{ code }}</code>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-800/5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-xs font-medium text-gray-600">Click to copy</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-50 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800">Danger Zone</h2>
                    </div>

                    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                        <p class="text-sm text-red-600 mb-4">
                            Disabling two-factor authentication will significantly reduce your account security
                        </p>
                        <button @click="showDisableModal = true" class="btn-danger">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Disable 2FA
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <Modal :show="showDisableModal" @close="closeModal">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-800">
                    Disable Two-Factor Authentication
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Are you sure you want to disable two-factor authentication? This will remove an important security layer from your account.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-primary-outline" @click="closeModal">
                    Cancel
                </button>
                <button type="submit" class="btn-danger" @click="disableTwoFactor" :disabled="disableForm.processing">
                    {{ disableForm.processing ? 'Disabling...' : 'Yes, Disable 2FA' }}
                </button>
            </div>
        </div>
    </Modal>
</template>

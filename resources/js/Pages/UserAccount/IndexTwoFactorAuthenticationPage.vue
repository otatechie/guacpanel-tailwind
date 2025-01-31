<template>

    <Head title="Two Factor Authentication" />

    <div class="max-w-5xl mx-auto px-6">
        <div v-if="!user.two_factor_secret" class="container-border p-8 space-y-8 bg-white rounded-xl shadow-lg">
            <div class="space-y-4">
                <h2 class="sub-heading">
                    Two-Factor Authentication
                </h2>

                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-amber-900 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="leading-tight">Two-factor authentication is not enabled. Enable now for enhanced
                            security.</span>
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg border border-blue-50 space-y-4 shadow-inner">
                    <h3 class="font-semibold text-lg text-gray-800">Benefits of 2FA:</h3>
                    <ul class="space-y-3">
                        <li v-for="(benefit, index) in benefits" :key="index"
                            class="p-3 bg-green-50/50 rounded-lg border border-green-100 flex items-start gap-3 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 text-green-300">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>

                            <span class="text-gray-700">{{ benefit }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <form @submit.prevent="enableTwoFactor" class="flex justify-end">
                <button type="submit" class="w-48 btn-primary" :disabled="form.processing">
                    <span class="drop-shadow-sm">{{ form.processing ? 'Enabling...' : 'Enable 2FA' }}</span>
                </button>
            </form>
        </div>

        <div v-if="user.two_factor_secret" class="space-y-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-6">
                    <h2 class="sub-heading">
                        Set Up Authentication
                    </h2>

                    <div class="space-y-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-4">
                                <span
                                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-blue-600 text-white font-medium">1</span>
                                <h3 class="font-medium text-lg text-gray-800">Download an Authenticator App</h3>
                            </div>
                            <div class="ml-10 space-y-2">
                                <a href="https://apps.apple.com/us/app/ente-authenticator/id6444121398" target="_blank"
                                    class="flex items-center gap-2 text-gray-700 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13v4H7v2h4v4h2v-4h4v-2h-4V7h-2z" />
                                    </svg>
                                    <span class="text-sm">Download for iOS</span>
                                </a>
                                <a href="https://play.google.com/store/apps/details?id=io.ente.auth" target="_blank"
                                    class="flex items-center gap-2 text-gray-700 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13v4H7v2h4v4h2v-4h4v-2h-4V7h-2z" />
                                    </svg>
                                    <span class="text-sm">Download for Android</span>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span
                                class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-blue-600 text-white font-medium">2</span>
                            <div class="space-y-1">
                                <h3 class="font-medium text-lg text-gray-800">Scan QR Code</h3>
                                <p class="text-sm text-gray-600">Open your authenticator app and scan the QR code
                                    shown here</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-500 font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            Need help? Our support team is here to assist you
                        </p>
                    </div>
                </div>

                <div class="container-border p-8 bg-white rounded-xl shadow-lg">
                    <div
                        class="p-6 bg-gradient-to-br from-gray-50 to-white rounded-xl border-2 border-gray-100 flex flex-col items-center space-y-4">
                        <div class="p-2 bg-white rounded-lg shadow-inner">
                            <div v-html="qrCodeSvg" class="w-48 h-48"></div>
                        </div>
                        <p class="text-xs text-gray-600 text-center font-medium">
                            Scan this QR code with your authenticator app
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200" />

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-4">
                    <h2 class="sub-heading">
                        Recovery Codes
                    </h2>

                    <div class="space-y-4">
                        <p class="text-gray-600">
                            Save these backup codes in a secure location. You can use them to regain access to your
                            account if you lose your authentication device.
                        </p>

                        <div class="bg-white border border-amber-200 p-4 rounded-lg">
                            <p class="text-sm text-amber-500 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Each code can only be used once
                            </p>
                        </div>
                    </div>
                </div>

                <div class="container-border p-6 bg-white rounded-xl shadow-lg">
                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="code in recoveryCodes" :key="code"
                            class="p-3 bg-gray-50 rounded-lg border-2 border-gray-100 text-center hover:bg-gray-100 transition-colors">
                            <code class="font-mono text-sm text-gray-800 tracking-tighter">{{ code }}</code>
                        </div>
                    </div>

                    <form @submit.prevent="regenerateCodes" class="mt-6 flex justify-end">
                        <button type="submit" class="w-full btn-primary" :disabled="form.processing">
                            Generate new codes
                        </button>
                    </form>
                </div>
            </div>

            <hr class="border-gray-200" />

            <div class="bg-red-50 border border-red-100 p-6 rounded-xl">
                <form @submit.prevent="disableTwoFactor" class="flex flex-col items-center">
                    <p class="text-sm text-red-800 mb-4 text-center">
                        Disabling two-factor authentication will make your account less secure
                    </p>
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex justify-center px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed items-center cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Disable Two-Factor Authentication
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'

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

const form = useForm({})

const enableTwoFactor = () => {
    form.post(route('two-factor.enable'), {
        preserveScroll: true
    })
}

const regenerateCodes = () => {
    form.post(route('two-factor.recovery-codes'), {
        preserveScroll: true
    })
}

const disableTwoFactor = () => {
    form.delete(route('two-factor.disable'), {
        preserveScroll: true
    })
}

const benefits = [
    'Adds an additional security layer beyond passwords',
    'Protects against compromised credentials',
    'Required for sensitive account actions',
    'Meets industry security standards'
];
</script>

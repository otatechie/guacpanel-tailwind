<template>

    <Head title="Two factor authentication" />

    <slot>
        <div class="px-5">
            <div v-if="!user.two_factor_secret" class="container-border p-6">
                <h2 class="sub-heading mb-2">Why Two-Factor Authentication?</h2>
                <ul class="text-sm font-normal mt-1.5 list-disc pl-5 text-gray-700">
                    <li>Adds an extra layer of security beyond your password</li>
                    <li>Protect account if password is stolen</li>
                    <li>Prevents unauthorized account access</li>
                </ul>
                <p class="text-sm mt-4 text-red-500 font-medium mb-5">2FA is off. Enable for extra security.</p>
                <form @submit.prevent="enableTwoFactor">
                    <Button
                        @click="enableTwoFactor"
                        :label="form.processing ? 'Enabling...' : 'Enable 2FA'"
                        class="btn-primary"
                        :disabled="form.processing"
                        size="small"
                    />
                </form>
            </div>

            <div v-if="user.two_factor_secret" class="space-y-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="px-4 sm:px-0">
                        <h2 class="sub-heading">
                            Set Up Two-Factor Authentication
                        </h2>

                        <div class="my-6">
                            <div class="flex items-center mb-2">
                                <span
                                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-stone-200 text-gray-700 font-medium mr-4">1</span>
                                <h3 class="font-medium text-md text-gray-700">Download an Authenticator App</h3>
                            </div>
                            <div class="ml-12 space-y-1 text-sm">
                                <p class="text-gray-700">
                                    • iPhone users: Get authenticator from
                                    <a href="https://apps.apple.com/us/app/ente-authenticator/id6444121398"
                                        target="_blank" class="font-medium text-gray-700 hover:text-gray-700 underline underline-offset-2">
                                        App Store
                                    </a>
                                </p>
                                <p class="text-gray-700">
                                    • Android users: Get authenticator from
                                    <a href="https://play.google.com/store/apps/details?id=io.ente.auth" target="_blank"
                                        class="font-medium text-gray-700 hover:text-gray-700 underline underline-offset-2">
                                        Play Store
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span
                                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-stone-200 text-gray-700 font-medium mr-4">2</span>
                                <h3 class="font-medium text-md text-gray-700">Open the App</h3>
                            </div>
                            <div class="ml-12 space-y-1 text-sm">
                                <p class="text-gray-700">Choose one of these options:</p>
                                <p class="text-gray-700">• Tap the "+" button</p>
                                <p class="text-gray-700">• Select "Scan QR Code"</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span
                                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-stone-200 text-gray-700 font-medium mr-4">3</span>
                                <h3 class="font-medium text-md text-gray-700">Scan QR Code</h3>
                            </div>
                            <div class="ml-12 text-sm">
                                <p class="text-gray-700">Point your phone's camera at the QR code shown above</p>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-stone-200 rounded-lg">
                            <p class="text-sm text-gray-700 font-medium">
                                Need help? Contact our support team for assistance with setup.
                            </p>
                        </div>
                    </div>

                    <div class="container-border">
                        <div class="px-4 py-6 sm:p-8">
                            <div v-html="qrCodeSvg"></div>
                            <p class="mt-4 text-sm uppercase text-gray-800 font-medium">Point your camera here</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200" />

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="px-4 sm:px-0">
                        <h2 class="sub-heading">
                            Save Your Recovery Codes
                        </h2>
                        <div class="text-sm">
                            <p class="text-gray-700 my-2">
                                Store these recovery codes in a secure location. You'll need them to regain access if
                                you:
                            </p>
                            <ul class="list-disc text-gray-700 ml-4 mb-4 space-y-2">
                                <li>Lose your authentication device</li>
                                <li>Uninstall your authenticator app</li>
                                <li>Switch to a new phone</li>
                            </ul>
                            <div class="mt-6 p-4 bg-stone-200 rounded-lg">
                                <p class="text-sm text-gray-700 font-medium">
                                    Each code can only be used once. Keep them private and separate from your
                                    authentication device.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="container-border">
                        <div class="px-4 py-6 sm:p-8">
                            <ul>
                                <li v-for="code in recoveryCodes" :key="code" class="text-gray-800">
                                    {{ code }}
                                </li>
                            </ul>
                            <form @submit.prevent="regenerateCodes" class="mt-4">
                                <Button
                                    @click="regenerateCodes"
                                    :label="form.processing ? 'Regenerating...' : 'Regenerate codes'"
                                    severity="contrast"
                                    :disabled="form.processing"
                                    size="small"
                                />
                            </form>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200" />

                <div class="text-right">
                    <form @submit.prevent="disableTwoFactor">
                        <Button
                            @click="disableTwoFactor"
                            :label="form.processing ? 'Disabling...' : 'Disable 2FA'"
                            size="small"
                            :disabled="form.processing"
                            severity="danger"
                        />
                    </form>
                </div>
            </div>
        </div>
    </slot>
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
</script>

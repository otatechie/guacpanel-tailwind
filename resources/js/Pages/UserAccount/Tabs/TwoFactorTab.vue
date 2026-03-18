<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { DocumentDuplicateIcon } from '@heroicons/vue/24/outline'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const props = defineProps({
    user: { type: Object, required: true },
    qrCodeSvg: { type: String, default: null },
    recoveryCodes: { type: Array, default: () => [] },
    twoFactorEnabled: { type: Boolean, default: false },
})

const showDisableModal = ref(false)
const copied = ref(false)
const enableForm = useForm({})
const regenerateForm = useForm({})
const disableForm = useForm({})

const enableTwoFactor = () => enableForm.post(route('two-factor.enable'), { preserveScroll: true })
const regenerateCodes = () => regenerateForm.post(route('two-factor.recovery-codes'), { preserveScroll: true })
const disableTwoFactor = () => disableForm.delete(route('two-factor.disable'), { preserveScroll: true, onSuccess: () => { showDisableModal.value = false } })

const copyAllCodes = async () => {
    const text = props.recoveryCodes.join('\n')
    await navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}
</script>

<template>
    <div class="max-w-2xl space-y-5">
        <div>
            <h2 class="text-base font-semibold text-(--color-text)">Two-factor authentication</h2>
            <p class="mt-1 text-sm text-(--color-text-muted)">Add an extra security layer with an authenticator app</p>
        </div>

        <Alert v-if="!twoFactorEnabled" type="info">
            Two-factor authentication is disabled by your administrator.
        </Alert>

        <!-- Not enabled yet -->
        <template v-if="!user.two_factor_secret">
            <button
                @click="enableTwoFactor"
                :disabled="enableForm.processing || !twoFactorEnabled"
                class="btn btn-primary btn-sm"
                :aria-busy="enableForm.processing">
                {{ enableForm.processing ? 'Enabling...' : 'Enable two-factor' }}
            </button>
        </template>

        <!-- Enabled: setup + recovery codes -->
        <template v-else>
            <!-- QR code -->
            <div>
                <p class="text-base font-medium text-(--color-text)">Scan QR code</p>
                <p class="mt-1 text-sm text-(--color-text-muted)">Open your authenticator app and scan this code.</p>
                <div v-if="qrCodeSvg" class="mt-3 inline-block rounded-lg border border-(--card-border) bg-white p-3" v-html="qrCodeSvg" />
            </div>

            <!-- Recovery codes -->
            <div class="border-t border-(--card-border) pt-5">
                <p class="text-base font-medium text-(--color-text)">Recovery codes</p>
                <p class="mt-1 text-sm text-(--color-text-muted)">Save these codes somewhere safe. Each can only be used once.</p>

                <div v-if="recoveryCodes.length" class="mt-3 rounded-lg border border-(--card-border) bg-(--color-surface-muted)">
                    <div class="columns-2 gap-0 px-4 py-3 sm:columns-3">
                        <p
                            v-for="code in recoveryCodes"
                            :key="code"
                            class="py-1 font-mono text-sm tabular-nums text-(--color-text) select-all">
                            {{ code }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between border-t border-(--card-border) px-4 py-2.5">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-(--color-text) transition-colors hover:text-(--primary-color)"
                            @click="copyAllCodes">
                            <DocumentDuplicateIcon class="h-4 w-4" />
                            {{ copied ? 'Copied!' : 'Copy all' }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-secondary"
                            :disabled="regenerateForm.processing"
                            @click="regenerateCodes">
                            {{ regenerateForm.processing ? 'Generating...' : 'Regenerate' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Disable -->
            <div class="border-t border-red-200 pt-5 dark:border-red-900/30">
                <p class="text-base font-medium text-red-600 dark:text-red-400">Disable two-factor</p>
                <p class="mt-1 text-sm text-(--color-text-muted)">This removes 2FA protection from your account.</p>
                <button @click="showDisableModal = true" class="btn btn-danger btn-sm mt-3">Disable</button>
            </div>
        </template>
    </div>

    <Modal :show="showDisableModal" @close="showDisableModal = false" size="sm">
        <template #title>Disable two-factor</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">This immediately removes 2FA from your account. You can re-enable it later.</p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="showDisableModal = false">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" :disabled="disableForm.processing" @click="disableTwoFactor">
                    {{ disableForm.processing ? 'Disabling...' : 'Disable' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { CopyIcon } from '@lucide/vue'
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
const regenerateCodes = () =>
    regenerateForm.post(route('two-factor.recovery-codes'), { preserveScroll: true })
const disableTwoFactor = () =>
    disableForm.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            showDisableModal.value = false
        },
    })

const copyAllCodes = async () => {
    const text = props.recoveryCodes.join('\n')
    await navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => {
        copied.value = false
    }, 2000)
}
</script>

<template>
    <div class="max-w-2xl space-y-5">
        <div>
            <h2 class="text-foreground text-base font-semibold">Two-factor authentication</h2>
            <p class="text-muted-foreground mt-1 text-sm">
                Add an extra security layer with an authenticator app
            </p>
        </div>

        <Alert v-if="!twoFactorEnabled" type="info">
            Two-factor authentication is disabled by your administrator.
        </Alert>

        <!-- Not enabled yet -->
        <template v-if="!user.two_factor_secret">
            <Button
                variant="primary"
                size="sm"
                @click="enableTwoFactor"
                :disabled="enableForm.processing || !twoFactorEnabled"
                :aria-busy="enableForm.processing">
                {{ enableForm.processing ? 'Enabling...' : 'Enable two-factor' }}
            </Button>
        </template>

        <!-- Enabled: setup + recovery codes -->
        <template v-else>
            <!-- QR code -->
            <div>
                <p class="text-foreground text-base font-medium">Scan QR code</p>
                <p class="text-muted-foreground mt-1 text-sm">
                    Open your authenticator app and scan this code.
                </p>
                <div
                    v-if="qrCodeSvg"
                    class="border-border mt-3 inline-block rounded-lg border bg-white p-3"
                    v-html="qrCodeSvg" />
            </div>

            <!-- Recovery codes -->
            <div class="border-border border-t pt-5">
                <p class="text-foreground text-base font-medium">Recovery codes</p>
                <p class="text-muted-foreground mt-1 text-sm">
                    Save these codes somewhere safe. Each can only be used once.
                </p>

                <div
                    v-if="recoveryCodes.length"
                    class="border-border bg-muted mt-3 rounded-lg border">
                    <div class="columns-2 gap-0 px-4 py-3 sm:columns-3">
                        <p
                            v-for="code in recoveryCodes"
                            :key="code"
                            class="text-foreground py-1 font-mono text-sm tabular-nums select-all">
                            {{ code }}
                        </p>
                    </div>
                    <div
                        class="border-border flex items-center justify-between border-t px-4 py-2.5">
                        <button
                            type="button"
                            class="text-foreground hover:text-primary inline-flex items-center gap-1.5 text-sm font-medium transition-colors"
                            @click="copyAllCodes">
                            <CopyIcon class="h-4 w-4" />
                            {{ copied ? 'Copied!' : 'Copy all' }}
                        </button>
                        <Button
                            variant="secondary"
                            size="sm"
                            :disabled="regenerateForm.processing"
                            @click="regenerateCodes">
                            {{ regenerateForm.processing ? 'Generating...' : 'Regenerate' }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Disable -->
            <div class="border-t border-red-200 pt-5 dark:border-red-900/30">
                <p class="text-base font-medium text-red-600 dark:text-red-400">
                    Disable two-factor
                </p>
                <p class="text-muted-foreground mt-1 text-sm">
                    This removes 2FA protection from your account.
                </p>
                <Button variant="danger" size="sm" class="mt-3" @click="showDisableModal = true">
                    Disable
                </Button>
            </div>
        </template>
    </div>

    <Modal :show="showDisableModal" @close="showDisableModal = false" size="sm">
        <template #title>Disable two-factor</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                This immediately removes 2FA from your account. You can re-enable it later.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="showDisableModal = false">
                    Cancel
                </Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="disableForm.processing"
                    @click="disableTwoFactor">
                    {{ disableForm.processing ? 'Disabling...' : 'Disable' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>

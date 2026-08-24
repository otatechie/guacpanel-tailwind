<script setup>
import Button from '@/Components/Button.vue'
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

defineOptions({ layout: Auth })

const useRecovery = ref(false)

const codeForm = useForm({ code: '' })
const recoveryForm = useForm({ recovery_code: '' })

const submitCode = () => {
    codeForm.post('/two-factor-challenge', {
        preserveScroll: true,
        onSuccess: () => codeForm.reset(),
    })
}

const submitRecovery = () => {
    recoveryForm.post('/two-factor-challenge', {
        preserveScroll: true,
        onSuccess: () => recoveryForm.reset(),
    })
}
</script>

<template>
    <Head title="Two-factor challenge" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Two-factor authentication</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
                {{
                    useRecovery
                        ? 'Enter one of your emergency recovery codes'
                        : 'Enter the 6-digit code from your authenticator app'
                }}
            </p>
        </header>

        <!-- Authenticator code -->
        <form v-if="!useRecovery" class="mt-6 space-y-4" @submit.prevent="submitCode">
            <FormInput
                id="code"
                v-model="codeForm.code"
                placeholder=""
                label="Authentication code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                :show-required-marker="false"
                required
                :disabled="codeForm.processing"
                :error="codeForm.errors.code"
                maxlength="6"
                autocomplete="one-time-code" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="codeForm.processing"
                :aria-busy="codeForm.processing">
                {{ codeForm.processing ? 'Verifying...' : 'Verify' }}
            </Button>
        </form>

        <!-- Recovery code -->
        <form v-else class="mt-6 space-y-4" @submit.prevent="submitRecovery">
            <FormInput
                id="recovery_code"
                v-model="recoveryForm.recovery_code"
                placeholder=""
                label="Recovery code"
                type="text"
                :show-required-marker="false"
                required
                :disabled="recoveryForm.processing"
                :error="recoveryForm.errors.recovery_code"
                autocomplete="off" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="recoveryForm.processing"
                :aria-busy="recoveryForm.processing">
                {{ recoveryForm.processing ? 'Verifying...' : 'Verify' }}
            </Button>
        </form>

        <p class="text-muted-foreground mt-6 text-center text-sm">
            <button
                type="button"
                class="text-primary font-medium hover:underline"
                @click="useRecovery = !useRecovery">
                {{ useRecovery ? 'Use authenticator code instead' : 'Use a recovery code instead' }}
            </button>
        </p>
    </div>
</template>

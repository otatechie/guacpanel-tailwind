<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const codeForm = useForm({
    code: ''
})

const recoveryCodeForm = useForm({
    recovery_code: ''
})

const submitCode = () => {
    codeForm.post('/two-factor-challenge', {
        preserveScroll: true,
        onSuccess: () => {
            codeForm.reset()
        }
    })
}

const submitRecovery = () => {
    recoveryCodeForm.post('/two-factor-challenge', {
        preserveScroll: true,
        onSuccess: () => {
            recoveryCodeForm.reset()
        }
    })
}
</script>

<template>
    <Head title="Two-factor challenge" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">Two-factor authentication</h1>
        <p class="text-[var(--color-text-muted)] text-sm text-center font-medium mt-2">
            Please verify your identity to continue
        </p>

        <section class="mt-6 p-6 container-border rounded-xl">
            <section class="space-y-6">
                <form
                    class="space-y-4"
                    aria-labelledby="auth-code-title"
                    @submit.prevent="submitCode">
                    <span class="flex justify-center">
                        <span class="bg-[var(--color-surface-muted)] p-3 rounded-full">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6 text-gray-600 dark:text-gray-400"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </span>
                    </span>

                    <header class="text-center">
                        <h2 id="auth-code-title" class="font-medium text-[var(--color-text)]">
                            Authentication code
                        </h2>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1" role="note">
                            Enter the code from your authenticator app
                        </p>
                    </header>

                    <FormInput
                        id="code"
                        v-model="codeForm.code"
                        label="Code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        required
                        :disabled="codeForm.processing"
                        :error="codeForm.errors.code"
                        class="text-center"
                        maxlength="6"
                        autocomplete="one-time-code" />

                    <button
                        type="submit"
                        :disabled="codeForm.processing"
                        class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed h-10 rounded-lg"
                        aria-busy="codeForm.processing">
                        {{ codeForm.processing ? 'Verifying...' : 'Verify code' }}
                    </button>
                </form>

                <div role="separator" aria-label="or use recovery code" class="relative">
                    <hr class="border-t border-[var(--color-border)]" />
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 px-1 bg-[var(--color-surface)] text-[var(--color-text-muted)] text-xs">
                        or use recovery code
                    </span>
                </div>

                <form
                    class="space-y-4"
                    aria-labelledby="recovery-code-title"
                    @submit.prevent="submitRecovery">
                    <span class="flex justify-center">
                        <span class="bg-[var(--color-surface-muted)] p-3 rounded-full">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6 text-gray-600 dark:text-gray-400"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </span>
                    </span>

                    <header class="text-center">
                        <h2
                            id="recovery-code-title"
                            class="font-medium text-[var(--color-text)]">
                            Recovery code
                        </h2>
                    </header>

                    <FormInput
                        id="recovery_code"
                        v-model="recoveryCodeForm.recovery_code"
                        label="Recovery code"
                        type="text"
                        required
                        :disabled="recoveryCodeForm.processing"
                        :error="recoveryCodeForm.errors.recovery_code"
                        autocomplete="off" />

                    <button
                        type="submit"
                        :disabled="recoveryCodeForm.processing"
                        class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed h-10"
                        aria-busy="recoveryCodeForm.processing">
                        {{ recoveryCodeForm.processing ? 'Verifying...' : 'Use recovery code' }}
                    </button>
                </form>
            </section>
        </section>

        <p class="mt-8 text-center text-sm text-[var(--color-text-muted)]">
            Having trouble?
            <a
                href="#"
                class="link"
                aria-label="Contact support for help with two-factor authentication">
                Contact support
            </a>
        </p>
    </main>
</template>

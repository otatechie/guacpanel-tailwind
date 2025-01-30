<template>

    <Head title="Two-factor challenge" />

    <div class="flex min-h-[900px] flex-col">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="main-heading">
                Two-factor challenge
            </h2>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-[384px] px-8">
            <div
                class="container-border p-5">
                <form @submit.prevent="submitCode" class="space-y-3">
                    <div class="flex justify-center items-center mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-10 inline-block text-gray-800">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>

                    <p class="text-gray-800 text-sm">Enter the code from your authenticator app</p>

                    <FloatLabel variant="on">
                        <Password id="code" v-model="codeForm.code" required fluid :invalid="!!codeForm.errors.code"
                            :feedback="false" toggleMask />
                        <label for="code">Code</label>
                    </FloatLabel>
                    <p v-if="codeForm.errors.code" class="text-xs text-red-600">
                        {{ codeForm.errors.code }}
                    </p>

                    <Button @click="submitCode" :loading="codeForm.processing" label="Confirm authentication code"
                        size="small"
                        class="w-full btn-primary" />
                </form>

                <div class="relative mt-5">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm font-medium leading-6">
                        <span class="bg-white px-6 text-gray-500 text-xs">Or continue with</span>
                    </div>
                </div>

                <form @submit.prevent="submitRecovery" class="space-y-3 mt-6">
                    <div class="flex justify-center items-center mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-10 text-gray-800">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                        </svg>
                    </div>

                    <p class="text-gray-800 text-sm">Enter recovery code</p>

                    <FloatLabel variant="on">
                        <Password id="recovery_code" v-model="recoveryCodeForm.recovery_code" required fluid
                            :invalid="!!recoveryCodeForm.errors.recovery_code"
                            :feedback="false" toggleMask />
                        <label for="recovery_code">Code</label>
                    </FloatLabel>
                    <p v-if="recoveryCodeForm.errors.recovery_code" class="text-xs text-red-600">
                        {{ recoveryCodeForm.errors.recovery_code }}
                    </p>

                    <Button @click="submitRecovery" :loading="recoveryCodeForm.processing" label="Confirm recovery code"
                        size="small"
                        class="w-full container-border" />
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'

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

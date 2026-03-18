<script setup>
import { computed } from 'vue'
import { Form, Link, Head, useForm, router, usePage } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'

const page = usePage()
const success = computed(() => page.props.flash?.success)

defineOptions({
    layout: Auth,
})

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
})

const form = useForm({})

const returnToLogin = () => {
    form.post(route('logout'), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('login'))
        },
    })
}

const submit = () => {
    form.post(route('verification.send'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Email Verification" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Verify your email</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                We need to verify your email address before you can continue
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <div
                v-if="status === 'verification-link-sent'"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
                role="alert">
                A new verification link has been sent to your email address.
            </div>

            <Form
                v-if="status !== 'verification-link-sent'"
                @submit.prevent="submit"
                v-slot="{ processing }">
                <button
                    type="submit"
                    class="btn btn-primary w-full"
                    :aria-busy="form.processing"
                    :disabled="form.processing">
                    {{ form.processing ? 'Sending...' : 'Resend verification email' }}
                </button>
            </Form>

            <button
                @click="returnToLogin"
                type="button"
                class="btn btn-secondary w-full"
                :disabled="form.processing">
                Sign out
            </button>
        </div>
    </div>
</template>

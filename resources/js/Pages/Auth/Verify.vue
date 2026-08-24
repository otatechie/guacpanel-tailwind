<script setup>
import Button from '@/Components/Button.vue'
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
    <Head title="Email verification" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Verify your email</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
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
                <Button
                    variant="primary"
                    class="w-full"
                    type="submit"
                    :aria-busy="form.processing"
                    :disabled="form.processing">
                    {{ form.processing ? 'Sending...' : 'Resend verification email' }}
                </Button>
            </Form>

            <Button
                variant="secondary"
                class="w-full"
                @click="returnToLogin"
                :disabled="form.processing">
                Sign out
            </Button>
        </div>
    </div>
</template>

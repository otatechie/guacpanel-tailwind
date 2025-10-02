<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    name: '',
    email: ''
})

const submit = () => {
    form.post(route('magic.register'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        }
    })
}
</script>

<template>
    <Head title="Register with Magic Link" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">Register with Magic Link</h1>

        <form
            class="mt-6 container-border p-5 space-y-6"
            aria-labelledby="magic-link-form"
            @submit.prevent="submit">
            <p class="text-sm text-[var(--color-text-muted)]" role="note">
                Enter your details to create an account. We'll send you a secure login link - no
                password needed!
            </p>

            <FormInput
                id="name"
                v-model="form.name"
                label="Legal name"
                name="name"
                type="text"
                required
                autocomplete="name"
                :error="form.errors.name" />

            <FormInput
                id="email"
                v-model="form.email"
                label="Email"
                name="email"
                type="email"
                required
                autocomplete="email"
                :error="form.errors.email" />

            <p class="text-sm text-[var(--color-text-muted)]" role="note">
                By continuing, you agree to our
                <a href="#" class="underline font-medium" aria-label="Read Terms of Service">
                    Terms
                </a>
                and have read and acknowledge the
                <a href="#" class="underline font-medium" aria-label="Read Privacy Policy">
                    Global Privacy
                </a>
                Statement.
            </p>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full btn-primary"
                aria-busy="form.processing">
                {{ form.processing ? 'Sending...' : 'Register with magic link' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-[var(--color-text-muted)]">
            Prefer password login?
            <Link
                :href="route('login')"
                class="text-sm link"
                aria-label="Go to password login page">
                Login with password
            </Link>
        </p>
    </main>
</template>

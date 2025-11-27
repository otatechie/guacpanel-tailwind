<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'
import Socialite from '@/components/Socialite.vue'

defineOptions({
    layout: Auth
})

const props = defineProps({
    providersConfig: {
        type: Object,
        required: false
    },
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const { settings: { passwordlessLogin = true } = {} } = usePage().props

const smLogin = (() => {
    const providersConfig = props.providersConfig
    if (providersConfig.providers.length === 0) {
        return false
    }
    return true
})()

const submit = () => {
    form.post(route('register'))
}
</script>

<template>
    <Head title="Create account" />

    <div class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">Create account</h1>

        <form
            class="container-border p-5 space-y-6 mt-6"
            aria-labelledby="registration-form"
            @submit.prevent="submit">
            <FormInput
                id="name"
                v-model="form.name"
                label="Name"
                name="name"
                required
                :error="form.errors.name"
                autocomplete="name" />
            <FormInput
                id="email"
                v-model="form.email"
                label="Email"
                name="email"
                type="email"
                required
                :error="form.errors.email"
                autocomplete="email" />
            <FormInput
                id="password"
                v-model="form.password"
                label="Password"
                name="password"
                type="password"
                required
                :error="form.errors.password"
                autocomplete="new-password"
                aria-describedby="password-requirements" />
            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm password"
                name="password_confirmation"
                type="password"
                required
                :error="form.errors.password_confirmation"
                autocomplete="new-password" />

            <p class="text-sm text-[var(--color-text-muted)]" role="note">
                By creating an account, you agree to our
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
                {{ form.processing ? 'Creating account...' : 'Create account' }}
            </button>

            <template v-if="smLogin || passwordlessLogin">
                <div class="relative flex items-center" role="separator" aria-label="or separator">
                    <div class="w-full border-t border-[var(--color-border)]"></div>
                    <span
                        class="absolute left-1/2 -translate-x-1/2 px-2 bg-[var(--color-surface)] text-[var(--color-text-muted)] text-sm">
                        OR
                    </span>
                </div>
            </template>

            <template v-if="smLogin">
                <Socialite
                    :providers-config="providersConfig"
                 />
            </template>

            <template v-if="passwordlessLogin">
                <Link
                    :href="route('magic.create')"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 cursor-pointer text-sm btn-secondary"
                    role="button"
                    aria-label="Create account with magic link">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Create account with magic link</span>
                </Link>
                <p class="text-xs text-center text-[var(--color-text-muted)] mt-2" role="note">
                    No password needed - we'll send a secure login link to your email
                </p>
            </template>
        </form>

        <p class="my-8 text-center text-sm text-[var(--color-text-muted)]">
            Already a member?
            <Link :href="route('login')" class="text-sm link" aria-label="Go to login page">
                Login
            </Link>
        </p>
    </div>
</template>

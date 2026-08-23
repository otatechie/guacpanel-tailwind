<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Socialite from '@js/Components/Auth/Socialite.vue'

defineOptions({
    layout: Auth,
})

const props = defineProps({
    providersConfig: {
        type: Object,
        required: false,
    },
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
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

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Create account</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Get started with your free account
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="name"
                v-model="form.name"
                label="Full name"
                name="name"
                required
                :error="form.errors.name"
                autocomplete="name" />
            <FormInput
                id="email"
                v-model="form.email"
                label="Email address"
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
                autocomplete="new-password" />
            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm password"
                name="password_confirmation"
                type="password"
                required
                :error="form.errors.password_confirmation"
                autocomplete="new-password" />

            <p class="text-xs leading-relaxed text-(--color-text-muted)">
                By creating an account, you agree to our
                <a href="#" class="font-medium underline">Terms</a>
                and
                <a href="#" class="font-medium underline">Privacy Policy</a>.
            </p>

            <Button variant="primary" class="w-full" type="submit" :disabled="form.processing" :aria-busy="form.processing">
                {{ form.processing ? 'Creating account...' : 'Create account' }}
            </Button>
        </form>

        <template v-if="smLogin || passwordlessLogin">
            <div role="separator" class="relative my-6">
                <hr class="border-t border-(--color-border)" />
                <span
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-(--color-bg) px-3 text-xs text-(--color-text-muted)">
                    or continue with
                </span>
            </div>

            <div class="space-y-3">
                <Socialite v-if="smLogin" :providers-config="providersConfig" />

                <Button :as="Link" variant="secondary" class="flex w-full items-center justify-center gap-2 text-sm" v-if="passwordlessLogin" :href="route('magic.create')" role="button">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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
                    Sign up with magic link
                </Button>
            </div>
        </template>

        <p class="mt-8 text-center text-sm text-(--color-text-muted)">
            Already have an account?
            <Link :href="route('login')" class="font-medium text-(--primary-color) hover:underline">
                Sign in
            </Link>
        </p>
    </div>
</template>

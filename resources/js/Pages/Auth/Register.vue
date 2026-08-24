<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Socialite from '@js/Components/Auth/Socialite.vue'
import { MailIcon } from '@lucide/vue'
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
            <h1 class="text-foreground text-xl font-semibold">Create account</h1>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="name"
                v-model="form.name"
                placeholder=""
                label="Full name"
                name="name"
                :show-required-marker="false"
                required
                :error="form.errors.name"
                autocomplete="name" />
            <FormInput
                id="email"
                v-model="form.email"
                placeholder=""
                label="Email address"
                name="email"
                type="email"
                :show-required-marker="false"
                required
                :error="form.errors.email"
                autocomplete="email" />
            <FormInput
                id="password"
                v-model="form.password"
                placeholder=""
                label="Password"
                name="password"
                type="password"
                :show-required-marker="false"
                required
                :error="form.errors.password"
                autocomplete="new-password" />
            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                placeholder=""
                label="Confirm password"
                name="password_confirmation"
                type="password"
                :show-required-marker="false"
                required
                :error="form.errors.password_confirmation"
                autocomplete="new-password" />

            <p class="text-muted-foreground text-xs leading-relaxed">
                By creating an account, you agree to our
                <a href="#" class="font-medium underline">Terms</a>
                and
                <a href="#" class="font-medium underline">Privacy Policy</a>
                .
            </p>

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Creating account...' : 'Create account' }}
            </Button>
        </form>

        <template v-if="smLogin || passwordlessLogin">
            <div role="separator" class="relative my-6">
                <hr class="border-border border-t" />
                <span
                    class="bg-card text-muted-foreground absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 px-3 text-xs">
                    or continue with
                </span>
            </div>

            <div class="space-y-3">
                <Socialite v-if="smLogin" :providers-config="providersConfig" icons-only />

                <Button
                    v-if="passwordlessLogin"
                    :as="Link"
                    variant="secondary"
                    class="w-full"
                    :href="route('magic.create')">
                    <MailIcon class="h-4 w-4" aria-hidden="true" />
                    Sign up with magic link
                </Button>
            </div>
        </template>

        <p class="text-muted-foreground mt-8 text-center text-sm">
            Already have an account?
            <Link :href="route('login')" class="text-primary font-medium hover:underline">
                Sign in
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    password: ''
})

const submit = () => {
    form.post(route('password.confirm'))
}
</script>

<template>
    <Head title="Confirm password" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">Confirm access</h1>

        <form
            class="mt-6 container-border p-5 space-y-6"
            aria-labelledby="confirm-password-form"
            @submit.prevent="submit">
            <p class="text-[var(--color-text-muted)] text-sm" role="note">
                Enter password to confirm access
            </p>

            <FormInput
                id="password"
                v-model="form.password"
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                :disabled="form.processing"
                :error="form.errors.password" />

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full btn-primary h-11"
                aria-busy="form.processing">
                {{ form.processing ? 'Confirming...' : 'Confirm password' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-[var(--color-text-muted)]">
            Back to
            <Link :href="route('home')" class="text-sm link" aria-label="Return to dashboard">
                dashboard
            </Link>
        </p>
    </main>
</template>

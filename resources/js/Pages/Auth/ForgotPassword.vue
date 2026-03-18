<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

defineOptions({
    layout: Auth,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.request'))
}
</script>

<template>
    <Head title="Forgot password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Reset password</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Enter your email and we'll send a reset link
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="email"
                v-model="form.email"
                label="Email address"
                name="email"
                type="email"
                required
                autocomplete="email"
                :error="form.errors.email" />

            <button
                type="submit"
                :disabled="form.processing"
                class="btn btn-primary w-full"
                :aria-busy="form.processing">
                {{ form.processing ? 'Sending...' : 'Send reset link' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-(--color-text-muted)">
            <Link :href="route('login')" class="font-medium text-(--primary-color) hover:underline">
                Back to sign in
            </Link>
        </p>
    </div>
</template>

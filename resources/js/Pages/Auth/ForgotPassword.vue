<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    email: ''
})

const submit = () => {
    form.post(route('password.request'))
}
</script>


<template>

    <Head title="Forgot password" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center dark:text-white">
            Forgot password
        </h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6" aria-labelledby="reset-form">
            <p class="text-gray-600 dark:text-white text-sm" role="note">
                Enter your email to receive a password reset link
            </p>

            <FormInput v-model="form.email" label="Email" name="email" id="email" type="email" required
                autocomplete="email" :error="form.errors.email" />

            <button type="submit" :disabled="form.processing" class="w-full btn-primary" aria-busy="form.processing">
                {{ form.processing ? 'Please wait...' : 'Send reset email' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-white">
            Back to
            <Link :href="route('login')" class="text-sm link" aria-label="Return to login page">
            login
            </Link>
        </p>
    </main>
</template>

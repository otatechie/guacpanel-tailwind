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
    email: '',
})

const submit = () => {
    form.post(route('magic.register'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>


<template>

    <Head title="Register with Magic Link" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center dark:text-white">
            Register with Magic Link
        </h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6" aria-labelledby="magic-link-form">
            <p class="text-sm text-gray-500 dark:text-gray-300" role="note">
                Enter your details to create an account. We'll send you a secure login link - no password needed!
            </p>

            <FormInput v-model="form.name" label="Legal name" name="name" id="name" type="text" required
                autocomplete="name" :error="form.errors.name" />

            <FormInput v-model="form.email" label="Email" name="email" id="email" type="email" required
                autocomplete="email" :error="form.errors.email" />

            <p class="text-sm text-gray-500 dark:text-gray-300" role="note">
                By continuing, you agree to our
                <a href="#" class="underline font-medium" aria-label="Read Terms of Service">Terms</a> and have
                read and acknowledge the
                <a href="#" class="underline font-medium" aria-label="Read Privacy Policy">Global Privacy</a> Statement.
            </p>

            <button type="submit" :disabled="form.processing" class="w-full btn-primary" aria-busy="form.processing">
                {{ form.processing ? 'Sending...' : 'Register with magic link' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-white">
            Prefer password login?
            <Link :href="route('login')" class="text-sm link" aria-label="Go to password login page">
            Login with password
            </Link>
        </p>
    </main>
</template>

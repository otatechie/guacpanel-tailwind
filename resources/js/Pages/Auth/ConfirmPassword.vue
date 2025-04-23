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
        <h1 class="main-heading text-center dark:text-white">
            Confirm access
        </h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6"
            aria-labelledby="confirm-password-form">
            <p class="text-gray-800 dark:text-white text-sm" role="note">
                Enter password to confirm access
            </p>

            <FormInput v-model="form.password" label="Password" id="password" name="password" type="password" required
                autocomplete="current-password" :disabled="form.processing" :error="form.errors.password" />

            <button type="submit" :disabled="form.processing" class="w-full btn-primary h-11"
                aria-busy="form.processing">
                {{ form.processing ? 'Confirming...' : 'Confirm password' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-700 dark:text-gray-300">
            Back to
            <Link :href="route('home')" class="text-sm link" aria-label="Return to dashboard">
            dashboard
            </Link>
        </p>
    </main>
</template>

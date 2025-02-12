<template>

    <Head title="Forgot password" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">
            Forgot password
        </h2>

        <div class="mt-6 p-5 container-border">
            <p class="text-gray-800 text-sm">
                Enter your email to receive a password reset link
            </p>

            <form @submit.prevent="submit" class="space-y-6 mt-4">
                <FormInput v-model="form.email" label="Email" id="email" type="email" required
                    :error="form.errors.email" />

                <button @click="submit" :disabled="form.processing"
                    class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ form.processing ? 'Please wait...' : 'Send reset email' }}
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-800 dark:text-white">
            Back to
            <Link :href="route('login')" class="text-sm link">
            login
            </Link>
        </p>
    </div>
</template>

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

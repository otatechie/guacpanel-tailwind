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

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">
           Register with Magic Link
        </h2>

        <div class="mt-6 container-border p-5">
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">
                Enter your details to create an account. We'll send you a secure login link - no password needed!
            </p>

            <form @submit.prevent="submit" class="space-y-6 mt-2">
                <FormInput v-model="form.name" label="Legal name" id="name" type="text" required
                    :error="form.errors.name" />

                <FormInput v-model="form.email" label="Email" id="email" type="email" required
                    :error="form.errors.email" />

                <p class="text-sm text-gray-500 dark:text-gray-300">
                    By continuing, you agree to our <span class="underline font-medium">Terms</span> and have
                    read and acknowledge the <span class="underline font-medium">Global Privacy</span> Statement.
                </p>

                <button type="submit" :disabled="form.processing" class="w-full btn-primary">
                    {{ form.processing ? 'Sending...' : 'Register with magic link' }}
                </button>
            </form>
        </div>

        <p class="my-8 text-center text-sm text-gray-800 dark:text-white">
            Prefer password login?
            <Link :href="route('login')" class="text-sm link">
            Login with password
            </Link>
        </p>
    </div>
</template>


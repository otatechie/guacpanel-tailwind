<template>
    <Head title="Create account" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center dark:text-white">Create account</h2>
        <div class="mt-6 container-border p-5">
            <form @submit.prevent="submit" class="space-y-6 mt-2">
                <FormInput
                    v-model="form.name"
                    label="Legal name"
                    name="name"
                    id="name"
                    required
                    :error="form.errors.name"
                />
                <FormInput
                    v-model="form.email"
                    label="Email"
                    name="email"
                    id="email"
                    type="email"
                    required
                    :error="form.errors.email"
                />
                <FormInput
                    v-model="form.password"
                    label="Password"
                    name="password"
                    id="password"
                    type="password"
                    required
                    :error="form.errors.password"
                />
                <FormInput
                    v-model="form.password_confirmation"
                    label="Confirm password"
                    name="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    required
                    :error="form.errors.password_confirmation"
                />

                <p class="text-sm text-gray-500 dark:text-gray-300">
                    By creating an account, you agree to our <span class="underline font-medium">Terms</span> and have
                    read and acknowledge the <span class="underline font-medium">Global Privacy</span> Statement.
                </p>

                <button type="submit" :disabled="form.processing" class="w-full btn-primary">
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </button>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-900 text-gray-500">OR</span>
                    </div>
                </div>

                <Link
                    :href="route('magic.register.create')"
                    class="w-full flex items-center justify-center gap-2 btn-secondary dark:hover:bg-gray-800 transition-colors cursor-pointer hover:text-purple-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Create account with magic link</span>
                </Link>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    We'll send a secure login link to your email
                </p>
            </form>
        </div>

        <p class="my-8 text-center text-sm text-gray-800 dark:text-white">
            Already a member?
            <Link :href="route('login')" class="text-sm link">Login</Link>
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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'))
}
</script>

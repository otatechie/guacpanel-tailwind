<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const submit = () => {
    form.post(route('user.password.expired.update'))
}
</script>

<template>

    <Head title="Password Update Required" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center dark:text-white">
            Password update required
        </h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6">
            <section class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md" aria-labelledby="password-requirements">
                <h2 id="password-requirements" class="text-gray-400 dark:text-gray-400 text-sm mb-2">
                    Password must include:
                </h2>
                <ul class="text-gray-400 dark:text-gray-400 space-y-1 list-disc pl-5 text-sm">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </section>

            <FormInput v-model="form.current_password" label="Current Password" id="current_password" type="password"
                required :disabled="form.processing" :error="form.errors.current_password" />

            <FormInput v-model="form.password" label="Password" id="password" type="password" required
                :disabled="form.processing" :error="form.errors.password" aria-describedby="password-requirements" />

            <FormInput v-model="form.password_confirmation" label="Confirm Password" id="password_confirmation"
                type="password" required :disabled="form.processing" :error="form.errors.password_confirmation" />

            <button type="submit" :disabled="form.processing" class="w-full btn-primary" aria-busy="form.processing">
                {{ form.processing ? 'Please wait...' : 'Update password' }}
            </button>
        </form>

        <footer class="mt-8 text-center text-sm text-gray-700">
            Having trouble?
            <Link :href="route('home')" class="text-sm link">
            Contact support
            </Link>
        </footer>
    </main>
</template>

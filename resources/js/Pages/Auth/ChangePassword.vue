<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const props = defineProps({
    user: Object,
})

const form = useForm({
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('user.password.change.update'), {
        preserveScroll: true
    })
}
</script>


<template>

    <Head title="Reset password" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">
            Change your password
        </h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6"
            aria-labelledby="change-password-form">
            <section class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md" aria-labelledby="password-requirements">
                <h2 id="password-requirements" class="text-gray-500 dark:text-gray-400 text-sm mb-2">
                    Password must include:
                </h2>
                <ul class="text-gray-500 dark:text-gray-400 space-y-1 list-disc pl-5 text-sm">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </section>

            <FormInput v-model="form.password" label="Password" id="password" type="password" required
                autocomplete="new-password" :error="form.errors.password" aria-describedby="password-requirements" />

            <FormInput v-model="form.password_confirmation" label="Confirm password" id="password_confirmation"
                type="password" required autocomplete="new-password" :error="form.errors.password_confirmation" />

            <button type="submit" class="w-full btn-primary" :disabled="form.processing" :aria-busy="form.processing">
                {{ form.processing ? 'Please wait...' : 'Change password' }}
            </button>
        </form>
    </main>
</template>

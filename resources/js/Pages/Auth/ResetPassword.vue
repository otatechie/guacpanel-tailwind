<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

const props = defineProps({
    token: String,
    email: String
})

defineOptions({
    layout: Auth
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
})

const submit = () => {
    form.post(route('password.update'))
}
</script>

<template>
    <Head title="Reset password" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center">Reset your password</h1>

        <form
            class="mt-6 container-border p-5 space-y-6"
            aria-labelledby="reset-password-form"
            @submit.prevent="submit">
            <section
                class="bg-[var(--color-surface-muted)] p-4 rounded-md"
                aria-labelledby="password-requirements">
                <h2
                    id="password-requirements"
                    class="text-[var(--color-text-muted)] text-sm mb-2">
                    Password must include:
                </h2>
                <ul class="text-[var(--color-text-muted)] space-y-1 list-disc pl-5 text-sm">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </section>

            <input type="hidden" name="token" :value="form.token" />
            <input type="hidden" name="email" :value="form.email" />

            <FormInput
                id="password"
                v-model="form.password"
                label="Password"
                type="password"
                required
                autocomplete="new-password"
                :error="form.errors.password"
                aria-describedby="password-requirements" />

            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm password"
                type="password"
                required
                autocomplete="new-password"
                :error="form.errors.password_confirmation" />

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full btn-primary"
                aria-busy="form.processing">
                {{ form.processing ? 'Please wait...' : 'Reset password' }}
            </button>
        </form>
    </main>
</template>

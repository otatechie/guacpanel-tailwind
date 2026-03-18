<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

const props = defineProps({
    token: String,
    email: String,
})

defineOptions({
    layout: Auth,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.update'))
}
</script>

<template>
    <Head title="Reset password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Set a new password</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Choose a strong password for your account
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <ul
                class="rounded-lg border border-(--color-border) bg-(--color-surface-muted) px-4 py-3 text-xs leading-relaxed text-(--color-text-muted)"
                aria-label="Password requirements">
                <li>At least 8 characters</li>
                <li>One uppercase letter, one number, one special character</li>
            </ul>

            <input type="hidden" name="token" :value="form.token" />
            <input type="hidden" name="email" :value="form.email" />

            <FormInput
                id="password"
                v-model="form.password"
                label="New password"
                type="password"
                required
                autocomplete="new-password"
                :error="form.errors.password" />

            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                required
                autocomplete="new-password"
                :error="form.errors.password_confirmation" />

            <button
                type="submit"
                :disabled="form.processing"
                class="btn btn-primary w-full"
                :aria-busy="form.processing">
                {{ form.processing ? 'Resetting...' : 'Reset password' }}
            </button>
        </form>
    </div>
</template>

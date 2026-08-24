<script setup>
import Button from '@/Components/Button.vue'
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
            <h1 class="text-foreground text-xl font-semibold">Set a new password</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
                Choose a strong password for your account
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <ul
                class="border-border bg-muted text-muted-foreground rounded-lg border px-4 py-3 text-xs leading-relaxed"
                aria-label="Password requirements">
                <li>At least 8 characters</li>
                <li>One uppercase letter, one number, one special character</li>
            </ul>

            <input type="hidden" name="token" :value="form.token" />
            <input type="hidden" name="email" :value="form.email" />

            <FormInput
                id="password"
                v-model="form.password"
                placeholder=""
                label="New password"
                type="password"
                :show-required-marker="false"
                required
                autocomplete="new-password"
                :error="form.errors.password" />

            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                placeholder=""
                label="Confirm new password"
                type="password"
                :show-required-marker="false"
                required
                autocomplete="new-password"
                :error="form.errors.password_confirmation" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Resetting...' : 'Reset password' }}
            </Button>
        </form>
    </div>
</template>

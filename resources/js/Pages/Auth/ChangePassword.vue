<script setup>
import Button from '@/Components/Button.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

defineOptions({
    layout: Auth,
})

defineProps({
    user: Object,
})

const form = useForm({
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('user.password.change.update'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Change password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Change your password</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
                Your password has expired or needs to be changed
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <ul
                class="border-border bg-muted text-muted-foreground rounded-lg border px-4 py-3 text-xs leading-relaxed"
                aria-label="Password requirements">
                <li>At least 8 characters</li>
                <li>One uppercase letter, one number, one special character</li>
            </ul>

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
                {{ form.processing ? 'Updating...' : 'Update password' }}
            </Button>
        </form>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

defineOptions({
    layout: Auth,
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
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Change password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Change your password</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Your password has expired or needs to be changed
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <ul
                class="rounded-lg border border-(--color-border) bg-(--color-surface-muted) px-4 py-3 text-xs leading-relaxed text-(--color-text-muted)"
                aria-label="Password requirements">
                <li>At least 8 characters</li>
                <li>One uppercase letter, one number, one special character</li>
            </ul>

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

            <Button variant="primary" class="w-full" type="submit" :disabled="form.processing" :aria-busy="form.processing">
                {{ form.processing ? 'Updating...' : 'Update password' }}
            </Button>
        </form>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { useForm } from '@inertiajs/vue3'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineProps({
    passwordEnabled: { type: Boolean, default: false },
})

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submit = () =>
    form.put('/user/password', { preserveScroll: true, onSuccess: () => form.reset() })
</script>

<template>
    <div class="max-w-md">
        <h2 class="text-foreground text-base font-medium">Password</h2>
        <p class="text-muted-foreground mt-1 text-sm">
            Update your password to keep your account secure
        </p>

        <Alert v-if="!passwordEnabled" type="info" class="mt-4">
            Password changes are disabled by your administrator.
        </Alert>

        <form class="mt-4 space-y-4" @submit.prevent="submit">
            <FormInput
                v-model="form.current_password"
                label="Current password"
                type="password"
                autocomplete="current-password"
                :error="form.errors.current_password"
                required />
            <FormInput
                v-model="form.password"
                label="New password"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password"
                required />
            <FormInput
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password_confirmation"
                required />

            <div class="pt-2">
                <Button
                    variant="primary"
                    size="sm"
                    type="submit"
                    :disabled="form.processing || !passwordEnabled">
                    {{ form.processing ? 'Updating...' : 'Update password' }}
                </Button>
            </div>
        </form>
    </div>
</template>

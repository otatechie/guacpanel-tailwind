<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
    layout: Auth,
})

const form = useForm({
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('user.password.expired.update'), {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Password update required" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Password update required</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
                Your password has expired. Choose a new one to carry on.
            </p>
        </header>

        <Alert v-if="$page.props.flash.warning" type="warning" class="mt-4">
            {{ $page.props.flash.warning }}
        </Alert>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="password"
                v-model="form.password"
                label="New password"
                type="password"
                autocomplete="new-password"
                required
                :disabled="form.processing"
                :error="form.errors.password"
                aria-describedby="password-requirements" />

            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                autocomplete="new-password"
                required
                :disabled="form.processing"
                :error="form.errors.password_confirmation" />

            <!-- Mirrors the rule in UserAccountController::updateExpiredPassword.
                 The old list promised only an uppercase letter and never mentioned
                 the reuse check, so the server rejected passwords the page had
                 called valid. -->
            <div id="password-requirements" class="text-muted-foreground text-xs">
                <p>Your new password needs:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-4">
                    <li>8 characters or more</li>
                    <li>Upper and lower case letters</li>
                    <li>A number</li>
                    <li>A symbol</li>
                    <li>To be different from your current password</li>
                </ul>
            </div>

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Updating password...' : 'Update password' }}
            </Button>
        </form>

        <footer class="text-muted-foreground mt-8 text-center text-sm">
            Having trouble?
            <Link :href="route('home')" class="link text-sm">Contact support</Link>
        </footer>
    </div>
</template>

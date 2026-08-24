<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

defineOptions({
    layout: Auth,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.request'))
}
</script>

<template>
    <Head title="Forgot password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Reset password</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">
                Enter your email and we'll send a reset link
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="email"
                v-model="form.email"
                placeholder=""
                label="Email address"
                name="email"
                type="email"
                :show-required-marker="false"
                required
                autocomplete="email"
                :error="form.errors.email" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Sending...' : 'Send reset link' }}
            </Button>
        </form>

        <p class="text-muted-foreground mt-8 text-center text-sm">
            <Link :href="route('login')" class="text-primary font-medium hover:underline">
                Back to sign in
            </Link>
        </p>
    </div>
</template>

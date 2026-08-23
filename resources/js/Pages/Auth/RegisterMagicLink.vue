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
    name: '',
    email: '',
})

const submit = () => {
    form.post(route('magic.register'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Register with magic link" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-xl font-semibold text-foreground">Create account</h1>
            <p class="mt-1.5 text-sm text-muted-foreground">
                No password needed. We'll email you a secure sign-in link.
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="name"
                v-model="form.name"
                placeholder=""
                label="Full name"
                name="name"
                type="text"
                :show-required-marker="false"
                required
                autocomplete="name"
                :error="form.errors.name" />

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

            <p class="text-xs leading-relaxed text-muted-foreground">
                By creating an account, you agree to our
                <a href="#" class="font-medium underline">Terms</a>
                and
                <a href="#" class="font-medium underline">Privacy Policy</a>.
            </p>

            <Button variant="primary" class="w-full" type="submit" :disabled="form.processing" :aria-busy="form.processing">
                {{ form.processing ? 'Sending...' : 'Send magic link' }}
            </Button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
            Prefer a password?
            <Link :href="route('register')" class="font-medium text-primary hover:underline">
                Sign up with password
            </Link>
        </p>
    </div>
</template>

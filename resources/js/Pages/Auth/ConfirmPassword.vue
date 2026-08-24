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
    password: '',
})

const submit = () => {
    form.post(route('password.confirm'))
}
</script>

<template>
    <Head title="Confirm access" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-foreground text-xl font-semibold">Confirm access</h1>
            <p class="text-muted-foreground mt-1.5 text-sm">Re-enter your password to continue</p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="password"
                v-model="form.password"
                placeholder=""
                label="Password"
                name="password"
                type="password"
                :show-required-marker="false"
                required
                autocomplete="current-password"
                :disabled="form.processing"
                :error="form.errors.password" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Confirming...' : 'Confirm' }}
            </Button>
        </form>

        <p class="text-muted-foreground mt-8 text-center text-sm">
            <Link :href="route('home')" class="text-primary font-medium hover:underline">
                Back to dashboard
            </Link>
        </p>
    </div>
</template>

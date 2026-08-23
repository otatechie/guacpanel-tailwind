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
    <Head title="Confirm password" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-xl font-semibold text-foreground">Confirm access</h1>
            <p class="mt-1.5 text-sm text-muted-foreground">
                Re-enter your password to continue
            </p>
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

            <Button variant="primary" class="w-full" type="submit" :disabled="form.processing" :aria-busy="form.processing">
                {{ form.processing ? 'Confirming...' : 'Confirm' }}
            </Button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
            <Link :href="route('home')" class="font-medium text-primary hover:underline">
                Back to dashboard
            </Link>
        </p>
    </div>
</template>

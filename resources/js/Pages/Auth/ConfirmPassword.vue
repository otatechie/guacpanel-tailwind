<script setup>
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
            <h1 class="text-2xl font-bold text-(--color-text)">Confirm access</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Re-enter your password to continue
            </p>
        </header>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="password"
                v-model="form.password"
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                :disabled="form.processing"
                :error="form.errors.password" />

            <button
                type="submit"
                :disabled="form.processing"
                class="btn btn-primary w-full"
                :aria-busy="form.processing">
                {{ form.processing ? 'Confirming...' : 'Confirm' }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-(--color-text-muted)">
            <Link :href="route('home')" class="font-medium text-(--primary-color) hover:underline">
                Back to dashboard
            </Link>
        </p>
    </div>
</template>

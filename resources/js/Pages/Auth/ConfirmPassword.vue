<template>

    <Head title="Confirm password" />

    <div class="max-w-[384px] mx-auto px-8">
        <Logo />

        <h2 class="main-heading text-center">
            Confirm access
        </h2>

        <div class="mt-6 p-5 container-border">
            <p class="text-gray-700 text-sm mb-4">
                Enter password to confirm access
            </p>

            <form @submit.prevent="submit" class="space-y-4">
                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :disabled="form.processing" :error="form.errors.password" />

                <button @click="submit" :disabled="form.processing"
                    class="w-full btn-primary h-11 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ form.processing ? 'Confirming...' : 'Confirm password' }}
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-700">
            Back to
            <Link :href="route('home')" class="text-sm link">
            dashboard
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Logo from '../../Shared/Logo.vue'
import FormInput from '../../Components/FormInput.vue'

const form = useForm({
    password: ''
})

const submit = () => {
    form.post(route('password.confirm'))
}
</script>

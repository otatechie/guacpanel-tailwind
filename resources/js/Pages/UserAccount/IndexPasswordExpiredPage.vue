<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import { InfoIcon } from '@lucide/vue'

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

    <main class="mx-auto max-w-[384px] px-8" role="main">
        <h1 class="main-heading text-center dark:text-white">Password update required</h1>
        <div
            v-if="$page.props.flash.warning"
            class="my-4 flex items-center rounded-lg bg-orange-100 p-4 text-orange-700">
            <div
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-500">
                <InfoIcon class="h-5 w-5" aria-hidden="true" />
                <span class="sr-only">Warning icon</span>
            </div>
            <div class="ml-3 text-sm font-normal">
                <p>{{ $page.props.flash.warning }}</p>
            </div>
        </div>

        <form class="card mt-6 space-y-6 p-5" @submit.prevent="submit">
            <section
                class="rounded-md bg-gray-50 p-4 dark:bg-gray-800"
                aria-labelledby="password-requirements">
                <h2
                    id="password-requirements"
                    class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                    Password must include:
                </h2>
                <ul class="list-disc space-y-1 pl-5 text-sm text-gray-500 dark:text-gray-400">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </section>

            <FormInput
                id="password"
                v-model="form.password"
                label="New password"
                type="password"
                required
                :disabled="form.processing"
                :error="form.errors.password"
                aria-describedby="password-requirements" />

            <FormInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                required
                :disabled="form.processing"
                :error="form.errors.password_confirmation" />

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                aria-busy="form.processing">
                {{ form.processing ? 'Updating password...' : 'Update password' }}
            </Button>
        </form>

        <footer class="mt-8 text-center text-sm text-gray-700 dark:text-gray-300">
            Having trouble?
            <Link :href="route('home')" class="link text-sm">Contact support</Link>
        </footer>
    </main>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

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

  <main class="mx-auto max-w-[384px] px-8" role="main">
    <h1 class="main-heading text-center">Forgot password</h1>

    <form
      class="container-border mt-6 space-y-6 p-5"
      aria-labelledby="reset-form"
      @submit.prevent="submit">
      <p class="text-sm text-[var(--color-text-muted)]" role="note">
        Enter your email to receive a password reset link
      </p>

      <FormInput
        id="email"
        v-model="form.email"
        label="Email"
        name="email"
        type="email"
        required
        autocomplete="email"
        :error="form.errors.email" />

      <button
        type="submit"
        :disabled="form.processing"
        class="btn-primary w-full"
        aria-busy="form.processing">
        {{ form.processing ? 'Please wait...' : 'Send reset email' }}
      </button>
    </form>

    <p class="mt-8 text-center text-sm text-[var(--color-text-muted)]">
      Back to
      <Link :href="route('login')" class="link text-sm" aria-label="Return to login page">
        login
      </Link>
    </p>
  </main>
</template>

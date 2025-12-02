<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
  layout: Auth,
})

const props = defineProps({
  user: Object,
})

const form = useForm({
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('user.password.change.update'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Reset password" />

  <main class="mx-auto max-w-[384px] px-8" role="main">
    <h1 class="main-heading text-center">Change your password</h1>

    <form
      class="container-border mt-6 space-y-6 p-5"
      aria-labelledby="change-password-form"
      @submit.prevent="submit">
      <section
        class="rounded-md bg-[var(--color-surface-muted)] p-4"
        aria-labelledby="password-requirements">
        <h2 id="password-requirements" class="mb-2 text-sm text-[var(--color-text-muted)]">
          Password must include:
        </h2>
        <ul class="list-disc space-y-1 pl-5 text-sm text-[var(--color-text-muted)]">
          <li>8+ characters</li>
          <li>One uppercase letter</li>
          <li>One number</li>
          <li>One special character</li>
        </ul>
      </section>

      <FormInput
        id="password"
        v-model="form.password"
        label="Password"
        type="password"
        required
        autocomplete="new-password"
        :error="form.errors.password"
        aria-describedby="password-requirements" />

      <FormInput
        id="password_confirmation"
        v-model="form.password_confirmation"
        label="Confirm password"
        type="password"
        required
        autocomplete="new-password"
        :error="form.errors.password_confirmation" />

      <button
        type="submit"
        class="btn-primary w-full"
        :disabled="form.processing"
        :aria-busy="form.processing">
        {{ form.processing ? 'Please wait...' : 'Change password' }}
      </button>
    </form>
  </main>
</template>

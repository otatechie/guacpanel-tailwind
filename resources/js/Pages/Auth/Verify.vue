<script setup>
import { computed } from 'vue'
import { Form, Link, Head, useForm, router, usePage } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

const page = usePage()
const success = computed(() => page.props.flash?.success)

defineOptions({
  layout: Auth,
})

const props = defineProps({
  status: {
    type: String,
    default: null,
  },
})

const form = useForm({})

const returnToLogin = () => {
  form.post(route('logout'), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('login'))
    },
    onError: () => {
      //
    },
  })
}

const submit = () => {
  form.post(route('verification.send'), {
    preserveScroll: true,
    onSuccess: () => {
      //
    },
    onError: () => {
      //
    },
  })
}
</script>

<template>
  <Head title="Email Verification" />

  <main class="mx-auto max-w-[384px] px-8" role="main">
    <h1 class="main-heading text-center">Email Verification</h1>
    <section class="container-border mt-6 rounded-xl p-6">
      <section class="space-y-4">
        <span class="flex justify-center">
          <span class="rounded-full bg-[var(--color-surface-muted)] p-3">
            <svg
              v-if="status === 'verification-link-sent'"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6 text-gray-600 dark:text-gray-400"
              aria-hidden="true">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>

            <svg
              v-if="status != 'verification-link-sent'"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6 text-gray-600 dark:text-gray-400">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
            </svg>
          </span>
        </span>

        <header class="text-center">
          <h2 id="auth-code-title" class="font-medium text-[var(--color-text)]">
            Verification Required
          </h2>
          <p
            class="mt-1 text-sm text-[var(--color-text-muted)]"
            role="note"
            v-if="status != 'verification-link-sent'">
            Please verify your email to continue
          </p>
        </header>

        <div class="space-y-6">
          <Form
            @submit.prevent="submit"
            v-slot="{ processing }"
            v-if="status != 'verification-link-sent'">
            <div class="my-6 flex items-center justify-start">
              <button
                type="submit"
                class="btn btn-primary btn-sm w-full disabled:cursor-not-allowed"
                aria-busy="form.processing"
                :disabled="form.processing">
                {{ form.processing ? 'Sending verification email' : 'Resend verification email' }}
              </button>
            </div>
          </Form>
        </div>
        <div
          v-if="status === 'verification-link-sent'"
          class="mt-4 mb-6 text-center text-sm font-medium text-green-600">
          A new verification link has been sent to the email address you provided during
          registration.
        </div>
      </section>
      <button
        @click="returnToLogin"
        type="button"
        class="btn btn-secondary btn-outline btn-sm w-full"
        aria-busy="form.processing"
        :disabled="form.processing">
        Logout
      </button>
    </section>
  </main>
</template>

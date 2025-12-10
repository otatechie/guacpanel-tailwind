<script setup>
import { ref, onMounted, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Socialite from '@js/Components/Auth/Socialite.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
  layout: Auth,
})

const props = defineProps({
  status: {
    type: String,
    default: null,
  },
  canResetPassword: {
    type: Boolean,
    default: false,
  },
  canRegister: {
    type: Boolean,
    default: false,
  },
  providersConfig: {
    type: Object,
    required: false,
  },
  demo: {
    type: Object,
    default: () => ({
      enabled: false,
      username: '',
      password: '',
    }),
  },
})

const page = usePage()

const { settings: { passwordlessLogin = true } = {} } = page.props

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

onMounted(() => {
  if (props.demo.enabled) {
    form.email = props.demo.username
    form.password = props.demo.password
  }
})

watch(
  () => props.demo,
  newDemo => {
    if (newDemo && newDemo.enabled) {
      form.email = newDemo.username
      form.password = newDemo.password
    }
  },
  { deep: true }
)

const smLogin = (() => {
  const providersConfig = props.providersConfig
  if (providersConfig.providers.length === 0) {
    return false
  }
  return true
})()

const showMagicLinkModal = ref(false)
const magicLinkForm = useForm({
  email: '',
})

const submit = () => {
  form.post(route('login'))
}

const sendMagicLink = () => {
  magicLinkForm.post(route('magic.login'), {
    onFinish: () => {
      if (!magicLinkForm.hasErrors) {
        showMagicLinkModal.value = false
      }
    },
  })
}
</script>

<template>
  <Head title="Login" />
  <main class="mx-auto max-w-[384px] px-8" role="main">
    <h1 class="main-heading text-center">Login</h1>
    <form
      class="container-border mt-6 space-y-6 p-5"
      aria-labelledby="login-form"
      @submit.prevent="submit">
      <Alert v-if="status" type="info" class="mb-6">
        {{ status }}
      </Alert>
      <FormInput
        id="email"
        v-model="form.email"
        label="Email address"
        name="email"
        type="email"
        required
        autocomplete="email"
        :error="form.errors.email" />
      <section class="space-y-4">
        <FormInput
          id="password"
          v-model="form.password"
          label="Password"
          name="password"
          type="password"
          required
          autocomplete="current-password"
          :error="form.errors.password" />
        <div class="flex items-center justify-between">
          <FormCheckbox
            id="remember-me"
            v-model="form.remember"
            label="Remember me"
            name="remember" />
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="link text-sm hover:underline"
            aria-label="Reset forgotten password">
            Forgot password?
          </Link>
        </div>
      </section>

      <button
        type="submit"
        :disabled="form.processing"
        class="btn btn-primary w-full"
        aria-busy="form.processing">
        {{ form.processing ? 'Signing in...' : 'Sign in' }}
      </button>

      <template v-if="smLogin || passwordlessLogin">
        <div role="separator" aria-label="or separator" class="relative">
          <hr class="border-t border-[var(--color-border)]" />
          <span
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[var(--color-surface)] px-2 text-[var(--color-text-muted)]">
            OR
          </span>
        </div>
      </template>

      <template v-if="smLogin">
        <Socialite :providers-config="providersConfig" :iconsOnly="true" />
      </template>

      <template v-if="passwordlessLogin">
        <button
          type="button"
          class="btn btn-secondary flex w-full cursor-pointer items-center justify-center gap-2 text-sm transition-colors dark:text-gray-200 dark:hover:bg-gray-800 dark:hover:text-purple-400"
          aria-label="Open magic link login modal"
          @click="showMagicLinkModal = true">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span>Login with magic link</span>
        </button>
        <p class="text-center text-xs text-[var(--color-text-muted)]" role="note">
          We'll send a secure login link to your email
        </p>
      </template>
    </form>

    <p v-if="canRegister" class="mt-6 text-center text-sm text-[var(--color-text-muted)]">
      Don't have an account?
      <Link
        :href="route('register')"
        class="link ml-1 hover:underline"
        aria-label="Go to registration page">
        Sign up
      </Link>
    </p>
  </main>

  <Modal
    v-if="passwordlessLogin"
    :show="showMagicLinkModal"
    size="sm"
    aria-labelledby="modal-title"
    @close="showMagicLinkModal = false">
    <template #title>
      <h2 id="modal-title">Login with Magic Link</h2>
    </template>

    <template #default>
      <form class="space-y-4" aria-labelledby="magic-link-form" @submit.prevent="sendMagicLink">
        <FormInput
          id="magic-link-email"
          v-model="magicLinkForm.email"
          label="Email address"
          name="magic-link-email"
          type="email"
          required
          :error="magicLinkForm.errors.email"
          autocomplete="email" />
        <p class="text-sm text-[var(--color-text-muted)]">
          We'll send a secure login link to your email address.
        </p>
      </form>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-[var(--color-text)]"
          aria-label="Close modal"
          @click="showMagicLinkModal = false">
          Cancel
        </button>
        <button
          :disabled="magicLinkForm.processing"
          type="button"
          class="btn btn-primary btn-sm"
          aria-busy="magicLinkForm.processing"
          @click="sendMagicLink">
          {{ magicLinkForm.processing ? 'Sending...' : 'Send magic link' }}
        </button>
      </div>
    </template>
  </Modal>
</template>

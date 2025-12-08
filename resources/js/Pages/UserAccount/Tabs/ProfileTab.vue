<script setup>
import { computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormRadioGroup from '@js/Components/Forms/FormRadioGroup.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const gravatarUrl = computed(() => user.value?.gravatar)

const safeUserName = computed(() =>
  user.value?.name ? String(user.value.name).replace(/[<>]/g, '') : ''
)

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  profileEnabled: {
    type: Boolean,
    default: false,
  },
})

const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
  location: props.user.location,
  image_type: props.user.profile_image_type,
})

const submitProfileForm = () => {
  profileForm.put('/user/profile-information', {
    preserveScroll: true,
  })
}

const avatarTypes = {
  avatar: {
    label: 'Avatar',
    value: 'avatar',
  },
  gravatar: {
    label: 'Gravatar',
    value: 'gravatar',
  },
  // image: {
  //     label: 'Image',
  //     value: 'image'
  // },
}

const currentAvatarUrl = computed(() => {
  if (profileForm?.image_type == 'gravatar') {
    return gravatarUrl?.value
  }
  return avatarUrl?.value
})
</script>

<template>
  <Head title="Profile - Account Settings" />
  <div>
    <div class="p-6 dark:bg-gray-900">
      <div
        class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <Alert v-if="!profileEnabled" type="info" class="mb-5">
          For demo purposes, profile update operations have been disabled in the Fortify
          configuration.
        </Alert>
        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
          Basic Profile Information
        </h3>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
          Update your personal information and email address
        </p>
        <form id="profile-form" class="space-y-8" @submit.prevent="submitProfileForm">
          <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <FormInput
                v-model="profileForm.name"
                label="Name"
                :error="profileForm.errors.name"
                required
                placeholder="Enter your full name" />
              <FormInput
                v-model="profileForm.email"
                label="Email address"
                type="email"
                autocomplete="email"
                :error="profileForm.errors.email"
                disabled />
            </div>
            <FormInput
              v-model="profileForm.location"
              label="Location"
              :error="profileForm.errors.location"
              placeholder="Enter your location" />
            <div class="flex items-end justify-between">
              <FormRadioGroup
                v-model="profileForm.image_type"
                label="Avatar Type"
                :options="avatarTypes"
                class="mb-2" />
              <img
                :src="currentAvatarUrl"
                :alt="`${safeUserName}'s avatar`"
                class="size-10 rounded-full ring-2 ring-white dark:ring-gray-800" />
            </div>
          </div>
        </form>
      </div>
    </div>
    <div
      class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
      <button
        type="submit"
        form="profile-form"
        class="btn btn-sm btn-primary"
        :disabled="profileForm.processing || !profileEnabled"
        :aria-busy="profileForm.processing">
        <svg
          v-if="profileForm.processing"
          class="h-4 w-4 animate-spin"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          aria-hidden="true">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4" />
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        {{ profileForm.processing ? 'Saving...' : 'Save profile' }}
      </button>
    </div>
  </div>
</template>

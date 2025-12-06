<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import FilePondUploader from '@js/Components/Forms/FilePondUploader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import axios from 'axios'

defineOptions({
  layout: Default,
})

const page = usePage()
const csrfToken = page.props.csrf_token

const props = defineProps({
  personalisation: {
    type: Object,
    required: false,
    default: () => ({}),
  },
})

const form = useForm({
  app_logo: props.personalisation?.app_logo || null,
  app_name: props.personalisation?.app_name || null,
  app_logo_dark: props.personalisation?.app_logo_dark || null,
  favicon: props.personalisation?.favicon || null,
  copyright_text: props.personalisation?.copyright_text || null,
})

const uploadConfig = {
  process: {
    url: route('admin.personalization.upload'),
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    onload: response => {
      return typeof response === 'string' ? JSON.parse(response) : response
    },
  },
  load: (source, load) => {
    fetch(source)
      .then(res => res.blob())
      .then(load)
  },
  revert: null,
  remove: null,
}

const getInitialFiles = field => {
  if (!props.personalisation?.[field]) {
    return []
  }

  return [
    {
      source: `/storage/${props.personalisation[field]}`,
      options: { type: 'local' },
    },
  ]
}

const handleProcessedFile = (error, file, name) => {
  if (error || !file) return

  const response = file.serverId
    ? typeof file.serverId === 'string'
      ? JSON.parse(file.serverId)
      : file.serverId
    : typeof file === 'string'
      ? JSON.parse(file)
      : file
  if (response?.path) {
    form[name] = response.path
  }
}

const handleFileRemoved = async (error, file, name) => {
  if (!error) {
    form[name] = null

    try {
      await axios.post(route('admin.personalization.delete.file'), { field: name })
      refreshPersonalisation()
    } catch (e) {
      // handle error
    }
  }
}

const submit = () => {
  form.post(route('admin.personalization.update.info'), {
    preserveScroll: true,
  })
}

const refreshPersonalisation = () => {
  router.reload({
    only: ['personalisation'], // this matches the key from HandleInertiaRequests
  })
}
</script>

<template>
  <Head title="Personalization Settings" />

  <main class="mx-auto max-w-7xl" aria-labelledby="personalization-settings">
    <div class="container-border">
      <PageHeader
        title="Personalization Settings"
        description="Configure your application's appearance and settings"
        :breadcrumbs="[
          { label: 'Dashboard', href: '/' },
          { label: 'System Settings', href: route('admin.setting.index') },
          { label: 'Personalization' },
        ]" />

      <div class="p-6 dark:bg-gray-900">
        <div
          class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
            Application Details
          </h3>
          <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Configure your application's basic information and branding
          </p>

          <form id="app-details-form" class="max-w-2xl space-y-8" @submit.prevent="submit">
            <div class="space-y-6">
              <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <FormInput
                  id="app_name"
                  v-model="form.app_name"
                  label="Application name"
                  placeholder="Enter your application name"
                  :error="form.errors.app_name" />
                <FormInput
                  id="copyright_text"
                  v-model="form.copyright_text"
                  label="Copyright text"
                  placeholder="Enter copyright text"
                  :error="form.errors.copyright_text" />
              </div>
            </div>
          </form>
        </div>
      </div>
      <div
        class="flex justify-center border-t border-gray-200 bg-gray-50 px-4 py-4 sm:justify-end sm:px-6 dark:border-gray-700 dark:bg-gray-800">
        <button
          type="submit"
          form="app-details-form"
          class="btn btn-sm btn-primary w-full sm:w-auto"
          :disabled="form.processing"
          :aria-busy="form.processing">
          <svg
            v-if="form.processing"
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
          {{ form.processing ? 'Saving...' : 'Update Settings' }}
        </button>
      </div>

      <div class="border-t border-gray-200 p-4 sm:p-6 dark:border-gray-700 dark:bg-gray-900">
        <div
          class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6 dark:border-gray-700 dark:bg-gray-800">
          <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Branding</h3>
          <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Upload and manage your application logos and favicon
          </p>

          <div class="max-w-6xl space-y-16 md:space-y-16">
            <div
              class="grid grid-cols-1 gap-8 md:grid-cols-3 md:gap-12"
              role="group"
              aria-label="Media uploads">
              <div class="flex justify-center">
                <div class="w-full max-w-48 md:w-32">
                  <FilePondUploader
                    id="app_logo"
                    name="app_logo"
                    label="Logo"
                    label-idle="Drop logo here..."
                    :accepted-file-types="['image/jpeg', 'image/png']"
                    :server="uploadConfig"
                    :files="getInitialFiles('app_logo')"
                    @processfile="(error, file) => handleProcessedFile(error, file, 'app_logo')"
                    @removefile="(error, file) => handleFileRemoved(error, file, 'app_logo')" />
                </div>
              </div>

              <div class="flex justify-center">
                <div class="w-full max-w-48 md:w-32">
                  <FilePondUploader
                    id="app_logo_dark"
                    name="app_logo_dark"
                    label="Logo (dark mode)"
                    label-idle="Drop logo here..."
                    :accepted-file-types="['image/jpeg', 'image/png']"
                    :server="uploadConfig"
                    :files="getInitialFiles('app_logo_dark')"
                    @processfile="
                      (error, file) => handleProcessedFile(error, file, 'app_logo_dark')
                    "
                    @removefile="
                      (error, file) => handleFileRemoved(error, file, 'app_logo_dark')
                    " />
                </div>
              </div>

              <div class="flex justify-center">
                <div class="w-full max-w-48 md:w-32">
                  <FilePondUploader
                    id="favicon"
                    name="favicon"
                    label="Favicon"
                    label-idle="Drop favicon here..."
                    :accepted-file-types="['image/png', 'image/x-icon']"
                    :server="uploadConfig"
                    :files="getInitialFiles('favicon')"
                    @processfile="(error, file) => handleProcessedFile(error, file, 'favicon')"
                    @removefile="(error, file) => handleFileRemoved(error, file, 'favicon')" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

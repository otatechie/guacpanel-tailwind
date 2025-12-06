<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({})

const props = defineProps({
  deactivateEnabled: {
    type: Boolean,
    default: false,
  },
  deleteEnabled: {
    type: Boolean,
    default: false,
  },
})

const confirmDeactivateAccount = () => {
  if (props.deactivateEnabled) {
    deactivateModal.value = true
  }
}

const confirmDeleteAccount = () => {
  if (props.deleteEnabled) {
    deleteModal.value = true
  }
}

const deactivateAccount = () => {
  if (props.deactivateEnabled) {
    deactivateForm.post(route('user.deactivate'), {
      preserveScroll: true,
      onSuccess: () => {
        deactivateModal.value = false
        // window.location.href = route('home')
      },
    })
  } else {
    deactivateModal.value = false
  }
}

const deleteAccount = () => {
  if (props.deleteEnabled) {
    deleteForm.post(route('user.delete'), {
      preserveScroll: true,
      onSuccess: () => {
        deleteModal.value = false
        // window.location.href = route('home')
      },
    })
  }
}
</script>

<template>
  <Head title="Danger Zone - Account Settings" />
  <div class="border-t border-gray-200 p-4 sm:p-6 dark:border-gray-700 dark:bg-gray-900">
    <div class="rounded-lg border border-red-200 p-4 sm:p-6 dark:border-red-800">
      <h3 class="mb-4 text-base font-semibold text-red-600 sm:mb-6 sm:text-lg dark:text-red-400">
        Danger Zone
      </h3>

      <!-- Deactivate Account -->
      <div
        class="mb-4 rounded-lg border border-orange-200 bg-orange-50 p-3 sm:mb-6 sm:p-4 dark:border-orange-700 dark:bg-orange-900/20">
        <h4 class="mb-2 text-sm font-medium text-gray-900 sm:text-base dark:text-gray-100">
          Deactivate Account
        </h4>
        <p class="mb-3 text-xs leading-relaxed text-gray-600 sm:mb-4 sm:text-sm dark:text-gray-400">
          Hide your profile and data temporarily. You can reactivate your account anytime.
        </p>
        <button
          class="btn btn-secondary btn-sm inline-flex w-full items-center gap-2 sm:w-auto"
          :disabled="!deactivateEnabled"
          @click="confirmDeactivateAccount">
          <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            stroke-width="2">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
          </svg>
          <span class="hidden sm:inline">Deactivate account</span>
          <span class="sm:hidden">Deactivate</span>
        </button>
        <Alert v-if="!deactivateEnabled" type="warning" class="mt-4">
          Feature disabled in the
          <span class="font-mono">.env</span>
          file
        </Alert>
      </div>

      <!-- Delete Account -->
      <div
        class="rounded-lg border border-red-200 bg-red-50 p-3 sm:p-4 dark:border-red-700 dark:bg-red-900/20">
        <h4 class="mb-2 text-sm font-medium text-gray-900 sm:text-base dark:text-gray-100">
          Delete Account Permanently
        </h4>
        <p class="mb-3 text-xs leading-relaxed text-gray-600 sm:mb-4 sm:text-sm dark:text-gray-400">
          This action is permanent and cannot be undone. All your data will be permanently deleted.
        </p>
        <button
          class="btn btn-danger btn-sm inline-flex w-full items-center gap-2 sm:w-auto"
          :disabled="!deleteEnabled"
          @click="confirmDeleteAccount">
          <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            stroke-width="2">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          <span class="hidden sm:inline">Permanently delete account</span>
          <span class="sm:hidden">Delete Account</span>
        </button>
        <Alert v-if="!deleteEnabled" type="warning" class="mt-4">
          Feature disabled in the
          <span class="font-mono">.env</span>
          file
        </Alert>
      </div>
    </div>
  </div>

  <Modal :show="deactivateModal" size="sm" @close="deactivateModal = false">
    <template #title>
      <div class="text-red-600 dark:text-red-400">Deactivate Account</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to deactivate your account? This action cannot be undone.
        </p>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          :disabled="deactivateForm.processing"
          @click="deactivateModal = false">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-danger btn-sm"
          :disabled="deactivateForm.processing"
          @click="deactivateAccount">
          {{ deactivateForm.processing ? 'Deactivating...' : 'Yes, deactivate' }}
        </button>
      </div>
    </template>
  </Modal>

  <Modal :show="deleteModal" size="sm" @close="deleteModal = false">
    <template #title>
      <div class="text-red-600 dark:text-red-400">Delete Account</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to delete your account? This action is permanent and cannot be
          undone.
        </p>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          :disabled="deleteForm.processing"
          @click="deleteModal = false">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-danger btn-sm"
          :disabled="deleteForm.processing"
          @click="deleteAccount">
          {{ deleteForm.processing ? 'Deleting...' : 'Yes, delete' }}
        </button>
      </div>
    </template>
  </Modal>
</template>

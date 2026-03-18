<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'

const props = defineProps({
    deactivateEnabled: { type: Boolean, default: false },
    deleteEnabled: { type: Boolean, default: false },
})

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({})

const deactivateAccount = () => {
    deactivateForm.post(route('user.deactivate'), {
        preserveScroll: true,
        onSuccess: () => { deactivateModal.value = false },
    })
}

const deleteAccount = () => {
    deleteForm.post(route('user.delete'), {
        preserveScroll: true,
        onSuccess: () => { deleteModal.value = false },
    })
}
</script>

<template>
    <div class="max-w-2xl space-y-5">
        <div v-if="deactivateEnabled">
            <p class="text-base font-medium text-(--color-text)">Deactivate account</p>
            <p class="mt-1 text-sm text-(--color-text-muted)">Temporarily hide your profile and data. You can reactivate anytime.</p>
            <button type="button" class="btn btn-sm btn-secondary mt-3" @click="deactivateModal = true">Deactivate</button>
        </div>

        <div :class="deactivateEnabled ? 'border-t border-red-200 pt-5 dark:border-red-900/30' : ''">
            <p class="text-base font-medium text-red-600 dark:text-red-400">Delete account</p>
            <p class="mt-1 text-sm text-(--color-text-muted)">Permanently delete your account and all data. This cannot be undone.</p>
            <button v-if="deleteEnabled" type="button" class="btn btn-sm btn-danger mt-3" @click="deleteModal = true">Delete account</button>
            <p v-else class="mt-2 text-xs text-(--color-text-muted)">Account deletion is disabled.</p>
        </div>
    </div>

    <Modal :show="deactivateModal" @close="deactivateModal = false" size="sm">
        <template #title>Deactivate account</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">Your account will be hidden but can be reactivated later.</p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="deactivateModal = false">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" :disabled="deactivateForm.processing" @click="deactivateAccount">
                    {{ deactivateForm.processing ? 'Deactivating...' : 'Deactivate' }}
                </button>
            </div>
        </template>
    </Modal>

    <Modal :show="deleteModal" @close="deleteModal = false" size="sm">
        <template #title>Delete account</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">This permanently deletes your account and all associated data.</p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="deleteModal = false">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" :disabled="deleteForm.processing" @click="deleteAccount">
                    {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

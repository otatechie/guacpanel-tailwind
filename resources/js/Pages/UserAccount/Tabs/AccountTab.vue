<script setup>
import Button from '@/Components/Button.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'

defineProps({
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
        onSuccess: () => {
            deactivateModal.value = false
        },
    })
}

const deleteAccount = () => {
    deleteForm.post(route('user.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            deleteModal.value = false
        },
    })
}
</script>

<template>
    <div class="max-w-2xl space-y-5">
        <div>
            <p class="text-foreground text-base font-medium">Download your data</p>
            <p class="text-muted-foreground mt-1 text-sm">
                A JSON file with your profile, roles and permissions, notification preferences,
                sign-in history and the notifications addressed to you.
            </p>
            <Button
                :as="'a'"
                :href="route('user.export')"
                variant="secondary"
                size="sm"
                class="mt-3">
                Download
            </Button>
        </div>

        <div v-if="deactivateEnabled" class="border-border border-t pt-5">
            <p class="text-foreground text-base font-medium">Deactivate account</p>
            <p class="text-muted-foreground mt-1 text-sm">
                Signs you out and suspends access. An administrator has to reactivate it for you.
            </p>
            <Button variant="secondary" size="sm" class="mt-3" @click="deactivateModal = true">
                Deactivate
            </Button>
        </div>

        <div
            :class="deactivateEnabled ? 'border-t border-red-200 pt-5 dark:border-red-900/30' : ''">
            <p class="text-base font-medium text-red-600 dark:text-red-400">Delete account</p>
            <p class="text-muted-foreground mt-1 text-sm">
                Permanently delete your account and all data. This cannot be undone.
            </p>
            <Button
                variant="danger"
                size="sm"
                class="mt-3"
                v-if="deleteEnabled"
                @click="deleteModal = true">
                Delete account
            </Button>
            <p v-else class="text-muted-foreground mt-2 text-xs">Account deletion is disabled.</p>
        </div>
    </div>

    <Modal :show="deactivateModal" @close="deactivateModal = false" size="sm">
        <template #title>Deactivate account</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                You will be signed out immediately and will not be able to sign back in.
                Reactivating requires an administrator.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="deactivateModal = false">
                    Cancel
                </Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="deactivateForm.processing"
                    @click="deactivateAccount">
                    {{ deactivateForm.processing ? 'Deactivating...' : 'Deactivate' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal :show="deleteModal" @close="deleteModal = false" size="sm">
        <template #title>Delete account</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                This permanently deletes your account and all associated data.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="deleteModal = false">Cancel</Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="deleteForm.processing"
                    @click="deleteAccount">
                    {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>

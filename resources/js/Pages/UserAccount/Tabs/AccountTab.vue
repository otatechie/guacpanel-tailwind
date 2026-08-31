<script setup>
import Button from '@/Components/Button.vue'
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'

const props = defineProps({
    deactivateEnabled: { type: Boolean, default: false },
    deleteEnabled: { type: Boolean, default: false },
    restoreEnabled: { type: Boolean, default: false },
    daysToRestore: { type: Number, default: 0 },
    deletePasswordRequired: { type: Boolean, default: true },
})

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({ password: '' })

/* Deleting soft-deletes and emails a signed restore link, so promising the user
   it "cannot be undone" was both false and a reason to distrust every other
   warning on the page. Say what actually happens, and only claim permanence
   where the restore route is switched off. */
const deleteConsequence = computed(() =>
    props.restoreEnabled && props.daysToRestore > 0
        ? `Removes your account. We email you a link to restore it, good for ${props.daysToRestore} days.`
        : 'Removes your account and all its data. This cannot be undone.'
)

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
            deleteForm.reset()
        },
    })
}

const closeDeleteModal = () => {
    deleteModal.value = false
    deleteForm.reset()
    deleteForm.clearErrors()
}
</script>

<template>
    <section class="space-y-8">
        <div>
            <h2 class="text-foreground text-base font-medium">Download your data</h2>
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

        <!-- Leaving is one goal with two answers, so they are grouped and ordered
             by consequence rather than sitting at the same weight as an export. -->
        <div class="border-border border-t pt-8">
            <h2 class="text-foreground text-base font-medium">Close your account</h2>
            <p class="text-muted-foreground mt-1 text-sm">
                One of these is reversible on your own terms. The other is not.
            </p>

            <div v-if="deactivateEnabled" class="mt-5">
                <h3 class="text-foreground text-sm font-medium">Deactivate</h3>
                <p class="text-muted-foreground mt-1 text-sm">
                    Signs you out and suspends access. Everything is kept, and an administrator has
                    to reactivate it for you.
                </p>
                <Button variant="secondary" size="sm" class="mt-3" @click="deactivateModal = true">
                    Deactivate
                </Button>
            </div>

            <div class="mt-6">
                <h3 class="text-foreground text-sm font-medium">Delete</h3>
                <p class="text-muted-foreground mt-1 text-sm">{{ deleteConsequence }}</p>
                <Button
                    v-if="deleteEnabled"
                    variant="danger"
                    size="sm"
                    class="mt-3"
                    @click="deleteModal = true">
                    Delete
                </Button>
                <p v-else class="text-muted-foreground mt-2 text-xs">
                    Account deletion is disabled.
                </p>
            </div>
        </div>
    </section>

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
                    {{ deactivateForm.processing ? 'Deactivating...' : 'Deactivate account' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal :show="deleteModal" @close="closeDeleteModal" size="sm">
        <template #title>Delete account</template>
        <template #default>
            <form class="space-y-4" @submit.prevent="deleteAccount">
                <p class="text-muted-foreground text-sm">{{ deleteConsequence }}</p>

                <FormInput
                    v-if="deletePasswordRequired"
                    v-model="deleteForm.password"
                    label="Confirm your password"
                    type="password"
                    autocomplete="current-password"
                    :error="deleteForm.errors.password"
                    required />
            </form>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeDeleteModal">Cancel</Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="deleteForm.processing"
                    @click="deleteAccount">
                    {{ deleteForm.processing ? 'Deleting...' : 'Delete account' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { ref, h } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import { formatPermissionName } from '@js/utils/permissions'
import { SquarePenIcon, Trash2Icon } from '@lucide/vue'

const props = defineProps({
    permissions: {
        type: Array,
        required: true,
        default: () => [],
    },
    protectedPermissions: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const showAddModal = ref(false)
const editingPermission = ref(null)
const showDeleteModal = ref(false)
const permissionToDelete = ref(null)

const form = useForm({
    name: '',
    description: '',
})

/* Shared by the table column and the mobile card slot, which had drifted into
   two different looks — the card still used tinted blue/red buttons that
   scale on hover, from before the redesign. Empty for a protected permission,
   and RowActions renders nothing when it is empty. */
const permissionActions = permission =>
    permission.is_protected
        ? []
        : [
              {
                  label: 'Edit permission',
                  icon: SquarePenIcon,
                  onSelect: () => editPermission(permission),
              },
              {
                  label: 'Delete permission',
                  icon: Trash2Icon,
                  variant: 'destructive',
                  onSelect: () => confirmDeletePermission(permission),
              },
          ]

const columns = [
    {
        accessorKey: 'name',
        header: 'Permission',
        cell: ({ row }) => {
            const p = row.original
            /* No description line: the formatted name is the description.
               "Delete users" was sitting above "Delete user accounts" on every
               row, which is a second line of nothing per permission. */
            return h('div', {}, [
                h(
                    'span',
                    { class: 'text-sm font-medium text-foreground' },
                    formatPermissionName(p.name)
                ),
                p.is_protected
                    ? h('span', { class: 'ml-2 text-xs text-muted-foreground' }, 'Protected')
                    : null,
            ])
        },
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) => {
            const permission = row.original
            return h(RowActions, {
                actions: permissionActions(permission),
                label: `Actions for ${formatPermissionName(permission.name)}`,
            })
        },
    },
]

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingPermission.value = null
    permissionToDelete.value = null
    form.reset()
}

const editPermission = permission => {
    if (permission.is_protected) {
        return
    }
    editingPermission.value = permission
    form.name = permission.name
    form.description = permission.description
    showAddModal.value = true
}

const submitPermission = () => {
    if (editingPermission.value) {
        form.put(route('admin.permission.update', editingPermission.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.permission.store'), {
            onSuccess: () => closeModal(),
        })
    }
}

const confirmDeletePermission = permission => {
    if (permission.is_protected) {
        return
    }
    permissionToDelete.value = permission
    showDeleteModal.value = true
}

const deletePermission = () => {
    form.delete(route('admin.permission.destroy', permissionToDelete.value.id), {
        onSuccess: () => closeModal(),
    })
}
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-muted-foreground text-xs">{{ permissions.length }} permissions</p>
            <Button variant="primary" size="sm" @click="showAddModal = true">Add permission</Button>
        </div>

        <!-- `:pagination="{}"` deliberately: a non-empty pagination object puts
             Datatable in server mode, and this endpoint returns every permission
             in one go with no page/per_page handling — so the rows-per-page
             selector fired a round trip that came back identical. There are ~30
             of these; the table paginates them itself. -->
        <Datatable
            :data="permissions"
            :columns="columns"
            :pagination="{}"
            title="Permissions"
            :enable-search="true"
            :enable-export="true"
            empty-message="No permissions found"
            empty-description="Add your first permission to get started"
            export-file-name="permissions"
            :page-size-options="[10, 25, 50, 'All']"
            :default-page-size="10">
            <!-- "Protected" already appears beside the name, so a protected row
                 simply renders no menu rather than repeating the word here. -->
            <template #mobile-actions="{ row }">
                <RowActions
                    :actions="permissionActions(row)"
                    :label="`Actions for ${formatPermissionName(row.name)}`" />
            </template>
        </Datatable>

        <Modal
            :show="showAddModal"
            size="sm"
            description="Permissions are granted to users through roles."
            @close="closeModal">
            <template #title>
                {{ editingPermission ? 'Edit permission' : 'Add permission' }}
            </template>
            <template #default>
                <form @submit.prevent="submitPermission" class="space-y-4">
                    <FormInput
                        label="Name"
                        v-model="form.name"
                        :error="form.errors.name"
                        required />
                    <FormInput
                        label="Description"
                        v-model="form.description"
                        :error="form.errors.description" />
                </form>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                    <Button
                        variant="primary"
                        size="sm"
                        :disabled="form.processing"
                        @click="submitPermission">
                        {{ form.processing ? 'Saving...' : editingPermission ? 'Save' : 'Add' }}
                    </Button>
                </div>
            </template>
        </Modal>

        <Modal :show="showDeleteModal" size="sm" @close="closeModal">
            <template #title>Delete permission</template>
            <template #default>
                <!-- The record, then what happens to it. The consequence used to
                     sit in the header as a subtitle, two sizes down from the name
                     it applied to. -->
                <p class="text-foreground text-sm font-medium">
                    {{ formatPermissionName(permissionToDelete?.name) }}
                </p>
                <p class="text-muted-foreground mt-2 text-sm">
                    Removed from every role that uses it. Anyone who had access through it loses it
                    immediately, and this cannot be undone.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                    <Button
                        variant="danger"
                        size="sm"
                        :disabled="form.processing"
                        @click="deletePermission">
                        {{ form.processing ? 'Deleting...' : 'Delete' }}
                    </Button>
                </div>
            </template>
        </Modal>
    </section>
</template>

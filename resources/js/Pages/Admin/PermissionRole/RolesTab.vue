<script setup>
import Button from '@/Components/Button.vue'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormTextarea from '@js/Components/Forms/FormTextarea.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const props = defineProps({
    roles: { type: Array, required: true, default: () => [] },
    permissions: { type: Array, required: true, default: () => [] },
    protectedRoles: { type: Array, default: () => [] },
})

const showAddModal = ref(false)
const editingRole = ref(null)
const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const expandedRoles = ref(new Set())
const permissionSearch = ref('')

const form = useForm({ name: '', description: '', permissions: [] })

const filteredPermissions = computed(() => {
    if (!permissionSearch.value) return props.permissions
    const q = permissionSearch.value.toLowerCase()
    return props.permissions.filter(p => p.name?.toLowerCase().includes(q))
})

const allPermissionsSelected = computed(() =>
    props.permissions?.length && form.permissions.length === props.permissions.length
)

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingRole.value = null
    roleToDelete.value = null
    permissionSearch.value = ''
    form.reset()
}

const editRole = role => {
    if (role.is_protected) return
    editingRole.value = role
    form.name = role.name
    form.description = role.description || ''
    form.permissions = role.permissions?.map(p => p.id) || []
    showAddModal.value = true
}

const submitRole = () => {
    if (editingRole.value) {
        form.put(route('admin.role.update', editingRole.value.id), { onSuccess: closeModal })
    } else {
        form.post(route('admin.role.store'), { onSuccess: closeModal })
    }
}

const confirmDeleteRole = role => {
    if (role.is_protected) return
    roleToDelete.value = role
    showDeleteModal.value = true
}

const deleteRole = () => {
    form.delete(route('admin.role.destroy', roleToDelete.value.id), { onSuccess: closeModal })
}

const toggleAllPermissions = checked => {
    form.permissions = checked ? props.permissions.map(p => p.id) : []
}

const togglePermission = (id, checked) => {
    if (checked) { if (!form.permissions.includes(id)) form.permissions.push(id) }
    else { form.permissions = form.permissions.filter(i => i !== id) }
}

const toggleExpand = id => {
    if (expandedRoles.value.has(id)) expandedRoles.value.delete(id)
    else expandedRoles.value.add(id)
}

const formatPerm = name => {
    const words = name.split('-').join(' ')
    return words.charAt(0).toUpperCase() + words.slice(1)
}

const actionBtn = 'cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground'
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-xs text-muted-foreground">{{ roles.length }} {{ roles.length === 1 ? 'role' : 'roles' }}</p>
            <Button variant="primary" size="sm" @click="showAddModal = true">Add role</Button>
        </div>

        <!-- Roles list -->
        <div v-if="roles.length" class="divide-y divide-border rounded-lg border border-border">
            <div v-for="role in roles" :key="role.id" class="px-4 py-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium capitalize text-foreground">{{ role.name }}</p>
                            <span v-if="role.is_protected" class="text-[10px] text-muted-foreground">Protected</span>
                        </div>
                        <p v-if="role.description" class="mt-0.5 text-xs text-muted-foreground">{{ role.description }}</p>

                        <!-- Permissions -->
                        <div v-if="role.permissions?.length" class="mt-2 flex flex-wrap gap-1">
                            <template v-if="expandedRoles.has(role.id)">
                                <span v-for="p in role.permissions" :key="p.id" class="rounded bg-muted px-1.5 py-0.5 font-mono text-[10px] text-muted-foreground">{{ p.name }}</span>
                                <button type="button" @click="toggleExpand(role.id)" class="px-1 text-[10px] text-muted-foreground hover:text-foreground">Less</button>
                            </template>
                            <template v-else>
                                <span v-for="p in role.permissions.slice(0, 4)" :key="p.id" class="rounded bg-muted px-1.5 py-0.5 font-mono text-[10px] text-muted-foreground">{{ p.name }}</span>
                                <button v-if="role.permissions.length > 4" type="button" @click="toggleExpand(role.id)" class="px-1 text-[10px] text-muted-foreground hover:text-foreground">+{{ role.permissions.length - 4 }}</button>
                            </template>
                        </div>
                        <p v-else class="mt-1 text-[10px] text-muted-foreground">No permissions</p>
                    </div>

                    <div v-if="!role.is_protected" class="flex shrink-0 gap-0.5">
                        <button type="button" :class="actionBtn" title="Edit" @click="editRole(role)">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button type="button" :class="actionBtn + ' hover:text-red-600! dark:hover:text-red-400!'" title="Delete" @click="confirmDeleteRole(role)">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <p v-else class="py-6 text-center text-sm text-muted-foreground">
            No roles yet.
            <button type="button" @click="showAddModal = true" class="text-primary hover:underline">Add one</button>
        </p>


    <!-- Add/Edit modal -->
    <Modal :show="showAddModal" @close="closeModal">
        <template #title>{{ editingRole ? 'Edit role' : 'Add role' }}</template>
        <template #default>
            <form @submit.prevent="submitRole" class="space-y-4">
                <FormInput label="Name" v-model="form.name" :error="form.errors.name" required />
                <FormTextarea label="Description" v-model="form.description" :error="form.errors.description" :rows="2" />

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-xs font-medium text-foreground">Permissions</p>
                        <p class="text-xs tabular-nums text-muted-foreground">{{ form.permissions.length }}/{{ permissions.length }}</p>
                    </div>
                    <div class="rounded-lg border border-border">
                        <div class="flex items-center gap-3 border-b border-border px-3 py-2">
                            <FormCheckbox :model-value="Boolean(allPermissionsSelected)" @update:model-value="toggleAllPermissions" label="Select all" />
                            <input v-model="permissionSearch" type="text" placeholder="Filter..." class="ml-auto w-32 rounded-md border border-border bg-card px-2 py-1 text-xs text-foreground placeholder-muted-foreground focus:border-primary focus:outline-none" />
                        </div>
                        <div class="max-h-48 overflow-y-auto p-2">
                            <div v-if="filteredPermissions.length" class="grid gap-0.5 sm:grid-cols-2">
                                <label v-for="p in filteredPermissions" :key="p.id" :for="`rp-${p.id}`"
                                    class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-xs transition-colors hover:bg-muted"
                                    :class="form.permissions.includes(p.id) ? 'text-foreground font-medium' : 'text-muted-foreground'">
                                    <input :id="`rp-${p.id}`" type="checkbox" :checked="form.permissions.includes(p.id)" @change="togglePermission(p.id, $event.target.checked)" class="h-3 w-3 shrink-0 rounded border-border text-primary" />
                                    {{ formatPerm(p.name) }}
                                </label>
                            </div>
                            <p v-else class="py-3 text-center text-xs text-muted-foreground">No match</p>
                        </div>
                    </div>
                    <p v-if="form.errors.permissions" class="mt-1 text-xs text-red-600">{{ form.errors.permissions }}</p>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button variant="primary" size="sm" :disabled="form.processing" @click="submitRole">
                    {{ form.processing ? 'Saving...' : editingRole ? 'Save' : 'Add role' }}
                </Button>
            </div>
        </template>
    </Modal>

    <!-- Delete modal -->
    <Modal :show="showDeleteModal" @close="closeModal" size="sm">
        <template #title>Delete role</template>
        <template #default>
            <p class="text-sm text-muted-foreground">
                Delete <span class="font-medium text-foreground">{{ roleToDelete?.name }}</span>? This removes the role from all assigned users.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="form.processing" @click="deleteRole">
                    {{ form.processing ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>
    </div>
</template>

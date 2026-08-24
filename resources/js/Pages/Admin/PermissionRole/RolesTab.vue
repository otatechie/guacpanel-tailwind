<script setup>
import Button from '@/Components/Button.vue'
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import Sheet from '@js/Components/Notifications/Sheet.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormTextarea from '@js/Components/Forms/FormTextarea.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import Badge from '@js/Components/Badge.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import { formatPermissionName, groupPermissions } from '@js/utils/permissions'
import { SquarePenIcon, Trash2Icon, XIcon } from '@lucide/vue'

const props = defineProps({
    roles: { type: Array, required: true, default: () => [] },
    permissions: { type: Array, required: true, default: () => [] },
    /** Role to open and scroll to on arrival, from `?role=<id>` */
    focusRoleId: { type: String, default: '' },
    protectedRoles: { type: Array, default: () => [] },
})

const showAddModal = ref(false)
const editingRole = ref(null)
const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const expandedRoles = ref(new Set())
const permissionSearch = ref('')

const isFocused = id => Boolean(props.focusRoleId) && String(id) === String(props.focusRoleId)

const COLLAPSED_CHIPS = 4

const visiblePermissions = role =>
    expandedRoles.value.has(role.id) ? role.permissions : role.permissions.slice(0, COLLAPSED_CHIPS)

/* Empty for a protected role, and RowActions renders nothing when it is empty —
   so a system role shows no controls at all rather than two that open a form the
   server refuses. */
const roleActions = role =>
    role.is_protected
        ? []
        : [
              { label: 'Edit role', icon: SquarePenIcon, onSelect: () => editRole(role) },
              {
                  label: 'Delete role',
                  icon: Trash2Icon,
                  variant: 'destructive',
                  onSelect: () => confirmDeleteRole(role),
              },
          ]

/* Arriving from a link that names a role: open its permissions and put it on
   screen, so the answer to "what does this role grant" is the first thing seen
   rather than a list to hunt through. */
onMounted(() => {
    // A payload of the wrong shape should cost the highlight, not the page: this
    // threw and took the whole mount with it when `roles` arrived as a paginator.
    if (!Array.isArray(props.roles)) return

    const role = props.roles.find(r => isFocused(r.id))
    if (!role) return

    // Seed the set with the role's own id, so "Less" still collapses it.
    expandedRoles.value.add(role.id)
    document
        .getElementById(`role-${role.id}`)
        ?.scrollIntoView({ block: 'center', behavior: 'smooth' })
})

const form = useForm({ name: '', description: '', permissions: [] })

const filteredPermissions = computed(() => {
    if (!permissionSearch.value) return props.permissions
    const q = permissionSearch.value.toLowerCase()
    /* Name only. This used to match `description` too, which is never rendered
       here — so "branding" returned rows whose visible labels contained no such
       word, with nothing on screen to explain the result. */
    return props.permissions.filter(p => formatPermissionName(p.name).toLowerCase().includes(q))
})

const permissionGroups = computed(() => groupPermissions(filteredPermissions.value))

const permissionsCount = computed(() => props.permissions?.length ?? 0)

/* Every control in the picker header reads the *filtered* set, never the whole
   list. Scoped to the full list, "Select all" under an active filter granted
   thirty permissions while showing five — including delete and impersonate. */
const filteredIds = computed(() => filteredPermissions.value.map(p => p.id))

const selectedInFilter = computed(
    () => filteredIds.value.filter(id => form.permissions.includes(id)).length
)

const allFilteredSelected = computed(
    () => filteredIds.value.length > 0 && selectedInFilter.value === filteredIds.value.length
)

const allPermissionsGranted = computed(
    () => permissionsCount.value > 0 && form.permissions.length === permissionsCount.value
)

// 1 of 30 rendered identically to 0 of 30 without this.
const someFilteredSelected = computed(
    () => selectedInFilter.value > 0 && !allFilteredSelected.value
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
    const ids = filteredIds.value
    form.permissions = checked
        ? [...new Set([...form.permissions, ...ids])]
        : form.permissions.filter(id => !ids.includes(id))
}

const togglePermission = (id, checked) => {
    if (checked) {
        if (!form.permissions.includes(id)) form.permissions.push(id)
    } else {
        form.permissions = form.permissions.filter(i => i !== id)
    }
}

const toggleExpand = id => {
    if (expandedRoles.value.has(id)) expandedRoles.value.delete(id)
    else expandedRoles.value.add(id)
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-muted-foreground text-xs font-medium">
                {{ roles.length }} {{ roles.length === 1 ? 'role' : 'roles' }}
            </h2>
            <Button variant="primary" size="sm" @click="showAddModal = true">Add role</Button>
        </div>

        <!-- Rules, no box: the rows already read as a list, and the tab strip
             above draws its own line. A border around both is a second frame.
             No `rounded-md` on the rows — divide-y draws its border along the
             row's top edge, so rounding curved the ends of every divider. No
             negative margin either: the rules end where the content ends, level
             with the "N roles" heading above them. -->
        <div v-if="roles.length" class="divide-border divide-y">
            <div
                v-for="role in roles"
                :key="role.id"
                :id="`role-${role.id}`"
                class="py-4 transition-colors"
                :class="isFocused(role.id) ? 'bg-muted/50' : ''">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-foreground text-sm font-medium capitalize">
                                {{ role.name }}
                            </p>
                            <span v-if="role.is_protected" class="text-muted-foreground text-xs">
                                Protected
                            </span>
                        </div>
                        <p v-if="role.description" class="text-muted-foreground mt-1 text-sm">
                            {{ role.description }}
                        </p>

                        <!-- Chips at text-xs: text-[10px] was below anything else
                             in the app, and there can be thirty of them. -->
                        <div
                            v-if="role.permissions?.length"
                            class="mt-2.5 flex flex-wrap items-center gap-1.5">
                            <!-- Formatted, not the raw slug: the Permissions tab
                                 and this role's own editor both show "View
                                 dashboard", so listing `view-dashboard` here left
                                 you unable to match what you ticked against what
                                 the row reports. -->
                            <Badge
                                v-for="p in visiblePermissions(role)"
                                :key="p.id"
                                variant="neutral">
                                {{ formatPermissionName(p.name) }}
                            </Badge>
                            <!-- One control, one shape. "+26" and "Less" sat in the
                                 same spot doing opposite things, and "+26" read as
                                 a count rather than a button. -->
                            <button
                                v-if="role.permissions.length > COLLAPSED_CHIPS"
                                type="button"
                                :aria-expanded="expandedRoles.has(role.id)"
                                class="text-muted-foreground hover:text-foreground cursor-pointer px-1 text-xs underline underline-offset-2"
                                @click="toggleExpand(role.id)">
                                {{
                                    expandedRoles.has(role.id)
                                        ? 'Show fewer'
                                        : `Show all ${role.permissions.length}`
                                }}
                            </button>
                        </div>
                        <p v-else class="text-muted-foreground mt-1 text-xs">No permissions</p>
                    </div>

                    <!-- Same two actions as the users list, so the same control:
                         a menu with words rather than two unlabelled glyphs. -->
                    <RowActions
                        class="shrink-0"
                        :actions="roleActions(role)"
                        :label="`Actions for the ${role.name} role`" />
                </div>
            </div>
        </div>

        <p v-else class="text-muted-foreground py-6 text-center text-sm">
            No roles yet.
            <button type="button" @click="showAddModal = true" class="text-primary hover:underline">
                Add one
            </button>
        </p>

        <!-- Add/Edit modal -->
        <!-- A sheet, not a dialog: this is a form with a thirty-item picker, and
             the picker was living in a 192px scroll box inside a small box. The
             panel gives it the height, and the role list stays on screen. -->
        <Sheet
            :show="showAddModal"
            size="lg"
            :close-on-click-outside="false"
            description="A role is a named set of permissions you assign to users."
            @close="closeModal">
            <template #title>{{ editingRole ? 'Edit role' : 'Add role' }}</template>
            <template #default>
                <form id="role-form" class="space-y-4" @submit.prevent="submitRole">
                    <FormInput
                        label="Name"
                        v-model="form.name"
                        :error="form.errors.name"
                        required />
                    <FormTextarea
                        label="Description"
                        v-model="form.description"
                        :error="form.errors.description"
                        :rows="2" />

                    <!-- A fieldset, not a <p> and thirty loose inputs: the
                         group needs a name a screen reader can announce, the
                         same way Name and Description above are labelled. -->
                    <fieldset>
                        <legend class="text-foreground mb-2 text-xs font-medium">
                            Permissions
                        </legend>

                        <div class="border-border rounded-lg border">
                            <!-- Sticky, because dropping the picker's 192px cage
                                 made the list taller than the viewport, and the
                                 count is the only feedback that anything is
                                 selected at all. The sheet body is the scroll
                                 container; no overflow-hidden on the box, which
                                 would kill the stick. -->
                            <div
                                class="border-border bg-card sticky top-0 z-10 flex items-center gap-3 rounded-t-lg border-b px-3 py-2">
                                <FormCheckbox
                                    :model-value="allFilteredSelected"
                                    :indeterminate="someFilteredSelected"
                                    :label="
                                        permissionSearch
                                            ? `Select all ${filteredIds.length} shown`
                                            : 'Select all'
                                    "
                                    @update:model-value="toggleAllPermissions" />

                                <p
                                    class="text-muted-foreground ml-auto shrink-0 text-xs tabular-nums">
                                    {{ form.permissions.length }}/{{ permissions.length }}
                                </p>

                                <div class="relative w-36 shrink-0">
                                    <label class="sr-only" for="permission-filter">
                                        Filter permissions
                                    </label>
                                    <input
                                        id="permission-filter"
                                        v-model="permissionSearch"
                                        type="text"
                                        placeholder="Filter..."
                                        class="form-input w-full pr-8 text-xs" />
                                    <button
                                        v-if="permissionSearch"
                                        type="button"
                                        class="text-muted-foreground hover:text-foreground absolute top-1/2 right-2 -translate-y-1/2"
                                        aria-label="Clear filter"
                                        @click="permissionSearch = ''">
                                        <XIcon class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- No max-height: the sheet body scrolls, so the list
                                 uses whatever room the viewport has. -->
                            <div class="p-2">
                                <template v-if="permissionGroups.length">
                                    <!-- Grouped by what is governed. Flat, the
                                         list came out in insertion order and read
                                         across two columns, so "View dashboard"
                                         sat eight positions from "Access
                                         dashboard" and the user permissions were
                                         scattered over four rows. -->
                                    <div
                                        v-for="group in permissionGroups"
                                        :key="group.label"
                                        class="mt-4 first:mt-0">
                                        <p class="text-muted-foreground mb-1 px-1 text-xs">
                                            {{ group.label }}
                                        </p>
                                        <div class="grid gap-x-4 sm:grid-cols-2">
                                            <FormCheckbox
                                                v-for="p in group.permissions"
                                                :key="p.id"
                                                :id="`rp-${p.id}`"
                                                :model-value="form.permissions.includes(p.id)"
                                                :label="formatPermissionName(p.name)"
                                                class="hover:bg-muted rounded-md px-1 py-1.5"
                                                @update:model-value="
                                                    togglePermission(p.id, $event)
                                                " />
                                        </div>
                                    </div>
                                </template>
                                <p v-else class="text-muted-foreground py-3 text-center text-xs">
                                    {{
                                        permissionSearch
                                            ? `No permission matches "${permissionSearch}"`
                                            : 'No permissions available'
                                    }}
                                </p>
                            </div>
                        </div>

                        <p
                            v-if="allPermissionsGranted && !form.errors.permissions"
                            class="text-muted-foreground mt-1.5 text-xs">
                            This role grants everything, including deleting users and impersonation.
                        </p>
                        <p v-if="form.errors.permissions" class="mt-1 text-xs text-red-600">
                            {{ form.errors.permissions }}
                        </p>
                    </fieldset>
                </form>
            </template>
            <template #footer>
                <div class="flex w-full justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                    <!-- `form` reaches across the slot boundary, so Enter submits. -->
                    <Button
                        type="submit"
                        form="role-form"
                        variant="primary"
                        size="sm"
                        :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : editingRole ? 'Save role' : 'Add role' }}
                    </Button>
                </div>
            </template>
        </Sheet>

        <!-- Delete modal -->
        <Modal :show="showDeleteModal" size="sm" @close="closeModal">
            <template #title>Delete role</template>
            <template #default>
                <!-- The record, then what happens to it. The consequence used to
                     sit in the header as a subtitle, two sizes down from the name
                     it applied to. -->
                <p class="text-foreground text-sm font-medium capitalize">
                    {{ roleToDelete?.name }}
                    <span
                        v-if="roleToDelete?.permissions?.length"
                        class="text-muted-foreground font-normal normal-case">
                        · {{ roleToDelete.permissions.length }}
                        {{ roleToDelete.permissions.length === 1 ? 'permission' : 'permissions' }}
                    </span>
                </p>
                <p class="text-muted-foreground mt-2 text-sm">
                    Removed from every user assigned to it. They keep their accounts but lose
                    whatever access this role granted, and this cannot be undone.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                    <Button
                        variant="danger"
                        size="sm"
                        :disabled="form.processing"
                        @click="deleteRole">
                        {{ form.processing ? 'Deleting...' : 'Delete' }}
                    </Button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<template>
    <div class="w-full md:w-6/12">
        <div class="flex justify-end mb-2">
            <Button label="Add role" icon="pi pi-plus" size="small" class="btn-primary"
                @click="openAddModal" />
            <Dialog v-model:visible="addModalVisible" modal header="Create role" :style="{ width: '22rem' }">
                <div class="md:max-w-md w-full">
                    <div class="pt-2">
                        <FloatLabel variant="on">
                            <InputText id="name" v-model="addForm.name" type="text" :invalid="!!addForm.errors.name"
                                class="w-full" />
                            <label for="name">Role name</label>
                        </FloatLabel>
                        <p v-if="addForm.errors.name" class="mt-2 text-xs text-red-600">
                            {{ addForm.errors.name }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-8">
                    <Button type="button" size="small" label="Close" severity="secondary" @click="closeAddModal" />
                    <Button type="button" size="small" label="Save" @click="createRole" />
                </div>
            </Dialog>
        </div>

        <ul v-if="roles?.length" role="list" class="divide-y divide-gray-100 mt-12">
            <li v-for="role in roles" :key="role.id" class="flex justify-between gap-x-2 py-2">
                <div class="flex min-w-0 gap-x-4">
                    <div class="min-w-0 flex-auto">
                        <p class="text-md font-medium text-gray-700">
                            <span class="hover:underline capitalize">{{ role.name }}</span>
                        </p>
                        <p class="mt-1 flex text-xs/5 text-gray-500">
                            {{ role.description || 'No description available.' }}
                        </p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-x-2">
                    <Button icon="pi pi-pencil" @click="openEditModal(role)" size="small" severity="secondary" rounded
                        variant="outlined"  />
                    <Button icon="pi pi-trash" @click="confirmDelete(role)" size="small" severity="danger" rounded
                        variant="outlined" />
                </div>
            </li>
        </ul>

        <div v-else class="w-full md:w-8/12">
            <Message severity="secondary" class="p-2">
                <p class="text-xs">No roles found. Please create a role to get started.</p>
            </Message>
        </div>

        <Dialog v-model:visible="editModalVisible" modal header="Edit role" :style="{ width: '22rem' }">
            <div class="md:max-w-md w-full">
                <div class="pt-2">
                    <FloatLabel variant="on">
                        <InputText id="editName" v-model="editForm.name" type="text" :invalid="!!editForm.errors.name"
                            class="w-full" />
                        <label for="editName">Role name</label>
                    </FloatLabel>
                    <p v-if="editForm.errors.name" class="mt-2 text-xs text-red-600">
                        {{ editForm.errors.name }}
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-8">
                <Button type="button" size="small" label="Close" severity="secondary" @click="closeEditModal" />
                <Button type="button" size="small" label="Save" @click="updateRole" />
            </div>
        </Dialog>

        <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm delete" :style="{ width: '22rem' }">
            <p>Are you sure you want to delete this role?</p>
            <div class="flex justify-end gap-2 mt-8">
                <Button type="button" size="small" label="Cancel" severity="secondary"
                    @click="deleteDialogVisible = false" />
                <Button type="button" size="small" label="Delete" severity="danger" @click="deleteRole" />
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: {
        type: Object,
        required: true
    }
});

const addModalVisible = ref(false);

const addForm = useForm({
    name: '',
});

const deleteForm = useForm({});

const editModalVisible = ref(false);
const selectedRole = ref(null);
const editForm = useForm({
    name: '',
});

const deleteDialogVisible = ref(false);

const openAddModal = () => {
    addForm.reset();
    addModalVisible.value = true;
};

const closeAddModal = () => {
    addForm.reset();
    addModalVisible.value = false;
};

const createRole = () => {
    addForm.post(route('admin.role.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddModal();
        },
    });
};

const openEditModal = (role) => {
    selectedRole.value = role;
    editForm.name = role.name;
    editModalVisible.value = true;
};

const closeEditModal = () => {
    selectedRole.value = null;
    editForm.reset();
    editModalVisible.value = false;
};

const updateRole = () => {
    if (!selectedRole.value) return;

    editForm.put(route('admin.role.update', { id: selectedRole.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const confirmDelete = (role) => {
    selectedRole.value = role;
    deleteDialogVisible.value = true;
};

const deleteRole = () => {
    if (!selectedRole.value) return;

    deleteForm.delete(route('admin.role.destroy', { id: selectedRole.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogVisible.value = false;
            selectedRole.value = null;
        },
    });
};

watch(() => addForm.wasSuccessful, (success) => {
    if (success) {
        closeAddModal();
    }
});

watch(() => editForm.wasSuccessful, (success) => {
    if (success) {
        closeEditModal();
    }
});
</script>

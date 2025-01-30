<template>

    <Head title="Categories" />

    <slot>
        <div class="px-5">
            <Breadcrumb :home="home" :model="items" />
            <div class="space-y-10 divide-y divide-gray-900/10">
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 place-items-center">
                    <div class="w-full max-w-2xl px-4 sm:px-0">
                        <div class="flex justify-end">
                            <Button label="Add category" icon="pi pi-plus" size="small" class="btn-primary"
                                @click="openAddModal" />
                            <Dialog v-model:visible="addModalVisible" modal header="Create category"
                                :style="{ width: '22rem' }">
                                <div class="md:max-w-md w-full">
                                    <div class="pt-2">
                                        <FloatLabel variant="on">
                                            <InputText id="name" v-model="createCategoryForm.name" type="text"
                                                :invalid="!!createCategoryForm.errors.name" class="w-full" />
                                            <label for="name">Category name</label>
                                        </FloatLabel>
                                        <p v-if="createCategoryForm.errors.name" class="mt-2 text-xs text-red-600">
                                            {{ createCategoryForm.errors.name }}
                                        </p>
                                    </div>
                                    <div class="pt-6">
                                        <FloatLabel variant="on">
                                            <Select v-model="createCategoryForm.parent_id" :options="categories"
                                                optionLabel="name" optionValue="id" showClear class="w-full"
                                                :invalid="!!createCategoryForm.errors.parent_id" />
                                            <label for="parent_id">Parent category</label>
                                        </FloatLabel>
                                        <p v-if="createCategoryForm.errors.parent_id" class="mt-2 text-xs text-red-600">
                                            {{ createCategoryForm.errors.parent_id }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-8">
                                    <Button type="button" size="small" label="Close" severity="secondary"
                                        @click="closeAddModal" />
                                    <Button type="button" size="small" label="Save" @click="submitCategoryForm" />
                                </div>
                            </Dialog>
                            <Dialog v-model:visible="editModalVisible" modal header="Edit category"
                                :style="{ width: '22rem' }">
                                <div class="md:max-w-md w-full">
                                    <div class="pt-2">
                                        <FloatLabel variant="on">
                                            <InputText id="edit-name" v-model="editCategoryForm.name" type="text"
                                                :invalid="!!editCategoryForm.errors.name" class="w-full" />
                                            <label for="edit-name">Category name</label>
                                        </FloatLabel>
                                        <p v-if="editCategoryForm.errors.name" class="mt-2 text-xs text-red-600">
                                            {{ editCategoryForm.errors.name }}
                                        </p>
                                    </div>
                                    <div class="pt-6">
                                        <FloatLabel variant="on">
                                            <Select v-model="editCategoryForm.parent_id"
                                                :options="availableParentCategories"
                                                optionLabel="name"
                                                optionValue="id"
                                                showClear
                                                class="w-full"
                                                :invalid="!!editCategoryForm.errors.parent_id" />
                                            <label for="parent_id">Parent category</label>
                                        </FloatLabel>
                                        <p v-if="editCategoryForm.errors.parent_id" class="mt-2 text-xs text-red-600">
                                            {{ editCategoryForm.errors.parent_id }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-8">
                                    <Button type="button" size="small" label="Close" severity="secondary"
                                        @click="closeEditModal" />
                                    <Button type="button" size="small" label="Save" @click="submitEditForm" />
                                </div>
                            </Dialog>
                            <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Delete" :style="{ width: '22rem' }">
                                <div class="mb-4">Are you sure you want to delete this category?</div>
                                <div class="flex justify-end gap-2">
                                    <Button type="button" size="small" label="Cancel" severity="secondary" @click="closeDeleteDialog" />
                                    <Button type="button" size="small" label="Delete" severity="danger" @click="deleteCategory" />
                                </div>
                            </Dialog>
                        </div>
                        <div>
                            <h2 class="text-xl font-heading font-semibold leading-7 text-gray-800 mb-5">Categories</h2>
                            <template v-if="tableData && tableData.length > 0">
                                <div class="overflow-x-auto w-full">
                                    <DataTable :size="size.value" :value="tableData"
                                        v-model:selection="selectedCategory" class="text-xs w-full" ref="dt"
                                        dataKey="id" scrollable scrollHeight="600px" paginator :rows="10"
                                        :rowsPerPageOptions="[10, 20, 30, 50]" sortMode="multiple" removableSort
                                        stripedRows :filters="filters">
                                        <template #header>
                                            <div style="text-align: left">
                                                <Button size="small" icon="pi pi-external-link" severity="contrast"
                                                    label="Export" @click="exportCSV($event)" />
                                            </div>
                                            <div class="flex justify-end">
                                                <IconField>
                                                    <InputIcon>
                                                        <i class="pi pi-search" />
                                                    </InputIcon>
                                                    <InputText v-model="filters['global'].value" size="small"
                                                        placeholder="Search" />
                                                </IconField>
                                            </div>
                                        </template>
                                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                                        <Column field="name" header="Category name" sortable></Column>
                                        <Column field="parent_name" header="Parent category" sortable></Column>
                                        <Column field="created_at" header="Created"></Column>
                                        <Column header="Action" style="width: 10rem">
                                            <template #body="slotProps">
                                                <div class="flex gap-2">
                                                    <Button size="small" icon="pi pi-pencil" rounded severity="secondary" title="Edit"
                                                        @click="editCategory(slotProps.data)" />
                                                    <Button size="small" icon="pi pi-trash" rounded severity="danger" title="Delete"
                                                        @click="confirmDelete(slotProps.data)" />
                                                </div>
                                            </template>
                                        </Column>
                                    </DataTable>
                                </div>
                            </template>
                            <div v-else class="w-full md:w-9/12 mx-auto">
                                <Message severity="secondary" icon="pi pi-send" class="p-2">
                                    Bummer, apparently no records found.
                                </Message>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </slot>

</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Default from '../../../Layouts/Default.vue';



defineOptions({
    layout: Default,
})

const props = defineProps({
    categories: {
        type: Object,
        required: true
    }
});

const tableData = computed(() => {
    return Array.isArray(props.categories) ? props.categories : [];
});

const createCategoryForm = useForm({
    name: null,
    description: null,
    is_active: true,
    parent_id: null,
    description: null,
});

const submitCategoryForm = () => {
    createCategoryForm.post(route("category.store"), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const home = ref({
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Categories' }
]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const size = ref({ label: 'Normal', value: 'small' });

const selectedCategory = ref();

const dt = ref();

const exportCSV = () => {
    dt.value.exportCSV();
};

const addModalVisible = ref(false);

const openAddModal = () => {
    createCategoryForm.reset();
    addModalVisible.value = true;
};

const closeAddModal = () => {
    createCategoryForm.reset();
    addModalVisible.value = false;
};

const editModalVisible = ref(false);
const editCategoryForm = useForm({
    id: null,
    name: null,
    parent_id: null,
});

const editCategory = (category) => {
    editCategoryForm.id = category.id;
    editCategoryForm.name = category.name;
    editCategoryForm.parent_id = category.parent_id || null;
    editModalVisible.value = true;
};

const closeEditModal = () => {
    editCategoryForm.reset();
    editModalVisible.value = false;
};

const submitEditForm = () => {
    editCategoryForm.put(route("category.update", { category: editCategoryForm.id }), {
        preserveScroll: true,
    });
};

watch(() => editCategoryForm.wasSuccessful, (success) => {
    if (success) {
        closeEditModal();
    }
});

watch(() => createCategoryForm.wasSuccessful, (success) => {
    if (success) {
        closeAddModal();
    }
});

const availableParentCategories = computed(() => {
    return tableData.value.filter(cat => cat.id !== editCategoryForm.id);
});

const deleteDialogVisible = ref(false);
const categoryToDelete = ref(null);

const confirmDelete = (category) => {
    categoryToDelete.value = category;
    deleteDialogVisible.value = true;
};

const closeDeleteDialog = () => {
    categoryToDelete.value = null;
    deleteDialogVisible.value = false;
};

const deleteCategory = () => {
    if (categoryToDelete.value) {
        editCategoryForm.delete(route("category.destroy", { category: categoryToDelete.value.id }), {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
            },
        });
    }
};
</script>

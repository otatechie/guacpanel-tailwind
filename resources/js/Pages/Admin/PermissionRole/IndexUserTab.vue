<template>

    <Head title="Users" />

    <slot>
        <DataTable :size="size.value" v-model:filters="filters" v-if="users && users.length > 0" :value="users"
            class="text-xs" ref="dt" dataKey="id" v-model:selection="selectedUsers" scrollable scrollHeight="600px"
            paginator :rows="10" :rowsPerPageOptions="[10, 20, 30, 50]" sortMode="multiple" removableSort stripedRows
            tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h2 class="text-xl font-semibold">Manage Users</h2>
                    <div class="flex gap-2">
                        <Button size="small" icon="pi pi-external-link" severity="secondary"
                            label="Export" @click="exportCSVc($event)" outlined />
                        <Link :href="route('home')">
                            <Button icon="pi pi-plus" label="Add User" severity="primary" size="small" />
                        </Link>
                    </div>
                </div>
                <div class="flex justify-end mb-4">
                    <IconField class="w-full max-w-xs">
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText size="small" v-model="filters['global'].value"
                            placeholder="Search users..." class="w-full" />
                    </IconField>
                </div>
            </template>

            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" sortable header="Name" style="min-width: 12rem"></Column>
            <Column field="email" sortable header="Email" style="min-width: 16rem"></Column>
            <Column field="created_at" header="Joined" style="min-width: 10rem">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="Actions" :exportable="false" style="min-width: 8rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" size="small" rounded severity="secondary"
                        class="hover:bg-gray-100" @click="editUser(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <div v-else class="flex items-center justify-center">
            <div class="w-full max-w-lg my-4">
                <Message severity="secondary" variant="outlined" class="text-center">
                    No users found.
                </Message>
            </div>
        </div>
    </slot>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        required: true
    }
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const size = ref({ label: 'Normal', value: 'small' });

const dt = ref(null);

const exportCSVc = () => {
    dt.value.exportCSV();
};

const editUser = (user) => {
    const url = route('users.edit', { id: user.id });
    window.open(url, '_blank');
};

const selectedUsers = ref();

</script>

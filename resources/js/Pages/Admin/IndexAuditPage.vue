<template>
    <Head title="Audits" />

    <div class="px-5">
        <h2 class="text-xl font-heading font-semibold leading-7 text-gray-800 mb-5">Activity</h2>

        <div class="datatable-wrapper">
            <DataTable
                :data="audits.data"
                :columns="columns"
                :options="tableOptions"
                class="modern-datatable"
                ref="dataTable"
            />
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import { onMounted, ref } from 'vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    audits: {
        type: Object,
        required: true
    }
})

const columns = [
    {
        data: 'created_at',
        title: 'Date',
        width: '15%',
        render: (data) => {
            return new Date(data).toLocaleString()
        }
    },
    {
        data: 'user.name',
        title: 'User',
        width: '15%',
        defaultContent: 'System'
    },
    {
        data: 'event',
        title: 'Action',
        width: '10%',
        render: (data) => {
            return data.charAt(0).toUpperCase() + data.slice(1)
        }
    },
    {
        data: 'auditable_type',
        title: 'Type',
        width: '15%',
        render: (data) => {
            return data.split('\\').pop()
        }
    },
]

const tableOptions = {
    ordering: true,
    searching: true,
    responsive: true,
    pageLength: 25,
    dom: '<"datatable-header"lf>rt<"datatable-footer"ip>',
    language: {
        search: "",
        searchPlaceholder: "Search records...",
        lengthMenu: "_MENU_ per page",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "No entries found",
        emptyTable: "No records available",
        paginate: {
            first: "First",
            last: "Last",
            next: "Next",
            previous: "Previous"
        }
    },
    order: [[0, 'desc']]
}
</script>

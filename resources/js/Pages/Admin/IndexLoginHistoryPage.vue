<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '../../Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Datatable from '../../Components/Datatable.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    loginHistory: {
        type: Object,
        required: true
    }
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.loginHistory.current_page,
    per_page: Number(props.loginHistory.per_page),
    total: props.loginHistory.total
})

const columns = [
    columnHelper.accessor('login_at', {
        header: 'Login Time',
        cell: info => info.row.original.login_at_diff
    }),
    columnHelper.accessor('username', {
        header: 'User',
        cell: info => info.row.original.username
    }),
    columnHelper.accessor('user_agent', {
        header: 'Browser & Device',
        cell: info => {
            const device = info.row.original.device_info;
            if (!device) return 'Unknown Device';
            return `${device.browser} on ${device.platform} (${device.device})`;
        }
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => {
            const status = info.row.original.status;
            if (!status) return '-';

            return h('div', { class: 'flex items-center gap-2' }, [
                h('span', {
                    class: status.success
                        ? 'px-2 py-1 text-sm rounded-md bg-green-50 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                        : 'px-2 py-1 text-sm rounded-md bg-red-50 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                }, status.success ? 'Success' : 'Failed'),
                h('span', { class: 'text-sm text-gray-500' }, `(${status.ip})`)
            ]);
        }
    }),
]

// Handle pagination changes
watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('admin.audit'),
        {
            page: newPagination.current_page,
            per_page: Number(newPagination.per_page)
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        }
    )
}, { deep: true })
</script>

<template>
    <Head title="Login History" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden">
            <PageHeader
                title="Login History"
                description="View and monitor login history"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Login History' }
                ]"
            />

            <div class="p-6 dark:bg-gray-900">
                <Datatable
                    :data="loginHistory.data"
                    :columns="columns"
                    :loading="loading"
                    :pagination="pagination"
                    :search-fields="['authenticatable.name', 'user_agent', 'login_at', 'logout_at']"
                    empty-message="No login history records found"
                    empty-description="Login history will appear here"
                    export-file-name="login_history"
                    @update:pagination="pagination = $event"
                />
            </div>
        </section>
    </main>
</template>

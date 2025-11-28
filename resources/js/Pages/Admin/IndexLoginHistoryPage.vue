<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Datatable from '@/Components/Datatable.vue'
import axios from 'axios'

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
    columnHelper.accessor(row => row.login_at_diff, {
        id: 'login_at',
        header: 'Login Time',
        cell: info => info.getValue(),
        meta: {
            ariaLabel: 'Login timestamp'
        }
    }),
    columnHelper.accessor('username', {
        header: 'User',
        cell: info => info.row.original.username,
        meta: {
            ariaLabel: 'Username'
        }
    }),
    columnHelper.accessor('user_agent', {
        header: 'Browser & Device',
        cell: info => {
            const device = info.row.original.device_info
            if (!device) return 'Unknown Device'
            return h(
                'span',
                {
                    'aria-label': `Browser: ${device.browser}, Platform: ${device.platform}, Device: ${device.device}`
                },
                `${device.browser} on ${device.platform} (${device.device})`
            )
        },
        meta: {
            ariaLabel: 'Browser and device information'
        }
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => {
            const status = info.row.original.status
            if (!status) return h('span', { 'aria-label': 'Status not available' }, '-')

            return h(
                'div',
                {
                    class: 'flex items-center gap-2',
                    role: 'status',
                    'aria-label': `Login ${status.success ? 'successful' : 'failed'} `
                },
                [
                    h(
                        'span',
                        {
                            class: status.success
                                ? 'px-2 py-1 text-sm rounded-md bg-green-50 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                : 'px-2 py-1 text-sm rounded-md bg-red-50 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                        },
                        status.success ? 'Success' : 'Failed'
                    )
                ]
            )
        },
        meta: {
            ariaLabel: 'Login status and IP address'
        }
    })
]

const handleBulkDelete = async ({ selectedRows }) => {
    if (!selectedRows?.length) return

    loading.value = true
    const ids = selectedRows.map(row => row.id)

    await axios.post(route('admin.login.history.bulk-destroy'), { ids })
    await router.reload({ preserveScroll: true })
    loading.value = false
}

watch(
    pagination,
    newPagination => {
        loading.value = true
        router.get(
            route('admin.login.history.index'),
            {
                page: newPagination.current_page,
                per_page: Number(newPagination.per_page)
            },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loading.value = false)
            }
        )
    },
    { deep: true }
)
</script>

<template>
    <Head title="Login History" />

    <main class="max-w-7xl mx-auto" aria-labelledby="login-history">
        <div class="container-border">
            <PageHeader
                title="Login History"
                description="View and monitor login history"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'System Settings', href: route('admin.setting.index') },
                    { label: 'Login History' }
                ]" />

            <section class="p-6 bg-[var(--color-bg)]">
                <div
                    class="bg-[var(--color-surface)] rounded-xl shadow-sm border border-[var(--color-border)] p-6">
                    <Datatable
                        :data="loginHistory.data"
                        :columns="columns"
                        :loading="loading"
                        :pagination="pagination"
                        :search-fields="['username', 'user_agent', 'login_at']"
                        empty-message="No login history records found"
                        empty-description="Login history will appear here"
                        export-file-name="login_history"
                        :bulk-delete-route="route('admin.login.history.bulk-destroy')"
                        @update:pagination="pagination = $event"
                        @bulk-delete="handleBulkDelete">
                    </Datatable>
                </div>
            </section>
        </div>
    </main>
</template>

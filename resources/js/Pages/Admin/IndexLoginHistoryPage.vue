<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import axios from 'axios'

defineOptions({
    layout: Default,
})

const props = defineProps({
    loginHistory: { type: Object, required: true },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.loginHistory.current_page,
    per_page: Number(props.loginHistory.per_page),
    total: props.loginHistory.total,
})

const columns = [
    columnHelper.accessor('username', {
        header: 'User',
        cell: info => h('span', { class: 'text-sm font-medium text-foreground' }, info.getValue()),
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => {
            const s = info.row.original.status
            if (!s) return '-'
            return h('span', {
                class: s.success
                    ? 'flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400'
                    : 'flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400',
            }, [
                h('span', { class: s.success ? 'h-1.5 w-1.5 rounded-full bg-green-500' : 'h-1.5 w-1.5 rounded-full bg-red-500' }),
                s.success ? 'Success' : 'Failed',
            ])
        },
    }),
    columnHelper.accessor(row => row.login_at_diff, {
        id: 'login_at',
        header: 'When',
        cell: info => h('span', { class: 'text-xs tabular-nums text-muted-foreground' }, info.getValue()),
    }),
]

const handleBulkDelete = async ({ selectedRows }) => {
    if (!selectedRows?.length) return
    loading.value = true
    await axios.post(route('admin.login.history.bulk-destroy'), { ids: selectedRows.map(r => r.id) })
    await router.reload({ preserveScroll: true })
    loading.value = false
}

watch(pagination, p => {
    loading.value = true
    router.get(route('admin.login.history.index'), { page: p.current_page, per_page: Number(p.per_page) }, {
        preserveState: true, preserveScroll: true, onFinish: () => (loading.value = false),
    })
}, { deep: true })
</script>

<template>
    <Head title="Login history" />

    <main class="mx-auto max-w-7xl" aria-labelledby="login-history">
        <PageHeader
            title="Login history"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Login history' },
            ]" />

        <div class="card p-6">
            <Datatable
                :data="loginHistory.data"
                :columns="columns"
                :loading="loading"
                :pagination="pagination"
                empty-message="No login history"
                empty-description="Records appear as users sign in"
                export-file-name="login_history"
                :bulk-delete-route="route('admin.login.history.bulk-destroy')"
                @update:pagination="pagination = $event"
                @bulk-delete="handleBulkDelete" />
        </div>
    </main>
</template>

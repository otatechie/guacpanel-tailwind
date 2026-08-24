<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Badge from '@js/Components/Badge.vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Button from '@/Components/Button.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    loginHistory: { type: Object, required: true },
    /* bulkDestroy checks manage-login-history, while the page itself opens to
       view-login-history — so the selection UI has to be told apart from it. */
    canManage: { type: Boolean, default: false },
})

const bulkDeleteIds = ref([])
const showBulkDeleteModal = ref(false)

const handleBulkDelete = ({ ids }) => {
    if (!props.canManage || !ids.length) return
    bulkDeleteIds.value = ids
    showBulkDeleteModal.value = true
}

const closeBulkDeleteModal = () => {
    showBulkDeleteModal.value = false
    bulkDeleteIds.value = []
}

const runBulkDelete = () => {
    if (!bulkDeleteIds.value.length) return

    loading.value = true
    router.post(
        route('admin.login.history.bulk-destroy'),
        { ids: bulkDeleteIds.value },
        {
            preserveScroll: true,
            preserveState: false,
            onFinish: () => {
                loading.value = false
                closeBulkDeleteModal()
            },
        }
    )
}

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.loginHistory.current_page,
    per_page: Number(props.loginHistory.per_page),
    total: props.loginHistory.total,
})

const muted = 'text-xs text-muted-foreground'

const columns = [
    // Device and IP are what answer "was this me?". device_info was already being
    // computed on every row by the controller and thrown away for want of a column.
    columnHelper.accessor('username', {
        header: 'User',
        cell: info => h('span', { class: 'text-sm font-medium text-foreground' }, info.getValue()),
    }),
    columnHelper.accessor(row => row.device_info?.browser, {
        id: 'device',
        header: 'Device',
        meta: { narrow: true },
        cell: info => {
            const d = info.row.original.device_info
            return h('span', { class: muted }, d ? `${d.browser} on ${d.platform}` : '-')
        },
    }),
    columnHelper.accessor('ip_address', {
        header: 'IP address',
        meta: { narrow: true },
        cell: info =>
            h('span', { class: 'font-mono text-xs text-muted-foreground' }, info.getValue() || '-'),
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        meta: { narrow: true },
        cell: info => {
            const s = info.row.original.status
            if (!s) return '-'
            return h(
                Badge,
                { dot: true, variant: s.success ? 'success' : 'danger' },
                { default: () => (s.success ? 'Success' : 'Failed') }
            )
        },
    }),
    columnHelper.accessor(row => row.login_at_diff, {
        id: 'login_at',
        header: 'When',
        meta: { narrow: true },
        cell: info =>
            h(
                'span',
                {
                    class: 'text-xs tabular-nums text-muted-foreground',
                    title: info.row.original.login_at_exact,
                },
                info.getValue()
            ),
    }),
]

watch(
    pagination,
    p => {
        loading.value = true
        router.get(
            route('admin.login.history.index'),
            { page: p.current_page, per_page: Number(p.per_page) },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loading.value = false),
            }
        )
    },
    { deep: true }
)
</script>

<template>
    <Head title="Login history" />

    <main class="mx-auto max-w-4xl" aria-labelledby="login-history">
        <PageHeader
            title="Login history"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Login history' },
            ]" />

        <!-- No card. The table already has its own border; wrapping it in a
             second bordered box is decoration, not structure. -->
        <Datatable
            :data="loginHistory.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            empty-message="No login history yet"
            empty-description="Records appear here as users sign in."
            export-file-name="login_history"
            :bulk-delete-route="canManage ? route('admin.login.history.bulk-destroy') : ''"
            @bulk-delete="handleBulkDelete"
            @update:pagination="pagination = $event" />

        <Modal :show="showBulkDeleteModal" size="sm" @close="closeBulkDeleteModal">
            <template #title>Delete login history</template>
            <template #default>
                <p class="text-foreground text-sm font-medium">
                    {{ bulkDeleteIds.length }}
                    {{ bulkDeleteIds.length === 1 ? 'record' : 'records' }}
                </p>
                <p class="text-muted-foreground mt-2 text-sm">
                    These sign-in records are erased permanently. This cannot be undone.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="closeBulkDeleteModal">
                        Cancel
                    </Button>
                    <Button variant="danger" size="sm" :disabled="loading" @click="runBulkDelete">
                        {{ loading ? 'Deleting...' : 'Delete' }}
                    </Button>
                </div>
            </template>
        </Modal>
    </main>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, h } from 'vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import { createColumnHelper } from '@tanstack/vue-table'

defineOptions({
    layout: Default,
})

const props = defineProps({
    sessions: { type: Object, required: true },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.sessions.current_page,
    per_page: Number(props.sessions.per_page),
    total: props.sessions.total,
})

const showTerminateModal = ref(false)
const selectedSession = ref(null)
const form = useForm({})

const closeModal = () => {
    showTerminateModal.value = false
    selectedSession.value = null
}

const confirmTerminate = session => {
    if (session.is_current) return
    selectedSession.value = session
    showTerminateModal.value = true
}

const btnClass = 'cursor-pointer rounded-md p-1.5 text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-muted)] hover:text-red-600 dark:hover:text-red-400'
const iconClass = 'h-3.5 w-3.5'
const svgAttrs = { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.5', 'aria-hidden': 'true' }

const columns = [
    columnHelper.accessor('user', {
        header: 'Session',
        cell: info => {
            const s = info.row.original
            const user = s.user
            const d = s.device_info
            return h('div', { class: 'min-w-0' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h('span', { class: 'text-sm font-medium text-[var(--color-text)]' }, user.name),
                    s.is_current
                        ? h('span', { class: 'flex items-center gap-1 text-[10px] text-green-600 dark:text-green-400' }, [
                            h('span', { class: 'h-1 w-1 rounded-full bg-green-500' }),
                            'You',
                        ])
                        : null,
                ]),
                h('p', { class: 'mt-0.5 text-xs text-[var(--color-text-muted)]' }, `${user.email}`),
                h('p', { class: 'mt-0.5 text-[10px] text-[var(--color-text-muted)]' }, `${d.browser} · ${d.platform}`),
            ])
        },
    }),
    columnHelper.accessor('last_active_diff', {
        header: 'Last active',
        cell: info => h('span', { class: 'text-xs tabular-nums text-[var(--color-text-muted)]' }, info.getValue() || '-'),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info => {
            const session = info.row.original
            if (!session?.id || session.is_current) return null
            return h('div', { class: 'flex justify-end' }, [
                h('button', { class: btnClass, onClick: () => confirmTerminate(session), title: 'Terminate' }, [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M6 18 18 6M6 6l12 12' }),
                    ]),
                ]),
            ])
        },
    }),
]

watch(pagination, p => {
    loading.value = true
    router.get(route('admin.sessions.index'), { page: p.current_page, per_page: Number(p.per_page) }, {
        preserveState: true, preserveScroll: true, onFinish: () => (loading.value = false),
    })
}, { deep: true })
</script>

<template>
    <Head title="Sessions" />

    <main class="mx-auto max-w-7xl" aria-labelledby="active-sessions">
        <PageHeader
            title="Sessions"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Sessions' },
            ]" />

        <div class="card p-6">
            <Datatable
                :data="sessions.data"
                :columns="columns"
                :loading="loading"
                :pagination="pagination"
                empty-message="No active sessions"
                empty-description="Sessions appear as users sign in"
                export-file-name="sessions"
                @update:pagination="pagination = $event" />
        </div>
    </main>

    <Modal :show="showTerminateModal" size="sm" @close="closeModal">
        <template #title>Terminate session</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Terminate session for <span class="font-medium text-(--color-text)">{{ selectedSession?.user?.name }}</span>? They will be logged out immediately.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" :disabled="form.processing" @click="terminateSession">
                    {{ form.processing ? 'Terminating...' : 'Terminate' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

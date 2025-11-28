<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, h } from 'vue'
import Default from '@/Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Datatable from '@/Components/Datatable.vue'
import { createColumnHelper } from '@tanstack/vue-table'

defineOptions({
    layout: Default
})

const props = defineProps({
    sessions: {
        type: Object,
        required: true
    }
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.sessions.current_page,
    per_page: Number(props.sessions.per_page),
    total: props.sessions.total
})

const showTerminateModal = ref(false)
const showTerminateAllModal = ref(false)
const selectedSession = ref(null)
const selectedUser = ref(null)
const form = useForm({})

const closeModal = () => {
    showTerminateModal.value = false
    showTerminateAllModal.value = false
    selectedSession.value = null
    selectedUser.value = null
    form.reset()
}

const confirmTerminate = session => {
    if (session.is_current) return
    selectedSession.value = session
    showTerminateModal.value = true
}

/* const terminateSession = () => {
    if (!selectedSession.value?.id) return

    form.delete(route('admin.sessions.destroy', { sessionId: selectedSession.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showTerminateModal.value = false
            selectedSession.value = null
        },
        onError: () => {
            showTerminateModal.value = false
            selectedSession.value = null
        }
    })
}
 */

const columns = [
    columnHelper.accessor('user', {
        header: 'User',
        cell: info =>
            h('div', {}, [
                h('div', { class: 'font-medium' }, info.row.original.user.name),
                h('div', { class: 'text-sm text-gray-500' }, info.row.original.user.email)
            ])
    }),
    columnHelper.accessor('device_info', {
        header: 'Browser & Device',
        cell: info => {
            const device = info.row.original.device_info
            return h('span', {}, `${device.browser} on ${device.platform} (${device.device})`)
        }
    }),
    columnHelper.accessor('last_active_diff', {
        header: 'Last Active',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.accessor('is_current', {
        header: 'Status',
        cell: info => {
            return h(
                'span',
                {
                    class: info.row.original.is_current
                        ? 'px-2 py-1 text-sm rounded-md bg-green-50 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                        : 'px-2 py-1 text-sm rounded-md bg-gray-50 text-gray-700 dark:bg-gray-900/50 dark:text-gray-400'
                },
                info.row.original.is_current ? 'Current' : 'Active'
            )
        }
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: info => {
            const session = info.row.original
            if (!session?.id || session.is_current) return null

            const terminateButton = h(
                'button',
                {
                    class: 'p-2 text-red-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 cursor-pointer',
                    onClick: () => confirmTerminate(session),
                    title: 'Terminate session'
                },
                [
                    h(
                        'svg',
                        {
                            class: 'w-4 h-4',
                            xmlns: 'http://www.w3.org/2000/svg',
                            fill: 'none',
                            viewBox: '0 0 24 24',
                            'stroke-width': '1.5',
                            stroke: 'currentColor'
                        },
                        [
                            h('path', {
                                'stroke-linecap': 'round',
                                'stroke-linejoin': 'round',
                                d: 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z'
                            })
                        ]
                    )
                ]
            )

            return h(
                'div',
                {
                    class: 'flex items-center gap-2 justify-end'
                },
                [terminateButton]
            )
        }
    })
]

watch(
    pagination,
    newPagination => {
        loading.value = true
        router.get(
            route('admin.sessions.index'),
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
    <Head title="Active Sessions" />

    <main class="max-w-7xl mx-auto" aria-labelledby="active-sessions">
        <div class="container-border">
            <PageHeader
                title="Active Sessions"
                description="Manage system user sessions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'System Settings', href: route('admin.setting.index') },
                    { label: 'Session Management' }
                ]" />

            <section class="p-6 bg-[var(--color-bg)]">
                <div
                    class="bg-[var(--color-surface)] rounded-xl shadow-sm border border-[var(--color-border)] p-6">
                    <Datatable
                        :data="sessions.data"
                        :columns="columns"
                        :loading="loading"
                        :pagination="pagination"
                        :search-fields="['user.name', 'user.email', 'device_info.browser']"
                        empty-message="No active sessions found"
                        empty-description="Active sessions will appear here"
                        export-file-name="sessions"
                        @update:pagination="pagination = $event" />
                </div>
            </section>
        </div>
    </main>

    <!-- Terminate Session Modal -->
    <Modal :show="showTerminateModal" size="md" @close="closeModal">
        <template #title>
            <div class="text-red-600 dark:text-red-400">
                Terminate Session
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-[var(--color-text-muted)]">
                    Are you sure you want to terminate this session? The user will be logged out
                    immediately.
                </p>
                <div
                    v-if="selectedSession"
                    class="bg-[var(--color-surface-muted)] p-4 rounded-lg border border-[var(--color-border)]">
                    <h4 class="text-sm font-medium text-[var(--color-text)] mb-2">
                        Session details:
                    </h4>
                    <dl class="space-y-1">
                        <div class="flex gap-2">
                            <dt class="text-sm text-[var(--color-text-muted)]">User:</dt>
                            <dd class="text-sm text-[var(--color-text)]">
                                {{ selectedSession.user.name }}
                            </dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="text-sm text-[var(--color-text-muted)]">Device:</dt>
                            <dd class="text-sm text-[var(--color-text)]">
                                {{ selectedSession.device_info.browser }} on
                                {{ selectedSession.device_info.platform }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    type="button"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    @click="closeModal">
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn-danger btn-sm"
                    :disabled="form.processing"
                    @click="terminateSession">
                    Yes, terminate session
                </button>
            </div>
        </template>
    </Modal>
</template>

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

const confirmTerminate = (session) => {
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
        cell: info => h('div', {}, [
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
            return h('span', {
                class: info.row.original.is_current
                    ? 'px-2 py-1 text-sm rounded-md bg-green-50 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                    : 'px-2 py-1 text-sm rounded-md bg-gray-50 text-gray-700 dark:bg-gray-900/50 dark:text-gray-400'
            }, info.row.original.is_current ? 'Current' : 'Active')
        }
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: info => {
            const session = info.row.original
            if (!session?.id || session.is_current) return null

            const terminateButton = h('button', {
                class: 'p-2 text-red-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 cursor-pointer',
                onClick: () => confirmTerminate(session),
                title: 'Terminate session'
            }, [
                h('svg', {
                    class: 'w-4 h-4',
                    xmlns: 'http://www.w3.org/2000/svg',
                    fill: 'none',
                    viewBox: '0 0 24 24',
                    'stroke-width': '1.5',
                    stroke: 'currentColor'
                }, [
                    h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        d: 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z'
                    })
                ])
            ])

            return h('div', {
                class: 'flex items-center gap-2 justify-end'
            }, [
                terminateButton,
            ])
        }
    })
]

watch(pagination, newPagination => {
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
            onFinish: () => loading.value = false
        }
    )
}, { deep: true })
</script>

<template>

    <Head title="Active Sessions" />

    <main class="max-w-7xl mx-auto" aria-labelledby="active-sessions">
        <div class="container-border">
            <PageHeader title="Active Sessions" description="Manage system user sessions" :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Settings', href: route('admin.setting.index') },
                { label: 'Sessions' }
            ]" />

            <section class="p-6 dark:bg-gray-900">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <Datatable :data="sessions.data" 
                    :columns="columns" :loading="loading" 
                    :pagination="pagination"
                        :search-fields="['user.name', 'user.email', 'device_info.browser']"
                        empty-message="No active sessions found" empty-description="Active sessions will appear here"
                        export-file-name="sessions" @update:pagination="pagination = $event" />
                </div>
            </section>
        </div>
    </main>

    <!-- Terminate Session Modal -->
    <Modal :show="showTerminateModal" @close="closeModal" size="md">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Terminate Session
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to terminate this session? The user will be logged out immediately.
                </p>
                <div v-if="selectedSession"
                    class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session details:</h4>
                    <dl class="space-y-1">
                        <div class="flex gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">User:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ selectedSession.user.name }}</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Device:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                                {{ selectedSession.device_info.browser }} on {{ selectedSession.device_info.platform }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="terminateSession" type="button" class="btn-danger" :disabled="form.processing">
                    Yes, terminate session
                </button>
            </div>
        </template>
    </Modal>
</template>
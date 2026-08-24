<script setup>
import Button from '@/Components/Button.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, h } from 'vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Badge from '@js/Components/Badge.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import { createColumnHelper } from '@tanstack/vue-table'

defineOptions({
    layout: Default,
})

const props = defineProps({
    sessions: { type: Object, required: true },
    driverSupported: { type: Boolean, default: true },
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

const terminateSession = () => {
    if (!selectedSession.value?.id) return
    form.delete(route('admin.sessions.destroy', selectedSession.value.id), {
        preserveScroll: true,
        onFinish: closeModal,
    })
}

// Sessions outlive their user: a deleted account leaves rows whose user fields
// are all null, which rendered as blank name, blank email and "for null".
const sessionName = session => session?.user?.name || 'Signed-out user'

const muted = 'text-xs text-muted-foreground'

const columns = [
    // One fact per column. Stacking name, email and device in a single cell made
    // every row look identical, which on a screen whose whole job is "which of
    // these should I end?" is the one thing it must not do.
    columnHelper.accessor(row => row.user?.name, {
        id: 'user',
        header: 'User',
        cell: info => {
            const s = info.row.original
            return h('div', { class: 'min-w-0' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h('span', { class: 'text-sm font-medium text-foreground' }, sessionName(s)),
                    s.is_current
                        ? h(
                              Badge,
                              { dot: true, variant: 'success' },
                              { default: () => 'This device' }
                          )
                        : null,
                ]),
                s.user?.email ? h('p', { class: `mt-0.5 ${muted}` }, s.user.email) : null,
            ])
        },
    }),
    columnHelper.accessor(row => row.device_info?.browser, {
        id: 'device',
        header: 'Device',
        meta: { narrow: true },
        cell: info => {
            const d = info.row.original.device_info
            return h('span', { class: muted }, `${d.browser} on ${d.platform}`)
        },
    }),
    columnHelper.accessor('ip_address', {
        header: 'IP address',
        meta: { narrow: true },
        cell: info =>
            h('span', { class: 'font-mono text-xs text-muted-foreground' }, info.getValue() || '-'),
    }),
    columnHelper.accessor('last_active_diff', {
        header: 'Last active',
        meta: { narrow: true },
        cell: info =>
            h(
                'span',
                {
                    class: 'text-xs tabular-nums text-muted-foreground',
                    title: info.row.original.last_active_exact,
                },
                info.getValue() || '-'
            ),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info => {
            const session = info.row.original
            if (!session?.id || session.is_current) return null
            // A label, not a lone glyph. There is exactly one action here and it
            // is not obvious from an icon which one it is.
            return h('div', { class: 'flex justify-end' }, [
                h(
                    Button,
                    {
                        variant: 'secondary',
                        size: 'xs',
                        onClick: () => confirmTerminate(session),
                        'aria-label': `Sign out ${sessionName(session)} on ${session.device_info.browser}`,
                    },
                    { default: () => 'Sign out' }
                ),
            ])
        },
    }),
]

watch(
    pagination,
    p => {
        loading.value = true
        router.get(
            route('admin.sessions.index'),
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
    <Head title="Sessions" />

    <main class="mx-auto max-w-4xl" aria-labelledby="active-sessions">
        <PageHeader
            title="Sessions"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Sessions' },
            ]" />

        <Alert v-if="!driverSupported" type="warning" class="mb-6">
            Sessions are only tracked when the session driver is set to
            <code class="font-mono">database</code>
            . Set SESSION_DRIVER=database in your .env file to see active sessions here.
        </Alert>

        <Datatable
            :data="sessions.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            empty-message="No active sessions"
            empty-description="Sessions appear here as users sign in."
            export-file-name="sessions"
            @update:pagination="pagination = $event" />
    </main>

    <Modal
        :show="showTerminateModal"
        size="sm"
        description="They will be signed out immediately on that device."
        @close="closeModal">
        <template #title>Sign out this session</template>
        <template #default>
            <p class="text-foreground text-sm font-medium">
                {{ sessionName(selectedSession) }}
                <span v-if="selectedSession?.user?.email" class="text-muted-foreground font-normal">
                    · {{ selectedSession.user.email }}
                </span>
            </p>
            <!-- Device and IP are what the reader checks before confirming. -->
            <p v-if="selectedSession" class="text-muted-foreground mt-1 text-xs">
                {{ selectedSession.device_info.browser }} on
                {{ selectedSession.device_info.platform }}
                <span class="font-mono">· {{ selectedSession.ip_address || 'unknown IP' }}</span>
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="form.processing"
                    @click="terminateSession">
                    {{ form.processing ? 'Signing out...' : 'Sign out' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>

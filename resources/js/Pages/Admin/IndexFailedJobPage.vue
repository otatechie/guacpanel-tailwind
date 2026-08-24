<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import axios from 'axios'
import Badge from '@js/Components/Badge.vue'
import Button from '@/Components/Button.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import { RotateCcwIcon, Trash2Icon } from '@lucide/vue'

defineOptions({ layout: Default })

const props = defineProps({
    failedJobs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    /* Retrying and deleting need manage-failed-jobs; the page itself opens to
       view-failed-jobs, so the controls are gated separately. */
    canManage: { type: Boolean, default: false },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.failedJobs.current_page,
    per_page: Number(props.failedJobs.per_page),
    total: props.failedJobs.total,
})

const detail = ref(null)
const jobToDelete = ref(null)
const showRetryAll = ref(false)

const openDetail = async job => {
    const { data } = await axios.get(route('admin.failed-jobs.show', { uuid: job.uuid }))
    detail.value = data
}

const retryJob = job => {
    if (!props.canManage) return
    loading.value = true
    router.post(
        route('admin.failed-jobs.retry', { uuid: job.uuid }),
        {},
        { preserveScroll: true, onFinish: () => (loading.value = false) }
    )
}

const deleteJob = () => {
    if (!jobToDelete.value) return
    loading.value = true
    router.delete(route('admin.failed-jobs.destroy', { uuid: jobToDelete.value.uuid }), {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false
            jobToDelete.value = null
        },
    })
}

const retryAll = () => {
    loading.value = true
    router.post(
        route('admin.failed-jobs.retry-all'),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                loading.value = false
                showRetryAll.value = false
            },
        }
    )
}

const jobActions = job =>
    props.canManage
        ? [
              { label: 'Retry job', icon: RotateCcwIcon, onSelect: () => retryJob(job) },
              {
                  label: 'Delete job',
                  icon: Trash2Icon,
                  variant: 'destructive',
                  onSelect: () => (jobToDelete.value = job),
              },
          ]
        : []

const columns = [
    columnHelper.accessor('job', {
        header: 'Job',
        cell: info =>
            h('div', { class: 'min-w-0' }, [
                h('p', { class: 'text-foreground truncate text-sm' }, info.getValue()),
                h(
                    'p',
                    { class: 'text-muted-foreground mt-0.5 truncate text-xs' },
                    info.row.original.exception
                ),
            ]),
    }),
    columnHelper.accessor('queue', {
        header: 'Queue',
        cell: info => h(Badge, { variant: 'neutral' }, () => info.getValue()),
    }),
    columnHelper.accessor('failed_at', {
        header: 'Failed',
        cell: info => h('span', { class: 'text-muted-foreground text-xs' }, info.getValue()),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info =>
            h(RowActions, {
                actions: jobActions(info.row.original),
                label: `Actions for ${info.row.original.job}`,
            }),
    }),
]

watch(pagination, next => {
    loading.value = true
    router.get(
        route('admin.failed-jobs.index'),
        { page: next.current_page, per_page: Number(next.per_page) },
        { preserveState: true, preserveScroll: true, onFinish: () => (loading.value = false) }
    )
})
</script>

<template>
    <Head title="Failed jobs" />

    <main class="mx-auto w-full max-w-7xl px-4 py-8">
        <PageHeader
            title="Failed jobs"
            description="Queued work that threw and stopped. Retrying puts a job back on the queue unchanged."
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Failed jobs' },
            ]">
            <template #actions>
                <Button
                    v-if="canManage && failedJobs.total"
                    variant="secondary"
                    size="sm"
                    @click="showRetryAll = true">
                    Retry all
                </Button>
            </template>
        </PageHeader>

        <Datatable
            :data="failedJobs.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            :filters="filters"
            route-name="admin.failed-jobs.index"
            row-clickable
            :row-label="job => `Show the exception for ${job.job}`"
            empty-message="Nothing has failed"
            empty-description="Jobs that throw and exhaust their retries appear here."
            export-file-name="failed_jobs"
            @row-click="openDetail"
            @update:pagination="pagination = $event" />

        <!-- The exception, in full. The table shows only its first line. -->
        <Modal :show="Boolean(detail)" size="2xl" @close="detail = null">
            <template #title>{{ detail?.job }}</template>
            <template #default>
                <p class="text-muted-foreground text-xs">
                    {{ detail?.queue }} · failed {{ detail?.failed_at }}
                </p>
                <pre
                    class="bg-muted text-foreground mt-3 max-h-96 overflow-auto rounded-md p-3 text-xs whitespace-pre-wrap"
                    >{{ detail?.exception }}</pre
                >
            </template>
        </Modal>

        <Modal :show="Boolean(jobToDelete)" size="sm" @close="jobToDelete = null">
            <template #title>Delete failed job</template>
            <template #default>
                <p class="text-foreground text-sm font-medium">{{ jobToDelete?.job }}</p>
                <p class="text-muted-foreground mt-2 text-sm">
                    The record is removed and the work is not retried. This cannot be undone.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="jobToDelete = null">
                        Cancel
                    </Button>
                    <Button variant="danger" size="sm" :disabled="loading" @click="deleteJob">
                        {{ loading ? 'Deleting...' : 'Delete' }}
                    </Button>
                </div>
            </template>
        </Modal>

        <Modal :show="showRetryAll" size="sm" @close="showRetryAll = false">
            <template #title>Retry all failed jobs</template>
            <template #default>
                <p class="text-muted-foreground text-sm">
                    All {{ failedJobs.total }} failed jobs go back on the queue. Anything that fails
                    for the same reason will land here again.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="showRetryAll = false">
                        Cancel
                    </Button>
                    <Button variant="primary" size="sm" :disabled="loading" @click="retryAll">
                        {{ loading ? 'Retrying...' : 'Retry all' }}
                    </Button>
                </div>
            </template>
        </Modal>
    </main>
</template>

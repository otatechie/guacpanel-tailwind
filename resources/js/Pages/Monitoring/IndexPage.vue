<script setup>
import Button from '@/Components/Button.vue'
import { Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon, QuestionMarkCircleIcon } from '@heroicons/vue/24/outline'

defineOptions({
    layout: Default,
})

const isRunning = ref(false)

const props = defineProps({
    healthChecks: {
        type: Object,
        required: true,
    },
})

const statusIcon = s => {
    const key = s?.toLowerCase()
    if (key === 'ok') return CheckCircleIcon
    if (key === 'warning') return ExclamationTriangleIcon
    if (key === 'failed' || key === 'crashed') return XCircleIcon
    return QuestionMarkCircleIcon
}

const statusColor = s => {
    const key = s?.toLowerCase()
    if (key === 'ok') return 'text-green-600 dark:text-green-400'
    if (key === 'warning') return 'text-amber-600 dark:text-amber-400'
    if (key === 'failed' || key === 'crashed') return 'text-red-600 dark:text-red-400'
    return 'text-(--color-text-muted)'
}

const statusDot = s => {
    const key = s?.toLowerCase()
    if (key === 'ok') return 'bg-green-500'
    if (key === 'warning') return 'bg-amber-500'
    if (key === 'failed' || key === 'crashed') return 'bg-red-500'
    return 'bg-(--color-border-strong)'
}

const results = computed(() => props.healthChecks?.results || [])
const lastChecked = computed(() => props.healthChecks.lastRanAtFormatted || null)

const counts = computed(() => {
    const map = { ok: 0, warning: 0, failed: 0, crashed: 0 }
    for (const r of results.value) {
        const k = (r.status || '').toLowerCase()
        if (k in map) map[k]++
    }
    return { ...map, total: results.value.length }
})

const allOk = computed(() => counts.value.total > 0 && counts.value.ok === counts.value.total)

const runHealthChecks = () => {
    if (isRunning.value) return
    isRunning.value = true
    router.post(route('admin.health.refresh'), {}, {
        preserveScroll: true,
        onFinish: () => { isRunning.value = false },
    })
}
</script>

<template>
    <Head title="System Health" />

    <main class="mx-auto max-w-7xl">
        <PageHeader
            title="System Health"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Health Status' },
            ]">
            <template #actions>
                <Button variant="primary" size="sm" :disabled="isRunning" :aria-busy="isRunning" @click="runHealthChecks">
                    {{ isRunning ? 'Running...' : 'Run checks' }}
                </Button>
            </template>
        </PageHeader>

        <!-- Summary bar -->
        <div v-if="results.length" class="mb-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-(--color-text-muted)">
            <span v-if="lastChecked">Updated {{ lastChecked }}</span>
            <span class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>{{ counts.ok }} ok</span>
            <span v-if="counts.warning" class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>{{ counts.warning }} warning</span>
            <span v-if="counts.failed" class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>{{ counts.failed }} failed</span>
            <span v-if="counts.crashed" class="flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>{{ counts.crashed }} crashed</span>
        </div>

        <!-- Results -->
        <div v-if="isRunning" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="n in 6" :key="n" class="card animate-pulse p-4">
                <div class="flex items-center gap-3">
                    <div class="h-4 w-4 rounded-full bg-(--color-surface-muted)"></div>
                    <div class="h-3 w-24 rounded bg-(--color-surface-muted)"></div>
                </div>
            </div>
        </div>

        <div v-else-if="results.length === 0" class="card px-5 py-12 text-center">
            <p class="text-sm text-(--color-text-muted)">No health checks available. Click "Run checks" to start.</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="result in results"
                :key="result.label"
                class="card flex items-start gap-3 px-4 py-3.5">
                <component
                    :is="statusIcon(result.status)"
                    :class="['mt-0.5 h-4 w-4 shrink-0', statusColor(result.status)]"
                    aria-hidden="true" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-(--color-text)">{{ result.label }}</p>
                        <span class="shrink-0 text-[10px] font-medium uppercase tabular-nums" :class="statusColor(result.status)">
                            {{ result.status }}
                        </span>
                    </div>
                    <p v-if="result.notificationMessage" class="mt-1 text-xs text-(--color-text-muted)">
                        {{ result.notificationMessage }}
                    </p>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { Head, router } from '@inertiajs/vue3'
import { computed, h, ref } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Badge from '@js/Components/Badge.vue'
import {
    CircleCheckIcon,
    CircleQuestionMarkIcon,
    CircleXIcon,
    TriangleAlertIcon,
} from '@lucide/vue'
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
    if (key === 'ok') return CircleCheckIcon
    if (key === 'warning') return TriangleAlertIcon
    if (key === 'failed' || key === 'crashed') return CircleXIcon
    return CircleQuestionMarkIcon
}

const statusColor = s => {
    const key = s?.toLowerCase()
    if (key === 'ok') return 'text-green-600 dark:text-green-400'
    if (key === 'warning') return 'text-amber-600 dark:text-amber-400'
    if (key === 'failed' || key === 'crashed') return 'text-red-600 dark:text-red-400'
    return 'text-muted-foreground'
}

// Broken first. A grid in storage order makes the reader scan every row to
// find the failures the summary bar just told them exist.
const severity = { crashed: 0, failed: 1, warning: 2, ok: 4 }
const rank = s => severity[s?.toLowerCase()] ?? 3

const results = computed(() =>
    [...(props.healthChecks?.results || [])].sort((a, b) => rank(a.status) - rank(b.status))
)
const lastChecked = computed(() => props.healthChecks.lastRanAtFormatted || null)

// Spatie writes its messages in markdown, so `production` arrives with the
// backticks intact. Nothing rendered them, so they showed up as literal ticks.
// Built with h() rather than a template loop: Prettier wraps an inline <code>
// and Vue's condense mode turns those newlines into padding inside the span.
const CheckMessage = props =>
    h(
        'p',
        { class: 'text-muted-foreground mt-0.5 text-xs' },
        (props.message || '')
            .split(/`([^`]+)`/)
            .map((text, i) =>
                i % 2 === 1 ? h('code', { class: 'text-foreground font-mono' }, text) : text
            )
    )

// "Used Disk Space ✓" says nothing about how full the disk is. The summary
// carries that, except where it just repeats the status word or the message.
const summaryFor = result =>
    result.status?.toLowerCase() === 'ok' && result.shortSummary?.toLowerCase() !== 'ok'
        ? result.shortSummary
        : null

const counts = computed(() => {
    const map = { ok: 0, warning: 0, failed: 0, crashed: 0 }
    for (const r of results.value) {
        const k = (r.status || '').toLowerCase()
        if (k in map) map[k]++
    }
    return { ...map, total: results.value.length }
})

const runHealthChecks = () => {
    if (isRunning.value) return
    isRunning.value = true
    router.post(
        route('admin.health.refresh'),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isRunning.value = false
            },
        }
    )
}
</script>

<template>
    <Head title="System health" />

    <main class="mx-auto max-w-4xl">
        <PageHeader
            title="System health"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'System health' },
            ]">
            <template #actions>
                <Button
                    variant="primary"
                    size="sm"
                    :disabled="isRunning"
                    :aria-busy="isRunning"
                    @click="runHealthChecks">
                    {{ isRunning ? 'Running...' : 'Run checks' }}
                </Button>
            </template>
        </PageHeader>

        <!-- Summary bar -->
        <div
            v-if="results.length"
            class="text-muted-foreground mb-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
            <span v-if="lastChecked" :title="healthChecks.lastRanAt">
                Updated {{ lastChecked }}
            </span>
            <Badge dot variant="success">{{ counts.ok }} ok</Badge>
            <Badge v-if="counts.warning" dot variant="warning">{{ counts.warning }} warning</Badge>
            <Badge v-if="counts.failed" dot variant="danger">{{ counts.failed }} failed</Badge>
            <Badge v-if="counts.crashed" dot variant="danger">{{ counts.crashed }} crashed</Badge>
        </div>

        <!-- Results -->
        <div v-if="isRunning" class="grid grid-cols-1 gap-1 sm:grid-cols-2">
            <div v-for="n in 6" :key="n" class="flex animate-pulse items-center gap-3.5 py-3">
                <div class="bg-muted h-4 w-4 shrink-0 rounded-full"></div>
                <div class="bg-muted h-3 w-32 rounded"></div>
            </div>
        </div>

        <p v-else-if="results.length === 0" class="text-muted-foreground py-12 text-center text-sm">
            No checks have run yet. Choose "Run checks" to start.
        </p>

        <div v-else class="grid grid-cols-1 gap-x-10 gap-y-1 sm:grid-cols-2">
            <div
                v-for="result in results"
                :key="result.label"
                class="flex items-start gap-3.5 py-3">
                <component
                    :is="statusIcon(result.status)"
                    :class="['mt-0.5 h-4 w-4 shrink-0', statusColor(result.status)]"
                    role="img"
                    :aria-label="result.status" />
                <div class="min-w-0">
                    <!-- The icon already carries "ok"; spelling it out again only
                         earns its space when the state needs distinguishing. -->
                    <p class="text-foreground text-sm font-medium">
                        {{ result.label }}
                        <span
                            v-if="result.status?.toLowerCase() !== 'ok'"
                            :class="['ml-1.5 text-xs font-normal', statusColor(result.status)]">
                            {{ result.status }}
                        </span>
                        <span
                            v-else-if="summaryFor(result)"
                            class="text-muted-foreground ml-1.5 text-xs font-normal tabular-nums">
                            {{ summaryFor(result) }}
                        </span>
                    </p>
                    <CheckMessage
                        v-if="result.notificationMessage"
                        :message="result.notificationMessage" />
                </div>
            </div>
        </div>
    </main>
</template>

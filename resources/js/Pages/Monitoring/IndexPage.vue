<script setup>
import { Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'

defineOptions({
    layout: Default
})

const isRunning = ref(false)

const props = defineProps({
    healthChecks: {
        type: Object,
        required: true
    }
})

const STATUS_CONFIGS = {
    ok: {
        color: 'text-green-500',
        bgLight: 'bg-green-100 dark:bg-green-500/10',
        icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    warning: {
        color: 'text-yellow-500',
        bgLight: 'bg-yellow-100 dark:bg-yellow-500/10',
        icon: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
    },
    failed: {
        color: 'text-red-500',
        bgLight: 'bg-red-100 dark:bg-red-500/10',
        icon: 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    crashed: {
        color: 'text-red-500',
        bgLight: 'bg-red-100 dark:bg-red-500/10',
        icon: 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    default: {
        color: 'text-gray-500',
        bgLight: 'bg-gray-100 dark:bg-gray-500/10',
        icon: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    }
}

const getStatusConfig = status => {
    const key = status?.toLowerCase() || 'default'
    return STATUS_CONFIGS[key] || STATUS_CONFIGS.default
}

const results = computed(() => props.healthChecks?.results || [])

const groupedResults = computed(() => {
    return results.value.reduce((acc, result) => {
        const status = (result.status || 'default').toLowerCase()
        if (!acc[status]) acc[status] = []
        acc[status].push(result)
        return acc
    }, {})
})

const counts = computed(() => {
    const map = { ok: 0, warning: 0, failed: 0, crashed: 0 }
    for (const r of results.value) {
        const k = (r.status || '').toLowerCase()
        if (k in map) map[k]++
    }
    const total = results.value.length
    return { ...map, total }
})

const lastCheckedForHumans = computed(() => {
    return props.healthChecks.lastRanAtFormatted || null
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
            }
        }
    )
}
</script>

<template>
    <Head title="System Health" />

    <main class="max-w-7xl mx-auto" role="main">
        <div class="container-border">
            <PageHeader
                title="System Health"
                description="Monitor system health and performance metrics"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'System Settings', href: route('admin.setting.index') },
                    { label: 'Health Status' }
                ]">
                <template #actions>
                    <button
                        class="btn btn-sm btn-primary w-full sm:w-auto"
                        :disabled="isRunning"
                        @click="runHealthChecks">
                        <svg
                            v-if="isRunning"
                            class="animate-spin h-3 w-3 mr-2"
                            fill="none"
                            viewBox="0 0 24 24">
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4" />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ isRunning ? 'Running checks...' : 'Run Health Checks' }}
                    </button>
                </template>
            </PageHeader>

            <section class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div
                        v-if="Object.keys(groupedResults).length > 0"
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                        <div
                            class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Updated {{ lastCheckedForHumans }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                OK: {{ counts.ok }}
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                                Warn: {{ counts.warning }}
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                Fail: {{ counts.failed }}
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                                Crash: {{ counts.crashed }}
                            </span>
                            <span
                                class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                Total: {{ counts.total }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                        <template v-if="isRunning">
                            <div
                                v-for="n in 6"
                                :key="n"
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 animate-pulse">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700"></div>
                                    <div class="flex-1 space-y-3">
                                        <div
                                            class="h-4 bg-gray-100 dark:bg-gray-700 rounded w-1/3"></div>
                                        <div
                                            class="h-3 bg-gray-100 dark:bg-gray-700 rounded w-2/3"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div
                            v-else-if="Object.keys(groupedResults).length === 0"
                            class="col-span-full flex flex-col items-center justify-center py-12 px-6 text-center">
                            <div
                                class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg
                                    class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">
                                No Health Checks Available
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md">
                                Click the button above to run initial health checks and monitor your
                                system.
                            </p>
                        </div>

                        <template v-for="(group, status) in groupedResults" v-else :key="status">
                            <div
                                v-for="result in group"
                                :key="result.label"
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 transition-all duration-200 hover:shadow-md border border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-3">
                                    <div
                                        :class="[
                                            'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center',
                                            getStatusConfig(result.status).bgLight
                                        ]">
                                        <svg
                                            class="w-5 h-5"
                                            :class="getStatusConfig(result.status).color"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="getStatusConfig(result.status).icon" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3
                                                class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ result.label }}
                                            </h3>
                                            <span
                                                :class="[
                                                    'px-2 py-0.5 rounded-full text-xs uppercase',
                                                    getStatusConfig(result.status).color,
                                                    getStatusConfig(result.status).bgLight
                                                ]">
                                                {{ result.status }}
                                            </span>
                                        </div>
                                        <div
                                            v-if="result.notificationMessage"
                                            class="mt-3 pt-3 text-xs border-t border-gray-100 dark:border-gray-700">
                                            <p class="text-gray-500 dark:text-gray-400">
                                                {{ result.notificationMessage }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>

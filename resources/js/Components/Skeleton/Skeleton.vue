<script setup>
defineProps({
    variant: {
        type: String,
        default: 'line',
        validator: v => ['line', 'card', 'table'].includes(v),
    },
    width: {
        type: String,
        default: '100%',
    },
    height: {
        type: String,
        default: '1rem',
    },
    rounded: {
        type: String,
        default: 'md',
        validator: v => ['none', 'sm', 'md', 'lg', 'full'].includes(v),
    },
    lines: {
        type: Number,
        default: 2,
    },
    size: {
        type: String,
        default: '2.5rem',
    },
    rows: {
        type: Number,
        default: 5,
    },
    columns: {
        type: Number,
        default: 4,
    },
})
</script>

<template>
    <div
        v-if="variant === 'line'"
        class="skeleton"
        :class="`rounded-${rounded}`"
        :style="{ width, height }" />

    <div v-else-if="variant === 'card'" class="card p-4">
        <div class="flex gap-3">
            <div class="skeleton shrink-0 rounded-full" :style="{ width: size, height: size }" />
            <div class="flex-1">
                <div class="skeleton mb-2 rounded-md" style="width: 40%; height: 1rem" />
                <div class="space-y-2">
                    <div
                        v-for="i in lines"
                        :key="i"
                        class="skeleton rounded-md"
                        :style="{ width: i === lines ? '60%' : '100%', height: '0.75rem' }" />
                </div>
            </div>
        </div>
    </div>

    <div v-else-if="variant === 'table'" class="border-border overflow-hidden rounded-lg border">
        <table class="w-full">
            <thead class="bg-muted">
                <tr>
                    <th v-for="col in columns" :key="col" class="px-4 py-3 text-left">
                        <div class="skeleton rounded-md" style="width: 60%; height: 0.75rem" />
                    </th>
                </tr>
            </thead>
            <tbody class="divide-border divide-y">
                <tr v-for="row in rows" :key="row" class="bg-card">
                    <td v-for="col in columns" :key="col" class="px-4 py-3">
                        <div
                            class="skeleton rounded-md"
                            :style="{ width: col === 1 ? '80%' : '60%', height: '0.75rem' }" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.skeleton {
    background: linear-gradient(90deg, var(--muted) 25%, var(--border) 50%, var(--muted) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}
</style>

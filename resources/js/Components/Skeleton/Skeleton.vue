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

    <div
        v-else-if="variant === 'card'"
        class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-4">
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

    <div
        v-else-if="variant === 'table'"
        class="overflow-hidden rounded-lg border border-[var(--color-border)]">
        <table class="w-full">
            <thead class="bg-[var(--color-surface-muted)]">
                <tr>
                    <th v-for="col in columns" :key="col" class="px-4 py-3 text-left">
                        <div class="skeleton rounded-md" style="width: 60%; height: 0.75rem" />
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
                <tr v-for="row in rows" :key="row" class="bg-[var(--color-surface)]">
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
    background: linear-gradient(
        90deg,
        var(--color-surface-muted) 25%,
        var(--color-border) 50%,
        var(--color-surface-muted) 75%
    );
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

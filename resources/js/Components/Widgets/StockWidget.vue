<!--
  StockWidget - Stock price card with company logo and change indicator

  Features:
  - Displays company logo/icon with branded background
  - Shows stock symbol, company name, and price
  - Change percentage with up/down arrow indicator
  - Color-coded positive/negative changes (green/red)
  - Responsive sizing options (sm, md, lg)
  - Smooth hover transitions

  Usage:
  <StockWidget
    :stock="{ symbol: 'AAPL', name: 'Apple Inc', price: '173.25', change: 0.86, currency: '$' }"
    src="https://example.com/apple-logo.svg"
    alt="Apple Logo"
    size="lg" />
-->
<script setup>
defineProps({
    stock: {
        type: Object,
        required: true
    },
    size: {
        type: String,
        default: 'md',
        validator: value => ['sm', 'md', 'lg'].includes(value)
    },
    src: {
        type: String,
        default: ''
    },
    alt: {
        type: String,
        default: ''
    }
})

const sizeClasses = {
    sm: {
        card: 'p-4',
        icon: 'w-8 h-8',
        symbol: 'text-xs',
        name: 'text-xs',
        price: 'text-lg',
        change: 'text-xs'
    },
    md: {
        card: 'p-5',
        icon: 'w-10 h-10',
        symbol: 'text-sm',
        name: 'text-xs',
        price: 'text-2xl',
        change: 'text-sm'
    },
    lg: {
        card: 'p-6',
        icon: 'w-12 h-12',
        symbol: 'text-base',
        name: 'text-sm',
        price: 'text-3xl',
        change: 'text-base'
    }
}
</script>

<template>
    <article
        class="group relative bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] shadow-xs hover:border-opacity-70 transition-all duration-200"
        :class="sizeClasses[size].card">
        <!-- Icon -->
        <div class="mb-4">
            <div
                class="flex items-center justify-center rounded-lg bg-[var(--color-surface-muted)]"
                :class="sizeClasses[size].icon">
                <img :src="src" :alt="alt" class="w-full h-full object-contain p-1.5" />
            </div>
        </div>

        <!-- Stock info -->
        <div class="space-y-1 mb-4">
            <h3
                class="font-medium text-[var(--color-text)] tracking-tight"
                :class="sizeClasses[size].symbol">
                {{ stock.symbol }}
            </h3>
            <p
                class="text-[var(--color-text-muted)] font-normal"
                :class="sizeClasses[size].name">
                {{ stock.name }}
            </p>
        </div>

        <!-- Price and change -->
        <div class="flex items-end justify-between gap-3">
            <div class="font-semibold text-[var(--color-text)] tracking-tight" :class="sizeClasses[size].price">
                {{ stock.currency }}{{ stock.price }}
            </div>

            <div
                class="flex items-center gap-1 font-medium shrink-0"
                :class="[
                    sizeClasses[size].change,
                    {
                        'text-green-600 dark:text-green-400': stock.change > 0,
                        'text-red-600 dark:text-red-400': stock.change < 0,
                        'text-gray-600 dark:text-gray-400': stock.change === 0
                    }
                ]">
                <svg
                    v-if="stock.change !== 0"
                    class="shrink-0"
                    :class="[
                        sizeClasses[size].change === 'text-xs' ? 'w-3 h-3' : 'w-4 h-4',
                        { 'rotate-180': stock.change < 0 }
                    ]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <span>{{ Math.abs(stock.change).toFixed(2) }}%</span>
            </div>
        </div>
    </article>
</template>

<script setup>
defineProps({
    stock: {
        type: Object,
        required: true,
        default: () => ({
            symbol: '',
            name: '',
            price: '0.00',
            change: 0,
            currency: '$'
        })
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    src: {
        type: String,
        default: ''
    },
    alt: {
        type: String,
        default: ''
    },
    bgColor: {
        type: String,
        default: 'gray'
    }
})

const sizeClasses = {
    sm: {
        card: 'p-3',
        icon: 'w-6 h-6',
        symbol: 'text-sm',
        name: 'text-xs',
        price: 'text-lg',
        change: 'text-xs'
    },
    md: {
        card: 'p-5',
        icon: 'w-8 h-8',
        symbol: 'text-sm',
        name: 'text-sm',
        price: 'text-xl',
        change: 'text-sm'
    },
    lg: {
        card: 'p-6',
        icon: 'w-10 h-10',
        symbol: 'text-lg',
        name: 'text-base',
        price: 'text-2xl',
        change: 'text-base'
    }
}
</script>

<template>
    <article 
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700"
        :class="sizeClasses[size].card"
    >
        <div class="flex items-center gap-3 mb-3">
            <div 
                :class="`bg-${bgColor}-100 dark:bg-${bgColor}-900/30 p-2 rounded-full flex items-center justify-center`"
            >
                <img 
                    :src="src" 
                    :alt="alt"
                    :class="sizeClasses[size].icon"
                >
            </div>
            <div>
                <h3 
                    class="font-semibold text-gray-900 dark:text-white"
                    :class="sizeClasses[size].symbol"
                >
                    {{ stock.symbol }}
                </h3>
                <p 
                    class="text-gray-500 dark:text-gray-400"
                    :class="sizeClasses[size].name"
                >
                    {{ stock.name }}
                </p>
            </div>
        </div>
        <div class="flex justify-between items-end">
            <strong 
                class="font-bold text-gray-900 dark:text-white"
                :class="sizeClasses[size].price"
            >
                {{ stock.currency }}{{ stock.price }}
            </strong>
            <span 
                :class="[
                    sizeClasses[size].change,
                    'flex items-center font-medium',
                    {
                        'text-emerald-600 dark:text-emerald-400': stock.change > 0,
                        'text-rose-600 dark:text-rose-400': stock.change < 0,
                        'text-gray-600 dark:text-gray-400': stock.change === 0
                    }
                ]"
            >
                <svg 
                    class="w-4 h-4 mr-1" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    xmlns="http://www.w3.org/2000/svg"
                    :class="{ 'rotate-180': stock.change < 0 }"
                >
                    <path 
                        d="M12 5L20 13L18.6 14.4L13 8.8V19H11V8.8L5.4 14.4L4 13L12 5Z"
                        fill="currentColor" 
                    />
                </svg>
                {{ Math.abs(stock.change).toFixed(2) }}%
            </span>
        </div>
    </article>
</template> 
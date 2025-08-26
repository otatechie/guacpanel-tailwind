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
        default: 'blue'
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
        card: 'p-4',
        icon: 'w-8 h-8',
        symbol: 'text-sm',
        name: 'text-sm',
        price: 'text-xl',
        change: 'text-sm'
    },
    lg: {
        card: 'p-5',
        icon: 'w-10 h-10',
        symbol: 'text-lg',
        name: 'text-base',
        price: 'text-2xl',
        change: 'text-base'
    }
}

const colorClasses = {
    blue: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700/30',
    green: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700/30',
    red: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700/30',
    yellow: 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700/30',
    purple: 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700/30',
    gray: 'bg-gray-50 dark:bg-gray-900/20 border-gray-200 dark:border-gray-700/30'
}
</script>

<template>
    <article 
        class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600"
        :class="sizeClasses[size].card"
    >
        <!-- Header with icon and stock info -->
        <div class="flex items-center gap-3 mb-4">
            <!-- Icon container with enhanced styling -->
            <div class="relative">
                <div class="absolute inset-0 rounded-xl opacity-60 group-hover:opacity-80 transition-opacity duration-300 bg-gradient-to-br from-gray-400/20 to-gray-600/20 blur-lg"></div>
                
                <div class="relative flex items-center justify-center rounded-xl border transition-all duration-300 group-hover:scale-105 shadow-sm"
                     :class="[colorClasses[bgColor], sizeClasses[size].icon]">
                    <img 
                        :src="src" 
                        :alt="alt"
                        class="w-full h-full object-contain p-1"
                    >
                </div>
            </div>
            
            <!-- Stock symbol and name -->
            <div class="flex-1 min-w-0">
                <h3 
                    class="font-semibold text-gray-900 dark:text-white truncate"
                    :class="sizeClasses[size].symbol"
                >
                    {{ stock.symbol }}
                </h3>
                <p 
                    class="text-gray-500 dark:text-gray-400 truncate"
                    :class="sizeClasses[size].name"
                >
                    {{ stock.name }}
                </p>
            </div>
        </div>

        <!-- Price and change section -->
        <div class="flex justify-between items-end">
            <!-- Price with enhanced styling -->
            <div class="flex-1 min-w-0">
                <strong 
                    class="font-semibold text-gray-900 dark:text-white block"
                    :class="sizeClasses[size].price"
                >
                    {{ stock.currency }}{{ stock.price }}
                </strong>
            </div>
            
            <!-- Change indicator with enhanced styling -->
            <div class="flex items-center">
                <div class="flex items-center px-2 py-1 rounded-lg font-medium transition-all duration-300"
                     :class="[
                         sizeClasses[size].change,
                         {
                             'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700/30': stock.change > 0,
                             'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700/30': stock.change < 0,
                             'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700/30': stock.change === 0
                         }
                     ]"
                >
                    <svg 
                        class="mr-1.5" 
                        :class="[
                            sizeClasses[size].change === 'text-xs' ? 'w-3 h-3' : 'w-4 h-4',
                            { 'rotate-180': stock.change < 0 }
                        ]"
                        viewBox="0 0 24 24" 
                        fill="none" 
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path 
                            d="M12 5L20 13L18.6 14.4L13 8.8V19H11V8.8L5.4 14.4L4 13L12 5Z"
                            fill="currentColor" 
                        />
                    </svg>
                    {{ Math.abs(stock.change).toFixed(2) }}%
                </div>
            </div>
        </div>

        <!-- Subtle accent line -->
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-600 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-b-xl"></div>
    </article>
</template> 
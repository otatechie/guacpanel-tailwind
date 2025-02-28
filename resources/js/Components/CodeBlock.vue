<template>
    <div class="bg-gray-800 rounded-lg p-4 group relative">
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300" @click="copyToClipboard">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
        </button>
        <div class="text-yellow-400 text-sm">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
const copyToClipboard = () => {
    const content = document.querySelector('.text-yellow-400').textContent;
    navigator.clipboard.writeText(content);
};
</script>

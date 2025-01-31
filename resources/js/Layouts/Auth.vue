<template>
    <div class="bg-stone-100 min-h-screen pb-16">
        <Logo />
        <FlashMessage />
        <slot></slot>
    </div>

    <footer class="border-t border-gray-400">
        <div class="mx-auto max-w-7xl px-6 py-4 md:flex md:items-center md:justify-between lg:px-8">
            <nav class="flex justify-center space-x-6 md:order-2">
                <Link :href="route('home')" class="link">
                <span class="text-xs">Homepage</span>
                </Link>

                <a href="#" class="link">
                    <span class="text-xs">Privacy Policy</span>
                </a>

                <a href="#" class="link">
                    <span class="text-xs">Terms</span>
                </a>
            </nav>

            <div class="mt-8 md:order-1 md:mt-0">
                <p class="text-center text-xs text-gray-800">
                    &copy; 2024 All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import Logo from '../Shared/Logo.vue'
import FlashMessage from '../Shared/FlashMessage.vue'
</script>

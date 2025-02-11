<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import FlashMessage from '@/Shared/FlashMessage.vue'
import { computed } from 'vue'

const page = usePage()
const personalisation = page.props.personalisation || {}

const hasIdentity = computed(() => personalisation.appName || personalisation.appLogo)

</script>

<template>
    <div class="bg-gray-50 min-h-screen">
        <Logo :class="{ 'mb-12': !hasIdentity }" />
        <h1 v-if="personalisation.appName" class="text-3xl font-extrabold text-gray-900 text-center mb-2">
            {{ personalisation.appName }}
        </h1>
        <FlashMessage />
        <slot></slot>
    </div>

    <footer class="border-t border-gray-200">
        <div class="mx-auto max-w-7xl px-6 py-4 md:flex md:items-center md:justify-between lg:px-8">
            <nav class="flex justify-center space-x-6 md:order-2">
                <Link :href="route('home')">
                <span class="text-xs link">Homepage</span>
                </Link>

                <a href="#">
                    <span class="text-xs link">Privacy Policy</span>
                </a>

                <a href="#">
                    <span class="text-xs link">Terms</span>
                </a>
            </nav>

            <div class="mt-8 md:order-1 md:mt-0">
                <p class="text-center text-xs text-gray-600">
                    {{ personalisation.footerText }}
                    {{ personalisation.copyrightText || '© 2024 All rights reserved.' }}
                </p>
            </div>
        </div>
    </footer>
</template>

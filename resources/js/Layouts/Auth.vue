<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import FlashMessage from '@/Shared/FlashMessage.vue'
import { computed } from 'vue'

const page = usePage()

const personalisation = page.props.personalisation || {}

const hasCustomBranding = computed(() => personalisation.appName || personalisation.appLogo)

</script>

<template>
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow bg-gray-50 dark:bg-gray-800 pb-16">
            <header>
                <Logo :class="{ 'mb-8': !hasCustomBranding }" />
                <h1 v-if="personalisation.appName" class="text-3xl font-extrabold text-gray-900 text-center mb-2">
                    {{ personalisation.appName }}
                </h1>
            </header>
            <FlashMessage />
            <article>
                <slot></slot>
            </article>
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <section class="mx-auto max-w-7xl px-4 py-4 flex flex-col gap-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                <nav class="text-center md:order-2">
                    <ul class="flex justify-center gap-2 md:gap-4">
                        <li>
                            <Link :href="route('home')" class="text-xs text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Homepage</Link>
                        </li>
                        <li>
                            <a href="#" class="text-xs text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="#" class="text-xs text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Terms</a>
                        </li>
                    </ul>
                </nav>

                <p class="text-center text-xs text-gray-500 dark:text-gray-300 md:order-1">
                    {{ personalisation.footerText }}
                    {{ personalisation.copyrightText || '© 2024 All rights reserved.' }}
                </p>
            </section>
        </footer>
    </div>
</template>

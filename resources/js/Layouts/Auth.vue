<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import { computed } from 'vue'

const page = usePage()
const personalisation = page.props.personalisation || {}
const hasCustomBranding = computed(() => personalisation.appLogo)
</script>


<template>
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow bg-gray-50 dark:bg-gray-800 py-8">
            <header class="flex justify-center items-center">
                <Logo class="py-2" :class="{ 'mb-2': !hasCustomBranding }" />
            </header>
            <FlashMessage />
            <article>
                <slot></slot>
            </article>
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <section
                class="mx-auto max-w-7xl px-4 py-4 flex flex-col gap-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                <nav class="text-center md:order-2">
                    <ul class="flex justify-center gap-2 md:gap-4">
                        <li>
                            <Link :href="route('home')"
                                class="text-xs text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline">
                            Homepage
                            </Link>
                        </li>
                    </ul>
                </nav>

                <p class="text-center text-xs text-gray-500 dark:text-gray-300 md:order-1">
                    {{ personalisation.app_name }}
                    {{ personalisation.copyright_text || '© 2024 All rights reserved.' }}
                </p>
            </section>
        </footer>
    </div>
</template>

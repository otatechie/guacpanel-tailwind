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
        <main class="flex-grow bg-[var(--color-bg)] py-4">
            <header class="flex justify-center items-center">
                <div class="w-12 h-12 flex items-center justify-center">
                    <Logo
                        class="py-1 max-w-full max-h-full"
                        :class="{ 'mb-1': !hasCustomBranding }" />
                </div>
            </header>
            <FlashMessage />
            <article class="mt-2">
                <slot></slot>
            </article>
        </main>

        <footer class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
            <section
                class="mx-auto max-w-7xl px-4 py-4 flex flex-col gap-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                <nav class="text-center md:order-2">
                    <ul class="flex justify-center gap-2 md:gap-4">
                        <li>
                            <Link
                                :href="route('terms')"
                                class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)] underline">
                                Terms
                            </Link>
                        </li>
                    </ul>
                </nav>

                <p class="text-center text-xs text-[var(--color-text-muted)] md:order-1">
                    {{ personalisation.app_name }}
                    {{ personalisation.copyright_text || '© 2024 All rights reserved.' }}
                </p>
            </section>
        </footer>
    </div>
</template>

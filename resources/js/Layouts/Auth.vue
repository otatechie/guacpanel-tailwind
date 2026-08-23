<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Logo from '@js/Components/Common/Logo.vue'
import FlashMessage from '@js/Components/Notifications/FlashMessage.vue'

const page = usePage()
const personalisation = page.props.personalisation || {}

const appName = computed(
    () => personalisation.app_name ?? page.props.settings?.appName ?? 'GuacPanel'
)
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background">
        <FlashMessage />

        <main class="flex flex-1 flex-col items-center justify-center px-4 py-10">
            <Logo size="5rem" max-size="6rem" class="mb-6" />

            <div class="w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-sm sm:p-8">
                <slot />
            </div>
        </main>

        <footer class="px-6 py-4">
            <p class="text-center text-xs text-muted-foreground">
                © {{ new Date().getFullYear() }} {{ appName }} ·
                <Link :href="route('terms')" class="hover:text-foreground">Terms</Link>
            </p>
        </footer>
    </div>
</template>

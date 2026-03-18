<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'
import Logo from '@js/Components/Common/Logo.vue'
import AuthBackground from '@js/Components/Auth/AuthBackground.vue'
import FlashMessage from '@js/Components/Notifications/FlashMessage.vue'

const page = usePage()
const personalisation = page.props.personalisation || {}

const appName = computed(
    () => personalisation.app_name ?? page.props.settings?.appName ?? 'GuacPanel'
)

const features = [
    'Two-factor authentication & magic links',
    'Roles, permissions & user management',
    'Admin panel with audit logs & backups',
    'Real-time notifications via Laravel Reverb',
]

const stack = ['Laravel', 'Vue 3', 'Inertia', 'Tailwind']

const showBrandingPanel = computed(() => page.props.settings?.authBrandingPanel !== false)
</script>

<template>
    <div class="flex min-h-screen bg-(--color-bg)">

        <!-- Left branding panel -->
        <aside
            v-if="showBrandingPanel"
            class="relative hidden shrink-0 flex-col md:flex md:w-[420px] lg:w-[480px]"
            aria-hidden="true">

            <AuthBackground />

            <div class="relative z-10 flex flex-1 flex-col justify-between p-10 xl:p-12">

                <!-- Logo -->
                <img src="/images/logo-dark.png" alt="GuacPanel" class="max-h-12 w-auto object-contain" />

                <!-- Headline + features -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold leading-tight text-white lg:text-3xl">
                            A starter kit<br>that means business.
                        </h2>
                        <p class="mt-3 text-sm text-white/60">
                            Open-source Laravel admin panel with everything you need to ship fast.
                        </p>
                    </div>

                    <ul class="space-y-3" role="list">
                        <li
                            v-for="feature in features"
                            :key="feature"
                            class="flex items-center gap-2.5 text-sm text-white/80">
                            <CheckCircleIcon class="h-4 w-4 shrink-0 text-white/70" />
                            {{ feature }}
                        </li>
                    </ul>
                </div>

                <!-- Tech stack -->
                <div class="space-y-3">
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="tech in stack"
                            :key="tech"
                            class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs text-white/60">
                            {{ tech }}
                        </span>
                    </div>
                    <a
                        href="https://github.com/otatechie/guacpanel-tailwind"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 text-xs text-white/40 transition-colors hover:text-white/80">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                        </svg>
                        View on GitHub
                    </a>
                </div>
            </div>
        </aside>

        <!-- Right form panel -->
        <div class="flex flex-1 flex-col">

            <!-- Mobile header -->
            <FlashMessage />

            <main class="flex flex-1 flex-col items-center justify-center px-4 py-8">
                <Logo size="5rem" max-size="6rem" class="mb-8" :class="showBrandingPanel ? 'md:hidden' : ''" />
                <div class="w-full max-w-sm">
                    <slot />
                </div>
            </main>

            <footer class="px-6 py-3">
                <p class="text-center text-xs text-(--color-text-muted)">
                    © {{ new Date().getFullYear() }} {{ appName }} ·
                    <Link :href="route('terms')" class="hover:text-(--color-text)">Terms</Link>
                </p>
            </footer>
        </div>
    </div>
</template>

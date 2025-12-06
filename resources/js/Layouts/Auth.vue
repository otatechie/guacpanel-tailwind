<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Logo from '@js/Components/Common/Logo.vue'
import FlashMessage from '@js/Components/Notifications/FlashMessage.vue'
import { computed } from 'vue'

const page = usePage()
const personalisation = page.props.personalisation || {}
const hasCustomBranding = computed(() => personalisation.appLogo)

const copyrightText = computed(
  () => personalisation.copyright_text || `© ${new Date().getFullYear()} All rights reserved.`
)
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <main class="flex-grow bg-[var(--color-bg)] py-4">
      <header class="flex items-center justify-center">
        <div class="flex h-12 w-12 items-center justify-center">
          <Logo class="max-h-full max-w-full py-1" :class="{ 'mb-1': !hasCustomBranding }" />
        </div>
      </header>
      <FlashMessage />
      <article class="mt-2">
        <slot></slot>
      </article>
    </main>

    <footer class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
      <section
        class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 md:flex-row md:items-center md:justify-between">
        <nav class="text-center md:order-2">
          <ul class="flex justify-center gap-2 md:gap-4">
            <li>
              <Link
                :href="route('terms')"
                class="text-xs text-[var(--color-text-muted)] underline hover:text-[var(--color-text)]">
                Terms
              </Link>
            </li>
          </ul>
        </nav>

        <p class="text-center text-xs text-[var(--color-text-muted)] md:order-1">
          {{ personalisation.app_name }}
          {{ copyrightText }}
        </p>
      </section>
    </footer>
  </div>
</template>

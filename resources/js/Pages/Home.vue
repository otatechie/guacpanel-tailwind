<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
// import Default from '../Layouts/Default.vue'

import FlashMessage from '@js/Components/FlashMessage.vue'

// defineOptions({
//   layout: Default,
// })

const mobileMenuOpen = ref(false)

const code = `git clone https://github.com/otatechie/guacpanel-tailwind
cd guacpanel-tailwind && composer install
php artisan migrate --seed
npm install && npm run dev`

const copied = ref(false)
const stars = ref(0)
const showBackToTop = ref(false)

function copyCode() {
  navigator.clipboard.writeText(code)
  copied.value = true
  setTimeout(() => (copied.value = false), 1500)
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 500
}

onMounted(async () => {
  const response = await fetch('https://api.github.com/repos/otatechie/guacpanel-tailwind')
  const data = await response.json()
  stars.value = data.stargazers_count

  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <FlashMessage />
  <Head title="GuacPanel - Open Source Admin" />
  <div
    class="flex min-h-screen flex-col bg-gradient-to-br from-green-50 via-white to-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
    <header
      class="sticky top-0 z-50 w-full border-b border-gray-100 bg-white/80 py-1 backdrop-blur-sm sm:py-2 dark:border-gray-800 dark:bg-gray-900/80"
      role="banner">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2">
        <div class="flex items-center gap-3">
          <img src="/images/logo.png" class="block h-8 w-auto sm:h-10 dark:hidden" alt="Logo" />
          <img
            src="/images/logo-dark.png"
            class="hidden h-8 w-auto sm:h-10 dark:block"
            alt="Logo Dark" />
        </div>
        <button
          class="flex items-center border border-gray-500 px-2 py-1 text-gray-800 md:hidden dark:border-gray-500 dark:text-gray-200"
          @click="mobileMenuOpen = !mobileMenuOpen"
          aria-label="Toggle mobile menu">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              v-if="!mobileMenuOpen"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16m-7 6h7" />
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <nav
          class="hidden gap-8 text-sm font-medium text-gray-700 md:flex dark:text-gray-200"
          aria-label="Main navigation">
          <a
            href="#features"
            class="transition-colors hover:text-teal-600 dark:hover:text-teal-400">
            Features
          </a>
          <a
            href="/documentation"
            class="transition-colors hover:text-teal-600 dark:hover:text-teal-400">
            Documentation
          </a>
        </nav>
        <div class="flex items-center gap-3">
          <a
            href="/login"
            class="inline-flex items-center border border-green-400 bg-green-500/20 px-3 py-2 font-mono text-xs tracking-wider text-green-400 uppercase transition-all hover:bg-green-500/30 sm:px-4"
            aria-label="View demo">
            [Demo]
            <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7" />
            </svg>
          </a>
          <a
            href="https://github.com/otatechie/guacpanel-tailwind"
            target="_blank"
            class="flex items-center gap-1 border border-gray-400 px-2 py-2 font-mono text-xs text-gray-800 transition-all hover:border-gray-600 sm:gap-2 sm:px-3 dark:border-gray-600 dark:text-gray-300 dark:hover:border-gray-400"
            aria-label="View on GitHub">
            <svg
              class="h-4 w-4 text-gray-600 dark:text-gray-400"
              fill="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true">
              <path
                d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.237 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
            </svg>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ stars }}</span>
          </a>
        </div>
      </div>
      <!-- Mobile menu -->
      <div
        v-show="mobileMenuOpen"
        class="border-t border-gray-100 bg-white md:hidden dark:border-gray-800 dark:bg-gray-900">
        <div class="space-y-3 px-4 py-3">
          <a
            href="#features"
            class="block font-mono text-sm text-gray-700 transition-colors hover:text-teal-700 dark:text-gray-200 dark:hover:text-teal-300">
            [Features]
          </a>
          <a
            href="/documentation"
            class="block font-mono text-sm text-gray-600 transition-colors hover:text-teal-600 dark:text-gray-300 dark:hover:text-teal-400">
            [Documentation]
          </a>
        </div>
      </div>
    </header>

    <section
      class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-4 py-12 text-center sm:px-8 sm:py-16 md:px-12 md:py-20"
      role="region"
      aria-labelledby="hero-heading">
      <div
        class="absolute inset-0 -z-10 bg-gradient-to-br from-teal-50/50 via-white to-white dark:from-teal-900/20 dark:via-gray-900 dark:to-gray-900"></div>
      <div class="absolute inset-0 -z-10 bg-[url('/grid.svg')] opacity-5 dark:opacity-10"></div>
      <div
        class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-teal-100 opacity-20 blur-3xl dark:bg-teal-800"></div>
      <div
        class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-green-100 opacity-20 blur-3xl dark:bg-green-800"></div>

      <div class="relative">
        <h1
          id="hero-heading"
          class="mb-6 px-2 text-4xl font-bold text-gray-900 md:text-6xl dark:text-white">
          Ready to ship
          <span
            class="bg-gradient-to-r from-teal-800 to-green-600 bg-clip-text text-transparent dark:from-teal-300 dark:to-green-300">
            faster
          </span>
          ?
        </h1>

        <p class="mx-auto mb-4 max-w-2xl px-2 text-lg text-gray-700 md:text-xl dark:text-gray-200">
          Build amazing Laravel applications with ease. GuacPanel provides ready-to-use features
          like authentication, permissions, backup management, and auditing - all with beautiful UI
          components.
          <span aria-hidden="true" class="inline-block animate-bounce">🚀</span>
        </p>

        <p
          class="mb-8 space-x-3 font-mono text-[13px] tracking-tight text-gray-400 dark:text-gray-500">
          <span class="opacity-75">PHP 8.2+</span>
          <span class="opacity-50">/</span>
          <span class="opacity-75">Laravel 12</span>
          <span class="opacity-50">/</span>
          <span class="opacity-75">Vue 3</span>
          <span class="opacity-50">/</span>
          <span class="opacity-75">Inertia 2.0</span>
          <span class="opacity-50">/</span>
          <span class="opacity-75">Tailwind CSS 4+</span>
        </p>

        <div class="mb-16 flex flex-col justify-center gap-4 sm:flex-row">
          <a
            href="/login"
            class="inline-flex w-full items-center justify-center border border-green-500 bg-green-500/20 px-6 py-4 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 sm:w-auto sm:py-3 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
            [View_Demo]
          </a>
          <a
            href="/documentation"
            class="inline-flex w-full items-center justify-center border border-gray-500 bg-white px-6 py-4 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-700 sm:w-auto sm:py-3 dark:border-gray-500 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-400">
            [Documentation]
            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        <div
          class="mx-auto w-full max-w-3xl overflow-x-auto border border-green-500/30 bg-gray-900 p-0 text-left font-mono text-xs sm:text-sm dark:border-green-500/20 dark:bg-gray-950"
          role="region"
          aria-label="Installation commands">
          <div
            class="flex items-center justify-between border-b border-green-500/20 bg-gray-800 px-4 py-3 dark:border-green-800/20 dark:bg-gray-900">
            <div class="flex items-center gap-3">
              <span class="text-xs font-medium text-green-700 dark:text-green-200">
                Terminal — bash
              </span>
            </div>
            <button
              class="border border-green-500/50 bg-green-500/10 px-2 py-2 font-mono text-xs tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/20 sm:px-3 sm:py-1 dark:text-green-200"
              aria-label="Copy installation commands"
              @click="copyCode">
              <svg
                v-if="!copied"
                class="mr-1 inline h-3 w-3"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <svg
                v-else
                class="mr-1 inline h-3 w-3"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7" />
              </svg>
              <span v-if="!copied">[COPY]</span>
              <span v-else>[COPIED]</span>
            </button>
          </div>
          <div class="p-6">
            <div class="mb-2 flex items-center text-green-700 dark:text-green-200">
              <span class="text-green-700 dark:text-green-200">user@guacpanel</span>
              <span class="mx-1">:</span>
              <span class="text-green-700 dark:text-green-200">~</span>
              <span class="ml-1">$</span>
            </div>
            <pre
              class="ml-2 border-l border-green-300/30 pl-6 leading-relaxed whitespace-pre-wrap text-white dark:border-green-500/30 dark:text-white"
              >{{ code }}</pre
            >
            <div class="mt-4 flex items-center text-green-700 dark:text-green-200">
              <span class="text-green-700 dark:text-green-200">user@guacpanel</span>
              <span class="mx-1">:</span>
              <span class="text-green-700 dark:text-green-200">~</span>
              <span class="ml-1">$</span>
              <span class="ml-2 animate-pulse">▊</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative overflow-hidden py-24" role="region" aria-labelledby="why-heading">
      <div
        class="absolute inset-0 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800"></div>
      <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5"></div>

      <div class="relative mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-3xl">
          <div class="mb-8 text-center">
            <h2 id="why-heading" class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
              Why GuacPanel?
            </h2>
            <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
              A simple, clean dashboard with just the essentials
            </p>
          </div>

          <div
            class="border border-green-500/30 bg-white p-6 font-mono dark:border-green-500/20 dark:bg-gray-900">
            <div class="space-y-4">
              <div class="flex items-start">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Every Laravel project starts the same way: auth, user management, security, 2FA,
                  backups, activity logs—you've built it all before.
                </p>
              </div>
              <div class="flex items-start">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Most dashboards try to do everything. You just need one that gets out of the way
                  and lets you build.
                </p>
              </div>
              <div class="flex items-start">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  That's why I made GuacPanel: open-source, no bloat, no subscriptions. Just the
                  essentials developers actually need.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section
      id="features"
      class="bg-gradient-to-br from-slate-50 via-white to-teal-50/50 py-16 sm:py-20 md:py-24 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
      role="region"
      aria-labelledby="features-heading">
      <div class="mx-auto max-w-7xl px-4">
        <div class="mb-16 text-center">
          <h2
            id="features-heading"
            class="mb-4 bg-gradient-to-r from-teal-800 to-teal-600 bg-clip-text text-4xl font-bold text-transparent md:text-5xl dark:from-teal-300 dark:to-teal-200">
            Powerful Features
          </h2>
          <p class="mx-auto max-w-3xl text-xl text-gray-600 dark:text-gray-300">
            Everything you need to build modern admin panels and dashboards with Laravel
          </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">TanStack Table</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Powerful table features using TanStack Table for data management with sorting,
                filtering, and pagination.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">FilePond Upload</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Modern file uploads using FilePond with image preview support and drag-and-drop
                functionality.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Spatie Backup</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Integrated Laravel backup system with UI management interface for database and
                files.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">UI Components</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Beautiful and responsive components with dark mode support and accessibility
                features.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Spatie Permissions</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Role-based access control using Spatie's Laravel-permission with UI management
                interface.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Session Management</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Secure browser session handling with device tracking and remote logout capabilities.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Passwordless Login</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Secure magic link and one-time code authentication with device verification.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Apex Charts</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Beautiful, interactive chart components including line, bar, area, donut, and more
                for data visualization.
              </p>
            </div>
          </div>

          <div
            class="border border-green-500/30 bg-white p-3 font-mono sm:p-4 dark:border-green-500/20 dark:bg-gray-900">
            <div class="mb-2 flex items-start">
              <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
              <h4 class="font-bold text-green-800 dark:text-green-200">Typesense Search</h4>
            </div>
            <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
              <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                Lightning-fast, typo-tolerant search functionality with Typesense integration.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section
      class="bg-gradient-to-br from-teal-600 to-teal-700 py-24 dark:from-teal-900 dark:to-teal-800"
      role="region"
      aria-labelledby="cta-heading">
      <div class="mx-auto max-w-7xl px-4">
        <div class="relative">
          <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-10"></div>
          <div class="relative z-10 text-center">
            <h2 id="cta-heading" class="mb-6 text-3xl font-bold text-white md:text-4xl">
              Ready to Build Something Amazing?
            </h2>
            <p class="mx-auto mb-8 max-w-2xl text-xl text-teal-100 dark:text-teal-200">
              Start your next Laravel project with GuacPanel; secure, modern, and ready to use.
            </p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
              <a
                href="/login"
                class="inline-flex items-center border border-gray-400 bg-white px-6 py-3 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                aria-label="View demo application">
                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                [View_Demo]
              </a>
              <a
                href="/documentation"
                class="inline-flex items-center border border-green-400 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-400 uppercase transition-all hover:bg-green-500/30 dark:text-green-300"
                aria-label="Read documentation">
                [Documentation]
                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section
      class="bg-gradient-to-br from-slate-50 via-white to-white py-24 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
      role="region"
      aria-labelledby="faq-heading">
      <div class="mx-auto max-w-7xl px-4">
        <div class="mb-16 text-center">
          <h2 id="faq-heading" class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
            Frequently Asked Questions
          </h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Everything you need to know about GuacPanel
          </p>
        </div>

        <div class="mx-auto max-w-3xl">
          <div class="space-y-6">
            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">What is GuacPanel?</h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  An open-source Laravel 12 dashboard starter kit with essentials like 2FA, password
                  rules, backups, logs, and user management—built with Vue 3, Tailwind, and Inertia.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Why did you build GuacPanel?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  To avoid rebuilding the same security and user features in every project. Most
                  dashboards felt bloated or overpriced - I wanted something simple and extendable.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Is GuacPanel production-ready?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  Yes. It has built-in 2FA, password rules, backups, and logs for real-world apps.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Does GuacPanel include bloatware?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  No. GuacPanel only includes essential packages. You can remove any feature without
                  breaking the app.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Can I remove or swap packages?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  Yes. GuacPanel is modular - you can remove features like backups or permissions
                  without breaking the app.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Does GuacPanel replace Jetstream or Fortify?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  No. GuacPanel builds on Fortify and ships a ready Vue + Inertia frontend, while
                  Jetstream comes with its own stack.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">Is GuacPanel free?</h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  Yes, 100% open-source under the MIT license.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Where can I contribute, request features or report issues?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  On the
                  <a
                    href="https://github.com/otatechie/guacpanel-tailwind"
                    class="text-green-600 underline underline-offset-2 transition-colors hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
                    aria-label="Visit GitHub repo">
                    GitHub
                  </a>
                  repo, feel free to open issues, request features or submit pull requests!
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Is GuacPanel actively maintained?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  Yes. It's kept up to date with Laravel releases, security fixes, and community
                  contributions.
                </p>
              </div>
            </div>

            <div
              class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
              <div class="mb-2 flex items-start">
                <span class="mr-2 text-green-600 dark:text-green-500">⌾</span>
                <h4 class="font-bold text-green-800 dark:text-green-200">
                  Does GuacPanel include CRUD generators?
                </h4>
              </div>
              <div class="ml-2 border-l border-green-200 pl-6 dark:border-green-800">
                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                  No. GuacPanel handles app essentials; you can add CRUD generators separately.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer
      class="border-t border-gray-100 bg-white py-8 dark:border-gray-800 dark:bg-gray-900"
      role="contentinfo">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-center gap-4 sm:flex-row">
          <a
            href="#features"
            class="inline-flex w-full items-center justify-center border border-gray-500 bg-white px-6 py-4 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-700 sm:w-auto sm:py-3 dark:border-gray-500 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-400">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
            [Features]
          </a>

          <a
            href="/documentation"
            class="inline-flex w-full items-center justify-center border border-green-500 bg-green-500/20 px-6 py-4 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 sm:w-auto sm:py-3 dark:text-green-200">
            [Documentation]
            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        <div class="mt-8 flex justify-center text-sm text-gray-500 dark:text-gray-400">
          <span>
            Made with
            <span class="text-red-500 dark:text-red-400" role="img" aria-label="love">♡</span>
            in
            <a
              href="https://github.com/otatechie"
              class="text-green-600 underline underline-offset-2 transition-colors hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
              aria-label="Visit otatechie's GitHub profile">
              Accra
            </a>
          </span>
        </div>
      </div>
    </footer>

    <!-- Back to Top Button -->
    <button
      v-show="showBackToTop"
      class="fixed right-4 bottom-4 z-50 border border-green-500 bg-green-500/20 p-3 font-mono text-sm text-green-700 transition-all hover:bg-green-500/30 sm:right-8 sm:bottom-8 dark:text-green-200"
      aria-label="Back to top"
      @click="scrollToTop">
      [^]
    </button>
  </div>
</template>

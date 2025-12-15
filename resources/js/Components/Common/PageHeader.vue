<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <nav
    v-if="breadcrumbs.length"
    class="border-b border-[var(--color-border)] bg-[var(--color-surface-muted)] px-3 py-3 sm:px-6"
    aria-label="Breadcrumbs">
    <ol class="scrollbar-hide flex items-center space-x-1 overflow-x-auto text-sm sm:space-x-2">
      <template v-for="(crumb, index) in breadcrumbs" :key="index">
        <li class="flex-shrink-0">
          <Link
            v-if="crumb.href"
            :href="crumb.href"
            class="text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
            {{ crumb.label }}
          </Link>
          <span v-else class="text-[var(--color-text)]">{{ crumb.label }}</span>
        </li>
        <li
          v-if="index < breadcrumbs.length - 1"
          class="flex-shrink-0 text-[var(--color-text-muted)]"
          aria-hidden="true">
          /
        </li>
      </template>
    </ol>
  </nav>

  <header
    class="border-b border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-4 sm:px-6 sm:py-5">
    <div
      class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center sm:gap-0">
      <div class="min-w-0 flex-1">
        <h1 class="sub-heading text-lg sm:text-xl">
          <span v-dompurify-html="title"></span>
        </h1>
        <p v-if="description" class="mt-1 text-sm text-[var(--color-text-muted)]">
          <span v-dompurify-html="description"></span>
        </p>
      </div>
      <aside class="actions flex-shrink-0">
        <slot name="actions"></slot>
      </aside>
    </div>
    <span class="bottom flex-shrink-0">
      <slot name="bottom"></slot>
    </span>
  </header>
</template>

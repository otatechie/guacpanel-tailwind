<script setup>
import { computed } from 'vue'

const props = defineProps({
  row: {
    type: Object,
    required: true,
  },
})

const typeLabel = type => {
  if (!type) return 'Info'
  return String(type).charAt(0).toUpperCase() + String(type).slice(1)
}

const typeBadgeClass = type => {
  if (type === 'success') return 'badge badge-success'
  if (type === 'info') return 'badge badge-info'
  if (type === 'warning') return 'badge badge-warning'
  if (type === 'danger') return 'badge badge-danger'
  return 'badge badge-default'
}

const scopeIconName = scope => {
  if (scope === 'user') return 'user'
  if (scope === 'system') return 'cpu'
  if (scope === 'release') return 'release'
  return 'tag'
}

const createdDisplay = computed(() => props.row?.created_at_diff ?? props.row?.created_at ?? '-')
const titleDisplay = computed(() => props.row?.title || 'Notification')
const messageDisplay = computed(() => props.row?.message || '')
const scopeDisplay = computed(() => props.row?.scope || 'notification')

const isUnread = computed(() => Boolean(!props.row?.is_read && !props.row?.is_dismissed))
const isRead = computed(() => Boolean(props.row?.is_read))
const isDismissed = computed(() => Boolean(props.row?.is_dismissed))
</script>

<template>
  <div class="min-w-0">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="flex items-center justify-start gap-1 font-medium text-[var(--color-text)]">
          <div
            v-if="isUnread"
            class="mt-0 -mr-0.5 -ml-1 flex h-6 w-6 items-center justify-center rounded not-hover:animate-pulse">
            <div class="h-2.5 w-2.5 rounded-full bg-blue-500" aria-hidden="true"></div>
          </div>

          <span class="truncate">
            {{ titleDisplay }}
          </span>
        </div>

        <div class="mt-1 text-sm break-words text-[var(--color-text-muted)]">
          {{ messageDisplay }}
        </div>
      </div>

      <div class="shrink-0">
        <span :class="typeBadgeClass(props.row?.type)">
          {{ typeLabel(props.row?.type) }}
        </span>
      </div>
    </div>

    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-[var(--color-text-muted)]">
      <span class="pill">
        <svg
          v-if="scopeIconName(props.row?.scope) === 'user'"
          class="size-3.5"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
          <path d="M4.5 20.25a7.5 7.5 0 0115 0" />
        </svg>

        <svg
          v-else-if="scopeIconName(props.row?.scope) === 'cpu'"
          class="size-3.5"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
        </svg>

        <svg
          v-else-if="scopeIconName(props.row?.scope) === 'release'"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 288 288"
          fill="none"
          aria-hidden="true"
          class="size-3.5 text-[var(--color-text-muted)]">
          <path
            d="M232.213 29.661a6.75 6.75 0 0 1 8.659 4.019 293.104 293.104 0 0 1 4.671 13.82 293.554 293.554 0 0 1 12.249 63.562c6.142 6.107 9.958 14.579 9.958 23.938 0 9.359-3.816 17.831-9.958 23.938a293.551 293.551 0 0 1-12.249 63.562 293.143 293.143 0 0 1-4.671 13.82 6.75 6.75 0 0 1-12.678-4.64c.937-2.56 1.838-5.137 2.702-7.731a279.258 279.258 0 0 0-88.553-26.124 207.662 207.662 0 0 0 8.709 22.888c4.285 9.53 1.151 21.268-8.338 26.747l-7.875 4.547c-9.831 5.675-22.847 2.225-27.825-8.542a256.906 256.906 0 0 1-16.74-48.337C60.857 190.897 38.25 165.588 38.25 135c0-33.551 27.199-60.75 60.75-60.75h9c8.258 0 16.431-.356 24.505-1.052 35.031-3.023 68.22-12.466 98.391-27.147a278.666 278.666 0 0 0-2.702-7.73 6.75 6.75 0 0 1 4.019-8.66Z"
            fill="currentColor"
            stroke="currentColor"
            stroke-width="2"></path>
        </svg>

        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-3.5">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
        </svg>

        <span class="text-xxs font-medium uppercase">
          {{ scopeDisplay }}
        </span>
      </span>

      <span class="pill text-xxs uppercase">
        <span class="font-medium">Read:</span>
        <span>{{ isRead ? 'Yes' : 'No' }}</span>
      </span>

      <span class="pill text-xxs uppercase">
        <span class="font-medium">Dismissed:</span>
        <span>{{ isDismissed ? 'Yes' : 'No' }}</span>
      </span>

      <span class="flex w-full items-center gap-1.5 text-[var(--color-text-muted)]">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="mt-0.25 size-3">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        {{ createdDisplay }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    isLoading: Boolean,
    hasValidApiKey: Boolean,
    searchQuery: String,
    showResults: Boolean,
    federatedResults: Array,
    isFederatedSearching: Boolean,
    placeholder: String,
})

defineEmits(['search', 'focus', 'blur'])

const showResultsContainer = computed(
    () => props.showResults && props.searchQuery && props.hasValidApiKey
)

const hasResults = computed(() => props.federatedResults && props.federatedResults.length > 0)

const isMac = ref(false)

onMounted(() => {
    isMac.value = /Mac|iPod|iPhone|iPad/.test(navigator.userAgent)
})
</script>

<template>
    <div class="relative w-full">
        <div class="relative">
            <input
                type="search"
                :disabled="isLoading"
                :placeholder="isLoading ? 'Loading search...' : placeholder"
                :value="searchQuery"
                class="w-full rounded-lg border border-border bg-muted py-2 pr-16 pl-9 text-sm text-foreground transition-colors placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/15"
                :class="{ 'opacity-50 cursor-wait': isLoading }"
                @input="$emit('search', $event)"
                @focus="$emit('focus')"
                @blur="$emit('blur')" />

            <MagnifyingGlassIcon
                class="pointer-events-none absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                aria-hidden="true" />

            <kbd class="pointer-events-none absolute top-1/2 right-2.5 -translate-y-1/2 rounded border border-border bg-card px-1.5 py-0.5 font-mono text-[10px] text-muted-foreground">
                {{ isMac ? 'Cmd+K' : 'Ctrl+K' }}
            </kbd>
        </div>

        <!-- No API key warning -->
        <div v-if="!hasValidApiKey && searchQuery" class="absolute top-full z-50 mt-1.5 w-full">
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700 shadow-lg dark:border-amber-800/50 dark:bg-amber-950/30 dark:text-amber-300">
                Search is temporarily unavailable.
            </div>
        </div>

        <!-- Results dropdown -->
        <div v-else-if="showResultsContainer" class="absolute top-full z-50 mt-1.5 w-full overflow-hidden rounded-lg border border-border bg-card shadow-lg">
            <div class="border-b border-border px-3 py-2 text-xs text-muted-foreground">
                {{ hasResults ? `${federatedResults.length} results` : 'No results' }}
            </div>

            <!-- Loading -->
            <div v-if="isFederatedSearching" class="px-4 py-6 text-center text-sm text-muted-foreground">
                Searching...
            </div>

            <!-- Results list -->
            <template v-else>
                <div v-if="hasResults" class="max-h-80 overflow-y-auto">
                    <a
                        v-for="item in federatedResults"
                        :key="item.id"
                        :href="item.url"
                        class="block border-b border-border px-3 py-2.5 transition-colors last:border-0 hover:bg-muted">
                        <p class="text-sm font-medium text-foreground">{{ item.displayTitle }}</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">{{ item.displaySubtitle }}</p>
                    </a>
                </div>

                <div v-else class="px-4 py-6 text-center text-sm text-muted-foreground">
                    No results for "{{ searchQuery }}"
                </div>
            </template>
        </div>
    </div>
</template>

<style>
.typesense-search em {
    background-color: rgba(var(--primary-color-rgb), 0.15);
    font-style: normal;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 600;
}
</style>

<script setup>
import { computed, ref, onMounted } from 'vue'

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
    <div class="typesense-search relative w-full">
        <div class="search-input-wrapper relative">
            <input
                type="search"
                :disabled="isLoading"
                :placeholder="isLoading ? 'Loading search...' : placeholder"
                :value="searchQuery"
                class="search-input"
                :class="{ loading: isLoading }"
                @input="$emit('search', $event)"
                @focus="$emit('focus')"
                @blur="$emit('blur')" />
            <div class="search-icon" :class="{ loading: isLoading }">
                <svg
                    v-if="isLoading"
                    class="animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24">
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4" />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <kbd class="search-shortcut">{{ isMac ? 'Cmd+K' : 'Ctrl+K' }}</kbd>
        </div>

        <div v-if="!hasValidApiKey && searchQuery" class="search-results-container">
            <div
                class="flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-900/20">
                <svg
                    class="h-5 w-5 flex-shrink-0 text-amber-500 dark:text-amber-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-300">
                        Search Service Unavailable
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400">
                        Search functionality is temporarily disabled. Please try again later.
                    </p>
                </div>
            </div>
        </div>

        <div v-else-if="showResultsContainer" class="search-results-container">
            <div class="search-stats-header">
                {{ hasResults ? `${federatedResults.length} results` : 'No results' }}
            </div>

            <div v-if="isFederatedSearching" class="search-loading-state">
                <svg
                    class="animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24">
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4" />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <p>Searching...</p>
            </div>

            <template v-else>
                <div v-if="hasResults" class="search-results-list">
                    <div v-for="item in federatedResults" :key="item.id" class="search-result-item">
                        <a :href="item.url" class="search-result-link">
                            <div class="flex items-center justify-between">
                                <h3 class="search-result-title" v-html="item.displayTitle"></h3>
                            </div>
                            <p class="search-result-subtitle" v-html="item.displaySubtitle"></p>
                        </a>
                    </div>
                </div>

                <div v-else class="search-empty-state">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No results found for "{{ searchQuery }}"</p>
                </div>
            </template>
        </div>
    </div>
</template>

<style>
.typesense-search {
    font-family: inherit;
    width: 100%;
}

.search-input-wrapper {
    position: relative;
    width: 100%;
}

.search-input {
    width: 100%;
    padding: 0.625rem 4rem 0.625rem 2.5rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    background-color: #f9fafb;
    font-size: 0.875rem;
    color: #4a5568;
    outline: none;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
    border-color: var(--primary-color, #38b2ac);
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb, 56, 178, 172), 0.2);
}

.search-input.loading {
    color: #9ca3af;
    cursor: wait;
}

.dark .search-input {
    background-color: rgba(31, 41, 55, 0.5);
    border-color: #374151;
    color: #e5e7eb;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1rem;
    height: 1rem;
    color: #9ca3af;
    pointer-events: none;
}

.search-icon svg {
    width: 100%;
    height: 100%;
}

.search-shortcut {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    padding: 0.125rem 0.375rem;
    font-size: 0.625rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
    color: #6b7280;
    pointer-events: none;
}

.dark .search-shortcut {
    background: #374151;
    border-color: #4b5563;
    color: #9ca3af;
}

.search-results-container {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    margin-top: 0.5rem;
    background-color: white;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 50;
    max-height: 24rem;
    overflow-y: auto;
    animation: fadeIn 0.2s ease-out;
}

.dark .search-results-container {
    background-color: #1f2937;
    border-color: #374151;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
}

.search-stats-header {
    padding: 0.75rem 1rem;
    font-size: 0.75rem;
    color: #6b7280;
    border-bottom: 1px solid #f3f4f6;
}

.dark .search-stats-header {
    color: #9ca3af;
    border-color: #374151;
}

.search-result-item {
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-link {
    display: block;
    padding: 0.75rem 1rem;
    text-decoration: none;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

.dark .search-result-item:hover {
    background-color: #374151;
}

.search-result-title {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.search-result-subtitle {
    color: #6b7280;
    font-size: 0.75rem;
}

.search-empty-state,
.search-loading-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
}

.search-empty-state svg,
.search-loading-state svg {
    width: 2rem;
    height: 2rem;
    margin: 0 auto 0.5rem;
    color: var(--primary-color, #38b2ac);
}

.search-empty-state p,
.search-loading-state p {
    font-size: 0.875rem;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dark .search-result-title {
    color: #f3f4f6;
}

.dark .search-result-subtitle {
    color: #9ca3af;
}

.dark .search-empty-state svg {
    color: #4b5563;
}

.typesense-search em {
    background-color: rgba(var(--primary-color-rgb, 56, 178, 172), 0.2);
    font-style: normal;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: bold;
}
</style>

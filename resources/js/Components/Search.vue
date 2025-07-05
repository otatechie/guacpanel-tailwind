<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import {
    AisInstantSearch,
    AisConfigure
} from 'vue-instantsearch/vue3/es';
import TypesenseInstantSearchAdapter from 'typesense-instantsearch-adapter';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    isMobile: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'Search...'
    },
    host: {
        type: String,
        default: 'localhost'
    },
    port: {
        type: String,
        default: '8108'
    },
    protocol: {
        type: String,
        default: 'http'
    }
});

const emit = defineEmits(['close']);

const searchClient = ref(null);
const typesenseApiKey = ref(null);
const hasValidApiKey = ref(false);
const isLoading = ref(true);
const searchQuery = ref('');
const isSearching = ref(false);
const searchInput = ref(null);
const searchResults = ref(null);
const showResults = ref(false);
const isMobileSearchActive = ref(false);
const federatedResults = ref([]);
const isFederatedSearching = ref(false);

const closeOverlay = () => {
    showResults.value = false;
    isMobileSearchActive.value = false;
    searchQuery.value = '';
    emit('close');
};

const handleKeyDown = (event) => {
    if (event.key === 'Escape' && props.isOpen && props.isMobile) {
        closeOverlay();
    }
};

watch(() => props.isOpen, (isOpen) => {
    if (isOpen && props.isMobile) {
        document.addEventListener('keydown', handleKeyDown);
    } else {
        document.removeEventListener('keydown', handleKeyDown);
    }
});

const fetchTypesenseApiKey = async () => {
    const response = await axios.get('/typesense/scoped-key', {
        params: {
            collections: ['users', 'financial_metrics']
        }
    }).catch(() => ({ data: null }));

    if (response?.data?.apiKey) {
        typesenseApiKey.value = response.data.apiKey;
        hasValidApiKey.value = true;
        return response.data.apiKey;
    }

    return null;
};

onMounted(async () => {
    const apiKey = await fetchTypesenseApiKey();

    if (apiKey) {
        const usersAdapter = new TypesenseInstantSearchAdapter({
            server: {
                apiKey: apiKey,
                nodes: [
                    {
                        host: props.host,
                        port: props.port,
                        protocol: props.protocol
                    }
                ]
            },
            additionalSearchParameters: {
                query_by: 'name,email,category,type'
            }
        });

        searchClient.value = usersAdapter.searchClient;
    }

    isLoading.value = false;
});

const performFederatedSearch = async () => {
    if (!searchQuery.value || !hasValidApiKey.value) {
        federatedResults.value = [];
        return;
    }

    isFederatedSearching.value = true;

    const apiKey = typesenseApiKey.value || await fetchTypesenseApiKey();

    if (!apiKey) {
        isFederatedSearching.value = false;
        return;
    }

    // Use Typesense's multi-search feature with a single HTTP request
    const searchResponse = await axios.post('/typesense/multi-search', {
        searches: [
            {
                collection: "users",
                q: searchQuery.value,
                query_by: "name",
                sort_by: "_text_match:desc",
                per_page: 5
            },
            {
                collection: "financial_metrics",
                q: searchQuery.value,
                query_by: "category,type",
                sort_by: "_text_match:desc",
                per_page: 5
            }
        ]
    }).catch(error => {
        return { data: { results: [{ hits: [] }, { hits: [] }] } };
    });

    // Process user results (first search in the multi-search)
    const userResults = searchResponse.data.results[0].hits.map(hit => {
        return {
            ...hit.document,
            collection_name: 'users',
            url: `/admin/user/${hit.document.id}/edit`,
            displayTitle: hit.highlights?.length > 0 && hit.highlights[0].field === 'name'
                ? hit.highlights[0].snippet
                : hit.document.name || 'User',
            displaySubtitle: hit.document.email || ''
        };
    });

    // Process financial metric results (second search in the multi-search)
    const financialResults = searchResponse.data.results[1].hits.map(hit => {
        const category = hit.highlights?.length > 0 && hit.highlights[0].field === 'category'
            ? hit.highlights[0].snippet
            : hit.document.category || 'Unknown';

        const type = hit.highlights?.length > 0 && hit.highlights[0].field === 'type'
            ? hit.highlights[0].snippet
            : hit.document.type || 'Unknown';

        const amount = hit.document.amount
            ? parseFloat(hit.document.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : '0.00';

        return {
            ...hit.document,
            collection_name: 'financial_metrics',
            url: `/admin/financial-metrics/${hit.document.id}`,
            displayTitle: `${category} - ${type}`,
            displaySubtitle: `Amount: $${amount}`
        };
    });

    // Combine results
    federatedResults.value = [...userResults, ...financialResults];
    isFederatedSearching.value = false;
};

watch(searchQuery, () => {
    isSearching.value = true;

    const timeoutId = setTimeout(() => {
        performFederatedSearch();
        isSearching.value = false;
    }, 300);

    return () => clearTimeout(timeoutId);
});

const handleSearch = (e) => {
    searchQuery.value = e.target.value;
};

const handleFocus = () => {
    showResults.value = true;
    if (props.isMobile) {
        isMobileSearchActive.value = true;
        if (searchInput.value) {
            searchInput.value.focus();
        }
    }
};

const handleBlur = (e) => {
    if (props.isMobile) {
        return;
    }

    if (searchResults.value && searchResults.value.contains(e.relatedTarget)) {
        return;
    }
    setTimeout(() => {
        showResults.value = false;
    }, 200);
};

watch(isMobileSearchActive, (newValue) => {
    if (newValue && props.isMobile) {
        showResults.value = true;
    }
});
</script>

<template>
    <!-- Mobile Search Overlay -->
    <div v-if="isOpen && isMobile" role="dialog" aria-modal="true" aria-label="Search site"
        class="fixed inset-0 z-50 bg-gray-900/50 dark:bg-gray-900/80">
        <div class="fixed inset-x-0 top-0 bg-white dark:bg-gray-800 p-4 shadow-lg" @touchstart.stop @click.stop>
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Search</h2>
                <button @click="closeOverlay" aria-label="Close search"
                    class="p-1 rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search Input -->
            <div class="typesense-search relative">
                <div v-if="isLoading" class="relative">
                    <div class="search-input-wrapper relative">
                        <input type="search" disabled placeholder="Loading search..." class="search-input loading" />
                        <div class="search-icon loading">
                            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div v-else-if="hasValidApiKey" class="relative">
                    <div class="search-input-wrapper relative">
                        <input ref="searchInput" type="search" :placeholder="placeholder" v-model="searchQuery"
                            @input="handleSearch" @focus="handleFocus" @blur="handleBlur" class="search-input" />
                        <div class="search-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Federated Search Results -->
                    <div v-if="showResults && searchQuery" ref="searchResults" class="search-results-container">
                        <div class="search-stats-header">
                            <span v-if="federatedResults.length > 0">
                                {{ federatedResults.length }} results
                            </span>
                            <span v-else>
                                No results
                            </span>
                        </div>

                        <!-- Loading State -->
                        <div v-if="isFederatedSearching" class="search-loading-state">
                            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            <p>Searching...</p>
                        </div>

                        <!-- Results List -->
                        <template v-else>
                            <div v-if="federatedResults.length > 0" class="search-results-list">
                                <div v-for="(item, index) in federatedResults" :key="index" class="search-result-item">
                                    <a :href="item.url" class="search-result-link">
                                        <div class="flex items-center justify-between">
                                            <h3 class="search-result-title" v-html="item.displayTitle"></h3>
                                        </div>
                                        <p class="search-result-subtitle" v-html="item.displaySubtitle"></p>
                                    </a>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-else class="search-empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p v-if="searchQuery">No results found for "{{ searchQuery }}"</p>
                                <p v-else>Type to search</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div v-else class="relative">
                    <div class="search-input-wrapper relative">
                        <input type="search" :placeholder="placeholder" class="search-input" />
                        <div class="search-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keep the InstantSearch component for backward compatibility but hide it -->
            <ais-instant-search v-if="false" :search-client="searchClient" index-name="users"
                :future="{ preserveSharedStateOnUnmount: true }">
                <ais-configure :query="searchQuery" />
            </ais-instant-search>
        </div>
    </div>

    <!-- Desktop Search -->
    <div v-else-if="!isMobile" class="typesense-search relative w-full">
        <div v-if="isLoading" class="relative">
            <div class="search-input-wrapper relative">
                <input type="search" disabled placeholder="Loading search..." class="search-input loading" />
                <div class="search-icon loading">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                </div>
            </div>
        </div>

        <div v-else-if="hasValidApiKey" class="relative">
            <div class="search-input-wrapper relative">
                <input ref="searchInput" type="search" :placeholder="placeholder" v-model="searchQuery"
                    @input="handleSearch" @focus="handleFocus" @blur="handleBlur" class="search-input" />
                <div class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Federated Search Results -->
            <div v-if="showResults && searchQuery" ref="searchResults" class="search-results-container">
                <div class="search-stats-header">
                    <span v-if="federatedResults.length > 0">
                        {{ federatedResults.length }} results
                    </span>
                    <span v-else>
                        No results
                    </span>
                </div>

                <!-- Loading State -->
                <div v-if="isFederatedSearching" class="search-loading-state">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    <p>Searching...</p>
                </div>

                <!-- Results List -->
                <template v-else>
                    <div v-if="federatedResults.length > 0" class="search-results-list">
                        <div v-for="(item, index) in federatedResults" :key="index" class="search-result-item">
                            <a :href="item.url" class="search-result-link">
                                <div class="flex items-center justify-between">
                                    <h3 class="search-result-title" v-html="item.displayTitle"></h3>
                                </div>
                                <p class="search-result-subtitle" v-html="item.displaySubtitle"></p>
                            </a>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="search-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p v-if="searchQuery">No results found for "{{ searchQuery }}"</p>
                        <p v-else>Type to search</p>
                    </div>
                </template>
            </div>
        </div>

        <div v-else class="relative">
            <div class="search-input-wrapper relative">
                <input type="search" :placeholder="placeholder" class="search-input" />
                <div class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Keep the InstantSearch component for backward compatibility but hide it -->
        <ais-instant-search v-if="false" :search-client="searchClient" index-name="users"
            :future="{ preserveSharedStateOnUnmount: true }">
            <ais-configure :query="searchQuery" />
        </ais-instant-search>
    </div>
</template>

<style>
/* Base styles */
.typesense-search {
    font-family: inherit;
    width: 100%;
}

/* Input styling */
.search-input-wrapper {
    position: relative;
    width: 100%;
}

.search-input {
    width: 100%;
    padding: 0.625rem 2.5rem 0.625rem 2.5rem;
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
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* Search icon */
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

.search-icon.loading {
    color: #9ca3af;
}

.search-icon svg {
    width: 100%;
    height: 100%;
}

/* Stats */
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

/* Results container */
.search-results-container {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    margin-top: 0.5rem;
    background-color: white;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 50;
    max-height: 24rem;
    overflow-y: auto;
    animation: fadeIn 0.2s ease-out;
}

.dark .search-results-container {
    background-color: #1f2937;
    border-color: #374151;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
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

/* Result items */
.search-result-item {
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.search-result-item:last-child {
    border-bottom: none;
}

.dark .search-result-item {
    border-color: #374151;
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

.dark .search-result-title {
    color: #f3f4f6;
}

.search-result-subtitle {
    color: #6b7280;
    font-size: 0.75rem;
}

.dark .search-result-subtitle {
    color: #9ca3af;
}

/* Empty state */
.search-empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
}

.dark .search-empty-state {
    color: #9ca3af;
}

.search-empty-state svg {
    width: 3rem;
    height: 3rem;
    margin: 0 auto 0.5rem;
    color: #9ca3af;
}

.dark .search-empty-state svg {
    color: #4b5563;
}

.search-empty-state p {
    font-size: 0.875rem;
}

/* Loading state */
.search-loading-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
}

.dark .search-loading-state {
    color: #9ca3af;
}

.search-loading-state svg {
    width: 2rem;
    height: 2rem;
    margin: 0 auto 0.5rem;
    color: var(--primary-color, #38b2ac);
}

.search-loading-state p {
    font-size: 0.875rem;
}

/* Highlight styling */
.typesense-search em {
    background-color: rgba(var(--primary-color-rgb, 56, 178, 172), 0.2);
    font-style: normal;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: bold;
}
</style>
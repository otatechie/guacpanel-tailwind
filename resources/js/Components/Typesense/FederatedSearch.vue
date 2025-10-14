<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    searchQuery: {
        type: String,
        required: true
    },
    typesenseApiKey: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['update:results', 'searching'])

const federatedResults = ref([])
const isFederatedSearching = ref(false)

const performFederatedSearch = async () => {
    if (!props.searchQuery || !props.typesenseApiKey) {
        federatedResults.value = []
        emit('update:results', [])
        return
    }

    isFederatedSearching.value = true
    emit('searching', true)

    try {
        const searchResponse = await axios.post('/typesense/multi-search', {
            searches: [
                {
                    collection: 'users',
                    q: props.searchQuery,
                    query_by: 'name',
                    sort_by: '_text_match:desc',
                    per_page: 5
                },
                {
                    collection: 'financial_metrics',
                    q: props.searchQuery,
                    query_by: 'category,type',
                    sort_by: '_text_match:desc',
                    per_page: 5
                }
            ]
        })

        const userResults = searchResponse.data.results[0].hits.map(hit => ({
            ...hit.document,
            collection_name: 'users',
            url: `/admin/users`,
            displayTitle:
                hit.highlights?.length > 0 && hit.highlights[0].field === 'name'
                    ? hit.highlights[0].snippet
                    : hit.document.name || 'User',
            displaySubtitle: hit.document.email || ''
        }))

        const financialResults = searchResponse.data.results[1].hits.map(hit => {
            const category =
                hit.highlights?.length > 0 && hit.highlights[0].field === 'category'
                    ? hit.highlights[0].snippet
                    : hit.document.category || 'Unknown'

            const type =
                hit.highlights?.length > 0 && hit.highlights[0].field === 'type'
                    ? hit.highlights[0].snippet
                    : hit.document.type || 'Unknown'

            const amount = hit.document.amount
                ? parseFloat(hit.document.amount).toLocaleString('en-US', {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2
                  })
                : '0.00'

            return {
                ...hit.document,
                collection_name: 'financial_metrics',
                url: `/admin/financial-metrics/${hit.document.id}`,
                displayTitle: `${category} - ${type}`,
                displaySubtitle: `Amount: $${amount}`
            }
        })

        federatedResults.value = [...userResults, ...financialResults]
        emit('update:results', federatedResults.value)
    } catch {
        federatedResults.value = []
        emit('update:results', [])
    } finally {
        isFederatedSearching.value = false
        emit('searching', false)
    }
}

watch(
    () => props.searchQuery,
    () => {
        const timeoutId = setTimeout(() => {
            performFederatedSearch()
        }, 300)

        return () => clearTimeout(timeoutId)
    }
)
</script>

<template>
    <!-- This component handles federated search logic but doesn't render anything -->
</template>

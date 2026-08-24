import { ref, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'

export function useServerPagination(options) {
    const columnFilters = ref([])
    const sorting = ref([])
    const globalFilter = ref('')

    /* `options.filters` was read once at setup. Inertia hands the page a new
       filters object on every visit while this composable lives on, so each
       request was built from the filters as they were on first render — which is
       why sorting dropped the search and searching dropped the sort. Resolve it
       per request instead; a getter keeps it current. */
    const currentFilters = () =>
        (typeof options.filters === 'function' ? options.filters() : options.filters) ?? {}

    const perPage = () => currentFilters().per_page || options.pagination.per_page

    // Store watchers and timers for cleanup
    let globalSearchTimer = null
    const watchers = []

    const watchColumnFilters = () => {
        const stopWatch = watch(
            columnFilters,
            newFilters => {
                const filterParams = {}
                newFilters.forEach(filter => {
                    if (
                        filter.value !== undefined &&
                        filter.value !== null &&
                        filter.value !== ''
                    ) {
                        filterParams[filter.id] = filter.value
                    }
                })

                const queryParams = {
                    ...currentFilters(),
                    ...filterParams,
                    page: 1,
                    per_page: perPage(),
                }

                router.get(options.routeUrl, queryParams, {
                    preserveState: true,
                    preserveScroll: true,
                })
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const watchSorting = () => {
        const stopWatch = watch(
            sorting,
            newSorting => {
                if (newSorting.length === 0) return

                const sort = newSorting[0]
                const queryParams = {
                    ...currentFilters(),
                    sort_by: sort.id,
                    sort_dir: sort.desc ? 'desc' : 'asc',
                    page: 1,
                    per_page: perPage(),
                }

                router.get(options.routeUrl, queryParams, {
                    preserveState: true,
                    preserveScroll: true,
                })
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const watchGlobalSearch = () => {
        const stopWatch = watch(
            globalFilter,
            newSearch => {
                if (newSearch === undefined || newSearch === null) return

                // Clear existing timer
                if (globalSearchTimer) {
                    clearTimeout(globalSearchTimer)
                }

                globalSearchTimer = setTimeout(() => {
                    const queryParams = {
                        ...currentFilters(),
                        search: newSearch,
                        page: 1,
                        per_page: perPage(),
                    }

                    router.get(options.routeUrl, queryParams, {
                        preserveState: true,
                        preserveScroll: true,
                    })
                }, 300)
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const cleanup = () => {
        // Stop all watchers
        watchers.forEach(stopWatch => stopWatch())
        watchers.length = 0

        // Clear any pending timer
        if (globalSearchTimer) {
            clearTimeout(globalSearchTimer)
            globalSearchTimer = null
        }
    }

    const init = () => {
        /* Seed from the server's filters first, so the toolbar shows the query
           and sort that produced the rows on screen. Assigning before the
           watchers exist keeps it from firing a request back at the server. */
        const filters = currentFilters()

        if (filters.search) {
            globalFilter.value = filters.search
        }

        if (filters.sort_by) {
            sorting.value = [{ id: filters.sort_by, desc: filters.sort_dir === 'desc' }]
        }

        watchColumnFilters()
        watchSorting()
        watchGlobalSearch()
    }

    // Cleanup on component unmount
    onBeforeUnmount(() => {
        cleanup()
    })

    return {
        columnFilters,
        sorting,
        globalFilter,
        init,
        cleanup, // Export cleanup in case manual cleanup is needed
    }
}

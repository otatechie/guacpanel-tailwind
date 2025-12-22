# DataTable Services Usage Guide

This guide shows how to use the `DataTablePaginationService` and `DataTableFilterService` together to create powerful, filterable, sortable, and paginated datatables.

## Services Overview

### DataTablePaginationService
Handles pagination logic including:
- Page number resolution
- Per-page item count (with "All" option support)
- Filter parameter building

### DataTableFilterService
Handles filtering and sorting logic including:
- Search across multiple columns
- Relationship-based filtering
- Custom composite filters
- Flexible sorting (including relationship sorting)

## Basic Usage Example

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTablePaginationService;
use App\Services\DataTableFilterService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function __construct(
        private DataTablePaginationService $pagination,
        private DataTableFilterService $filter
    ) {
        $this->middleware('permission:view-users');
    }

    public function index(Request $request)
    {
        // Start with base query
        $query = User::query()->with(['roles:id,name', 'permissions:id,name']);

        // Apply search across multiple columns
        $searchableColumns = ['name', 'email', 'roles.name'];
        $query = $this->filter->applySearch($query, $request, $searchableColumns);

        // Apply custom filters
        $filterConfig = [
            'status' => ['type' => 'simple'], // Simple column filter
            'role' => [
                'type' => 'relationship',
                'relation' => 'roles',
                'field' => 'name'
            ],
            'created_range' => [
                'type' => 'composite',
                'callback' => function ($query, $value) {
                    // Custom date range filter
                    if (isset($value['start'])) {
                        $query->where('created_at', '>=', $value['start']);
                    }
                    if (isset($value['end'])) {
                        $query->where('created_at', '<=', $value['end']);
                    }
                }
            ]
        ];
        $query = $this->filter->applyFilters($query, $request, $filterConfig);

        // Apply sorting
        $sortConfig = [
            'name' => ['type' => 'simple'],
            'email' => ['type' => 'simple'],
            'created_at' => ['type' => 'simple'],
            'role_name' => [
                'type' => 'relationship',
                'table' => 'roles',
                'foreign_key' => 'users.id',
                'local_key' => 'model_id',
                'order_by' => 'roles.name'
            ]
        ];
        $query = $this->filter->applySorting($query, $request, $sortConfig);

        // Get total count for "All" option
        $filteredTotal = $query->count();

        // Apply pagination
        $perPage = $this->pagination->resolvePerPageWithDefaults(
            $request, 
            'users', 
            $filteredTotal
        );

        // Execute query with pagination
        $users = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles,
                    'permissions' => $user->permissions,
                    'created_at_formatted' => $user->created_at_formatted,
                ];
            });

        return Inertia::render('Admin/User/IndexUserPage', [
            'users' => $users,
            'filters' => $this->pagination->buildFilters($request),
        ]);
    }
}
```

## Frontend Integration

### Example Request Parameters

```javascript
// Search
?search=john

// Pagination
?page=2&per_page=25

// Sorting
?sort_by=name&sort_dir=asc

// Filters
?status=active&role=admin

// Combined
?search=john&page=1&per_page=50&sort_by=created_at&sort_dir=desc&status=active
```

### Vue Component Example

```vue
<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  users: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const sortBy = ref(props.filters.sort_by || '')
const sortDir = ref(props.filters.sort_dir || 'desc')

const applyFilters = () => {
  router.get(route('admin.users.index'), {
    search: search.value,
    page: 1, // Reset to first page on new search
    per_page: props.filters.per_page,
    sort_by: sortBy.value,
    sort_dir: sortDir.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleSort = (column) => {
  if (sortBy.value === column) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortDir.value = 'asc'
  }
  applyFilters()
}
</script>
```

## Advanced Filter Configurations

### Relationship Filter
```php
'department' => [
    'type' => 'relationship',
    'relation' => 'department',
    'field' => 'name'
]
```

### Composite Filter (Custom Logic)
```php
'price_range' => [
    'type' => 'composite',
    'callback' => function ($query, $value) {
        if (isset($value['min'])) {
            $query->where('price', '>=', $value['min']);
        }
        if (isset($value['max'])) {
            $query->where('price', '<=', $value['max']);
        }
    }
]
```

### Relationship Sorting
```php
'user_name' => [
    'type' => 'relationship',
    'table' => 'users',
    'foreign_key' => 'orders.user_id',
    'local_key' => 'users.id',
    'order_by' => 'users.name'
]
```

## Best Practices

1. **Always provide searchable columns** - Define which columns can be searched
2. **Use filtered total for "All" option** - Pass the filtered count to pagination service
3. **Validate sort fields** - Only allow sorting on configured fields
4. **Preserve query string** - Use `withQueryString()` on pagination
5. **Reset page on filter change** - Reset to page 1 when filters change
6. **Use resource transformations** - Transform data in `through()` callback

## Performance Tips

1. **Index searchable columns** - Add database indexes for frequently searched columns
2. **Eager load relationships** - Use `with()` to avoid N+1 queries
3. **Limit "All" option** - The pagination service caps "All" at 1000 rows
4. **Use select()** - Only select needed columns to reduce memory usage
5. **Cache complex queries** - Consider caching for expensive filter combinations

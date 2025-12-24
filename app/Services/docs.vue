<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'

defineOptions({
    layout: Public,
})

const codeExamples = {
    routeConfig: `// Magic Link Authentication Routes
Route::middleware(['guest', 'web'])->group(function () {
    Route::controller(MagicLinkController::class)->group(function () {
        Route::get('/magic-link/register', 'create')->name('magic.register.create');
        Route::post('/register/magic-link', 'register')->name('magic.register');
        Route::post('/login/magic-link', 'login')->name('magic.login.request');
        Route::get('/magic-link/{token}', 'authenticate')->name('magic.login');
    });
});`,

    magicLink: `protected function sendLoginLink(User $user)
{
    $url = URL::temporarySignedRoute(
        'magic.login',
        now()->addMinutes(10),
        ['token' => $user->id]
    );

    Mail::to($user)->send(new MagicLoginLink($url));
}`,

    authProcess: `public function authenticate(Request $request)
{
    if (!$request->hasValidSignature()) {
        return redirect()->route('login')
            ->with('error', 'Magic link expired.');
    }

    $user = User::findOrFail($request->token);
    auth()->guard('web')->login($user);
    $request->session()->regenerate();

    return redirect()->intended(config('fortify.home'));
}`,

    accountDisabling: `public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && auth()->user()->is_disabled) {
        auth()->logout();
        session()->flash('error', 'Your account has been disabled.');
        return redirect()->route('login');
    }

    return $next($request);
}`,

    passwordExpiry: `public function handle(Request $request, Closure $next): Response
{
    if (auth()->check()) {
        $user = auth()->user();
        $settings = Setting::first();

        if ($settings && $settings->password_expiry && $user->isPasswordExpired()) {
            session()->flash('warning', 'Your password has expired. Please change it to continue.');
            return redirect()->route('user.password.expired');
        }
    }

    return $next($request);
}`,

    forcePassword: `public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && auth()->user()->force_password_change) {
        session()->flash('warning', 'You must change your password before continuing.');
        return redirect()->route('user.force.password.change');
    }

    return $next($request);
}`,

    twoFactor: `public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        return $next($request);
    }

    $settings = Setting::first();
    if (!$settings || !$settings->two_factor_authentication) {
        return $next($request);
    }

    $user = auth()->user();
    if (!$user->two_factor_secret) {
        session()->flash('warning', 'Two-factor authentication is required. Please enable it to continue.');
        return redirect()->route('user.two.factor');
    }

    return $next($request);
}`,

    socialConfig: `GOOGLE_ENABLED=false
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT=\u0024{APP_URL}/auth/social/google/callback

FACEBOOK_ENABLED=false
FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT=\u0024{APP_URL}/auth/social/facebook/callback

GITHUB_ENABLED=false
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
GITHUB_REDIRECT=\u0024{APP_URL}/auth/social/github/callback

LINKEDIN_ENABLED=false
LINKEDIN_CLIENT_ID=your-linkedin-client-id
LINKEDIN_SECRET=your-linkedin-client-secret
LINKEDIN_REDIRECT=\u0024{APP_URL}/auth/social/linkedin/callback`,

    frontendSetup: `// resources/js/Pages/Admin/User/IndexUserPage.vue
import { Head, router } from '@inertiajs/vue3'
import DataTable from '@/Components/Datatable.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref } from 'vue'

const props = defineProps({
    users: { type: Object, required: true }
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.users.current_page,
    per_page: Number(props.users.per_page),
    total: props.users.total
})

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.accessor('role', {
        header: 'Role',
        cell: info => {
            const roleName = info.row.original.roles?.[0]?.name || 'No Role'
            return h('span', { class: 'px-2 py-1 rounded text-xs bg-blue-50' }, roleName)
        }
    })
]`,

    templateUsage: `<DataTable
    :data="users.data"
    :columns="columns"
    :loading="loading"
    :pagination="pagination"
    :search-fields="['name', 'email', 'created_at_formatted']"
    empty-message="No users found"
    empty-description="Users will appear here once created"
    export-file-name="users"
    @update:pagination="pagination = $event" />`,

    paginationWatcher: `// Watch for pagination changes and reload data
import { watch } from 'vue'
import { router } from '@inertiajs/vue3'

watch(
    pagination,
    newPagination => {
        loading.value = true
        router.get(
            route('admin.user.index'),
            {
                page: newPagination.current_page,
                per_page: Number(newPagination.per_page)
            },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loading.value = false)
            }
        )
    },
    { deep: true }
)`,

    backendService: `// app/Services/DataTableService.php
// Use the process() method for a complete datatable pipeline
public function process(Builder $query, Request $request, array $config): array
{
    if (!empty($config['searchable'])) {
        $query = $this->applySearch($query, $request, $config['searchable']);
    }

    if (!empty($config['sortable'])) {
        $query = $this->applySorting($query, $request, $config['sortable']);
    }

    $filteredTotal = $query->count();
    $resourceName = $config['resource'] ?? 'items';
    $perPage = $this->resolvePerPageWithDefaults($request, $resourceName, $filteredTotal);

    $paginator = $query->paginate($perPage)->withQueryString();

    if (isset($config['transform']) && is_callable($config['transform'])) {
        $paginator->through($config['transform']);
    }

    return [
        'data' => $paginator,
        'filters' => $this->buildFilters($request),
    ];
}`,

    backendController: `// app/Http/Controllers/AdminUserController.php
public function __construct(private DataTableService $dataTable) {}

public function index(Request $request)
{
    $result = $this->dataTable->process(
        query: User::query()->with(['roles', 'permissions'])->latest(),
        request: $request,
        config: [
            'searchable' => ['name', 'email', 'roles.name'],
            'sortable' => [
                'name' => ['type' => 'simple'],
                'email' => ['type' => 'simple'],
                'created_at' => ['type' => 'simple'],
            ],
            'resource' => 'users',
            'transform' => fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles,
                'permissions' => $user->permissions,
            ],
        ]
    );

    return Inertia::render('Admin/User/IndexUserPage', [
        'users' => $result['data'],
        'filters' => $result['filters'],
    ]);
}`,

    actionButtons: `// Add to your columns array
columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: info => (
        <div class="flex items-center gap-2">
            <button
                onClick={() => handleEdit(info.row.original)}
                class="btn-primary-outline btn-sm"
            >
                Edit
            </button>
            <button
                onClick={() => handleDelete(info.row.original)}
                class="btn-danger-outline btn-sm"
            >
                Delete
            </button>
        </div>
    )
})

const handleEdit = (user) => {
    router.visit(route('users.edit', { user: user.id }))
}

const handleDelete = async (user) => {
    if (!confirm('Are you sure you want to delete this user?')) return

    router.delete(route('users.destroy', { user: user.id }), {
        preserveScroll: true,
        onSuccess: () => flash('User deleted successfully')
    })
}`,

    bulkDelete: `// Add bulk delete functionality to your Datatable
<Datatable
    :bulk-delete-route="route('admin.login.history.bulk-destroy')"
    @bulk-delete="handleBulkDelete">
</Datatable>

const handleBulkDelete = async ({ selectedRows }) => {
    if (!selectedRows?.length) return;

    const ids = selectedRows.map(row => row.id);
    await axios.post(route('admin.login.history.bulk-destroy'), { ids });
    await router.reload({ preserveScroll: true });
};`,

    configExample: `{
    // Required props
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    server: {
        type: Object,
        required: true
    },

    // Optional props
    labelIdle: {
        type: String,
        default: 'Drop files here...'
    },
    acceptedFileTypes: {
        type: Array,
        default: () => ['image/jpeg', 'image/png', 'application/pdf', 'image/x-icon']
    },
    maxFileSize: {
        type: String,
        default: '5MB'
    },
    allowMultiple: {
        type: Boolean,
        default: false
    },
    maxFiles: {
        type: Number,
        default: 1
    },
    required: {
        type: Boolean,
        default: false
    },
    files: {
        type: Array,
        default: () => []
    }
}`,

    scriptExample: `import { defineComponent } from 'vue'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview'
import 'filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css'

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginPdfPreview
)

const serverConfig = {
    url: '/upload',
    process: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }
}

const handleFileUpload = (error, file) => {
    if (!error) {
        // File uploaded successfully
    }
}

const handleFileRemove = (error, file) => {
    if (!error) {
        // File removed successfully
    }
}`,

    // Health monitoring
    healthChecksConfig: `// app/Providers/AppServiceProvider.php
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;

public function register(): void {
    Health::checks([
        UsedDiskSpaceCheck::new(),
        DatabaseCheck::new(),
        EnvironmentCheck::new(),
        DebugModeCheck::new(),
        CacheCheck::new(),
        QueueCheck::new(),
        OptimizedAppCheck::new(),
    ]);
}`,

    healthController: `// app/Http/Controllers/AdminHealthStatusController.php
use Spatie\Health\ResultStores\ResultStore;

public function index(ResultStore $resultStore)
{
    $checkResults = $resultStore->latestResults();

    return Inertia::render('Monitoring/IndexPage', [
        'healthChecks' => [
            'lastRanAt' => $checkResults?->finishedAt,
            'results' => $checkResults?->storedCheckResults?->map(function ($result) {
                return [
                    'label' => $result->label,
                    'status' => $result->status,
                    'notificationMessage' => $result->notificationMessage,
                    'meta' => $result->meta,
                ];
            }) ?? [],
        ],
    ]);
}

public function runHealthChecks()
{
    Artisan::call(RunHealthChecksCommand::class);
    return redirect()->back()->with('success', 'Health checks completed');
}`,

    healthFrontend: `// Monitoring/IndexPage.vue - Status grouping and rendering
const groupedResults = computed(() => {
    return results.value.reduce((acc, result) => {
        const status = (result.status || 'default').toLowerCase()
        if (!acc[status]) acc[status] = []
        acc[status].push(result)
        return acc
    }, {})
})

const runHealthChecks = () => {
    router.post(route('admin.health.refresh'), {}, {
        preserveScroll: true,
        onFinish: () => { /* Update UI */ }
    })
}`,

    generateSearchKey: `php generate-search-key.php`,

    typesenseEnv: `# .env
TYPESENSE_SEARCH_ONLY_KEY=your_generated_search_key_here
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http`,

    // Search configuration
    searchConfig: `{
    collection: "users",          // Collection name
    q: searchQuery,              // Search query
    query_by: "name,email",      // Fields to search
    sort_by: "_text_match:desc", // Sort order
    per_page: 5                  // Results per page
}`,

    // Add new example for result structure
    resultStructure: `{
    collection_name: 'collection_name',  // Collection identifier
    url: '/path/to/item',               // URL for the result
    displayTitle: 'Title',              // Main display text
    displaySubtitle: 'Subtitle'         // Secondary display text
}`,

    // Laravel Reverb - Real-time Notifications
    reverbEnv: `# Enable notifications in .env
APP_NOTIFICATIONS_ENABLED=true
APP_NOTIFICATIONS_IN_DEMO_MODE=true # set this to false to enable live reverb notifications.`,

    installBroadcasting: `php artisan install:broadcasting`,

    reverbCredentials: `# This will populate the following in your .env (you can override these as needed):
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_TLS_CERT=
REVERB_TLS_KEY=

BROADCAST_CONNECTION=reverb`,
}

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Permissions & Roles', href: '#permissions-roles' },
    { text: 'Security Middleware', href: '#middleware' },
    { text: 'Backup Management UI', href: '#backup-system' },
    { text: 'System Health Monitoring', href: '#system-health' },
    { text: 'Data Tables', href: '#data-tables' },
    { text: 'Typesense Search', href: '#typesense-search' },
    { text: 'File Uploads', href: '#file-uploads' },
    { text: 'Real-time Notifications', href: '#realtime-notifications' },

]

const showBackToTop = ref(false)

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>

    <Head title="Features - GuacPanel" />

    <div class="mx-auto max-w-4xl">
        <!-- Intro Section -->
        <div id="introduction" class="mb-4 sm:mb-8">
            <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-teal-500 dark:text-teal-300">
                Core Features
            </p>
            <h1 class="mb-4 text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                System Features
            </h1>
            <p class="text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                Build secure Laravel admin interfaces with GuacPanel's essential features:
                authentication, permissions, health monitoring, backups, data tables, and search.
                Everything you need, ready to use.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank"
                    class="inline-flex items-center justify-center gap-2 border border-zinc-400 dark:border-zinc-600 bg-[#3a3a3a] px-6 py-3 font-mono text-sm font-bold uppercase tracking-wider text-white transition-all hover:bg-white hover:text-[#3a3a3a] dark:bg-white dark:text-[#3a3a3a] dark:hover:bg-[#3a3a3a] dark:hover:text-white">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                            clip-rule="evenodd" />
                    </svg>
                    Source Code
                </a>
                <a href="/documentation/components"
                    class="inline-flex items-center justify-center gap-2 border border-zinc-400 dark:border-zinc-600 bg-white px-6 py-3 font-mono text-sm font-bold uppercase tracking-wider text-[#3a3a3a] transition-all hover:bg-[#3a3a3a] hover:text-white dark:bg-[#252525] dark:text-white dark:hover:bg-white dark:hover:text-[#3a3a3a]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    UI Components
                </a>
            </div>

            <!-- Feature Overview Grid -->
            <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div
                    class="rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-700 dark:border-zinc-600 dark:bg-[#1a1a1a] dark:text-zinc-200">
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500/10 text-zinc-700 dark:text-zinc-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <p class="font-semibold text-zinc-900 dark:text-white">Auth & Security</p>
                    </div>
                    <p>Fortify auth, magic links, social login, and security middleware.</p>
                </div>

                <div
                    class="rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-700 dark:border-zinc-600 dark:bg-[#1a1a1a] dark:text-zinc-200">
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500/10 text-zinc-700 dark:text-zinc-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </span>
                        <p class="font-semibold text-zinc-900 dark:text-white">Data & Search</p>
                    </div>
                    <p>Powerful TanStack data tables and lightning-fast typo-tolerant Typesense search.</p>
                </div>

                <div
                    class="rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-700 dark:border-zinc-600 dark:bg-[#1a1a1a] dark:text-zinc-200">
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500/10 text-zinc-700 dark:text-zinc-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <p class="font-semibold text-zinc-900 dark:text-white">Monitoring</p>
                    </div>
                    <p>Real-time system health dashboard and automated background Spatie backup system.</p>
                </div>

                <div
                    class="rounded-lg border border-zinc-300 bg-white p-3 text-sm text-zinc-700 dark:border-zinc-600 dark:bg-[#1a1a1a] dark:text-zinc-200">
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500/10 text-zinc-700 dark:text-zinc-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </span>
                        <p class="font-semibold text-zinc-900 dark:text-white">Files & UI</p>
                    </div>
                    <p>Modern drag-and-drop FilePond uploads and premium UI components.</p>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Authentication Section -->
        <section id="authentication" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Authentication</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel provides a complete authentication system built on Laravel Fortify. Choose from
                    traditional login, passwordless magic links, or social authentication with Google, Facebook, GitHub,
                    and LinkedIn. All authentication methods include built-in security middleware for account
                    management, password policies, and two-factor authentication.
                </p>
                <!-- Alert -->
                <div
                    class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200 dark:[--tw-prose-links-hover:theme(colors.teal.300)] dark:[--tw-prose-links:theme(colors.white)]">
                    <svg viewBox="0 0 32 32" aria-hidden="true"
                        class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                        <defs>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_auth"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_auth_dark"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                        </defs>
                        <g class="dark:hidden">
                            <circle cx="20" cy="20" r="12" fill="url(#_alert_auth)"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                class="fill-(--icon-background)" fill-opacity="0.5"></path>
                            <path
                                d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                class="fill-(--icon-foreground)"></path>
                            <path
                                d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                class="fill-(--icon-foreground)"></path>
                        </g>
                        <g class="hidden dark:inline">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                fill="url(#_alert_auth_dark)"></path>
                        </g>
                    </svg>
                    <div class="ml-4 flex-auto">
                        <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Seamless
                            Integration</p>
                        <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                            All authentication methods work seamlessly together. Users can switch between traditional
                            login, magic links, and social providers.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-12">
                <!-- Laravel Fortify -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Laravel
                        Fortify</h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        GuacPanel is built on
                        <a href="https://laravel.com/docs/11.x/fortify" target="_blank"
                            class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                            Laravel Fortify
                        </a>
                        and provides a complete, production-ready authentication system with login, registration, email
                        verification, two-factor authentication, password management, and session tracking—fully
                        configured and ready to use.
                    </p>
                </div>

                <!-- Passwordless Login -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Passwordless
                        Login</h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        Supports email-based magic link authentication, eliminating passwords while maintaining strong
                        security.
                    </p>

                    <!-- Alert -->
                    <div
                        class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                        <svg viewBox="0 0 32 32" aria-hidden="true"
                            class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                            <defs>
                                <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                    id="_alert_passwordless" gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                    <stop stop-color="#0EA5E9"></stop>
                                    <stop stop-color="#22D3EE" offset=".527"></stop>
                                    <stop stop-color="#818CF8" offset="1"></stop>
                                </radialGradient>
                                <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                    id="_alert_passwordless_dark"
                                    gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                    <stop stop-color="#0EA5E9"></stop>
                                    <stop stop-color="#22D3EE" offset=".527"></stop>
                                    <stop stop-color="#818CF8" offset="1"></stop>
                                </radialGradient>
                            </defs>
                            <g class="dark:hidden">
                                <circle cx="20" cy="20" r="12" fill="url(#_alert_passwordless)"></circle>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                    class="fill-(--icon-background)" fill-opacity="0.5"></path>
                                <path
                                    d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                    class="fill-(--icon-foreground)"></path>
                                <path
                                    d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                    class="fill-(--icon-foreground)"></path>
                            </g>
                            <g class="hidden dark:inline">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                    fill="url(#_alert_passwordless_dark)"></path>
                            </g>
                        </svg>
                        <div class="ml-4 flex-auto">
                            <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Configuration
                                Required</p>
                            <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                                Disabled by default. Enable in Security Settings to use this feature.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Login -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Social Login</h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        Let users sign in with their existing accounts for a faster, more convenient experience.
                        Authenticate users with Google, Facebook, GitHub, and LinkedIn via
                        <a href="https://laravel.com/docs/11.x/socialite" target="_blank"
                            class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                            Laravel Socialite
                        </a>.
                    </p>

                    <h4 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Environment
                        Setup</h4>
                    <div class="mb-4">
                        <CodeBlock :code="codeExamples.socialConfig" language="bash" :show-copy-button="true" />
                    </div>

                    <div
                        class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                        <svg aria-hidden="true" viewBox="0 0 32 32" fill="none"
                            class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                            <defs>
                                <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_provider"
                                    gradientTransform="matrix(0 21 -21 0 20 11)">
                                    <stop stop-color="#0EA5E9"></stop>
                                    <stop stop-color="#22D3EE" offset=".527"></stop>
                                    <stop stop-color="#818CF8" offset="1"></stop>
                                </radialGradient>
                                <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                    id="_alert_provider_dark" gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                    <stop stop-color="#0EA5E9"></stop>
                                    <stop stop-color="#22D3EE" offset=".527"></stop>
                                    <stop stop-color="#818CF8" offset="1"></stop>
                                </radialGradient>
                            </defs>
                            <g class="dark:hidden">
                                <circle cx="20" cy="20" r="12" fill="url(#_alert_provider)"></circle>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                    class="fill-(--icon-background)" fill-opacity="0.5"></path>
                                <path
                                    d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                    class="fill-(--icon-foreground)"></path>
                                <path
                                    d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                    class="fill-(--icon-foreground)"></path>
                            </g>
                            <g class="hidden dark:inline">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                    fill="url(#_alert_provider_dark)"></path>
                            </g>
                        </svg>
                        <div class="ml-4 flex-auto">
                            <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Provider
                                Visibility</p>
                            <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                                Enabled providers automatically appear on login and registration pages.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Permissions & Roles Section -->
        <section id="permissions-roles" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Permissions & Roles</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel provides powerful role-based access control built on
                    <a href="https://spatie.be/docs/laravel-permission" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Spatie Laravel-Permission
                    </a>, featuring a clean UI for defining roles, assigning granular permissions, and managing user
                    access with ease.
                </p>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Security Middleware Section -->
        <section id="middleware" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Security Middleware</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel includes comprehensive security middleware to protect your application. Automatically
                    enforce account status checks, password expiration policies, mandatory password changes, and
                    two-factor authentication requirements across your entire application.
                </p>
                <!-- Alert -->
                <div
                    class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                    <svg viewBox="0 0 32 32" aria-hidden="true"
                        class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                        <defs>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_security"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_security_dark"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                        </defs>
                        <g class="dark:hidden">
                            <circle cx="20" cy="20" r="12" fill="url(#_alert_security)"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                class="fill-(--icon-background)" fill-opacity="0.5"></path>
                            <path
                                d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                class="fill-(--icon-foreground)"></path>
                            <path
                                d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2 Z"
                                class="fill-(--icon-foreground)"></path>
                        </g>
                        <g class="hidden dark:inline">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                fill="url(#_alert_security_dark)"></path>
                        </g>
                    </svg>
                    <div class="ml-4 flex-auto">
                        <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Enabled by
                            Default</p>
                        <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                            Security middleware is enabled by default. Disable specific checks in your configuration if
                            needed.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-12">
                <!-- Account Disabling -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Account Disabling
                    </h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Immediately blocks access for disabled user accounts, logging them out automatically
                    </p>
                    <CodeBlock :code="codeExamples.accountDisabling" language="php" :show-copy-button="true" />
                </div>

                <!-- Password Expiry -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Password Expiry</h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Requires users to change passwords every 90 days (configurable)
                    </p>
                    <CodeBlock :code="codeExamples.passwordExpiry" language="php" :show-copy-button="true" />
                </div>

                <!-- Force Password Change -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Force Password Change
                    </h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Requires immediate password change when flagged by an administrator
                    </p>
                    <CodeBlock :code="codeExamples.forcePassword" language="php" :show-copy-button="true" />
                </div>

                <!-- Two-Factor Authentication -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Two-Factor
                        Authentication</h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Requires users to set up 2FA when enabled system-wide
                    </p>
                    <CodeBlock :code="codeExamples.twoFactor" language="php" :show-copy-button="true" />
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Backup System Section -->
        <section id="backup-system" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Backup Management UI</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel provides a visual interface for
                    <a href="https://spatie.be/docs/laravel-backup" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Spatie Laravel Backup
                    </a>, enabling automated database and file backups. Create, download, and restore backups through an
                    intuitive UI, without the need for terminal commands.
                </p>
                <!-- Alert -->
                <div
                    class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                    <svg viewBox="0 0 32 32" aria-hidden="true"
                        class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                        <defs>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_backup"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_backup_dark"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                        </defs>
                        <g class="dark:hidden">
                            <circle cx="20" cy="20" r="12" fill="url(#_alert_backup)"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                class="fill-(--icon-background)" fill-opacity="0.5"></path>
                            <path
                                d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                class="fill-(--icon-foreground)"></path>
                            <path
                                d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                class="fill-(--icon-foreground)"></path>
                        </g>
                        <g class="hidden dark:inline">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                fill="url(#_alert_backup_dark)"></path>
                        </g>
                    </svg>
                    <div class="ml-4 flex-auto">
                        <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Configuration
                            Required</p>
                        <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                            Requires Spatie Laravel Backup to be configured. See the installation guide for setup
                            instructions.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- System Health Monitoring Section -->
        <section id="system-health" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">
                    System Health Monitoring
                </h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel monitors system health with automated checks powered by
                    <a href="https://spatie.be/docs/laravel-health" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Spatie Laravel Health
                    </a>. Track disk space, database connections, cache systems, queue status, environment settings,
                    debug mode, and application optimization - all with real-time status monitoring and email
                    notifications.
                </p>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Data Tables Section -->
        <section id="data-tables" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Data Tables</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel provides powerful data tables built on
                    <a href="https://tanstack.com/table/latest" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        TanStack Table
                    </a>
                    with complete data management features. Includes row selection with bulk actions, server-side
                    pagination and sorting, built-in search, CSV export, and dark mode support.
                </p>
            </div>

            <div class="space-y-12">
                <!-- Frontend Setup -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Frontend Setup</h3>
                    <CodeBlock :code="codeExamples.frontendSetup" language="javascript" :show-copy-button="true" />
                </div>

                <!-- Template Usage -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Template Usage</h3>
                    <CodeBlock :code="codeExamples.templateUsage" language="vue" :show-copy-button="true" />
                </div>

                <!-- Backend Service -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Backend Service</h3>
                    <CodeBlock :code="codeExamples.backendService" language="php" :show-copy-button="true" />
                </div>

                <!-- Backend Controller -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Backend Controller
                    </h3>
                    <CodeBlock :code="codeExamples.backendController" language="php" :show-copy-button="true" />
                </div>

                <!-- Action Buttons -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Action Buttons
                        (Optional)</h3>
                    <CodeBlock :code="codeExamples.actionButtons" language="javascript" :show-copy-button="true" />
                </div>

                <!-- Bulk Delete -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Bulk Delete
                        (Optional)</h3>
                    <CodeBlock :code="codeExamples.bulkDelete" language="javascript" :show-copy-button="true" />
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Typesense Search Section -->
        <section id="typesense-search" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Typesense Search</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel delivers instant search results that understand typos. Powered by
                    <a href="https://typesense.org" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Typesense
                    </a>
                    and
                    <a href="https://laravel.com/docs/12.x/scout#main-content" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Laravel Scout
                    </a>
                    with real-time results, faceted filtering, geo-search, and multi-language support.
                </p>
                <!-- Alert -->
                <div
                    class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                    <svg viewBox="0 0 32 32" aria-hidden="true"
                        class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                        <defs>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_typesense"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                id="_alert_typesense_dark" gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                        </defs>
                        <g class="dark:hidden">
                            <circle cx="20" cy="20" r="12" fill="url(#_alert_typesense)"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                class="fill-(--icon-background)" fill-opacity="0.5"></path>
                            <path
                                d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                class="fill-(--icon-foreground)"></path>
                            <path
                                d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                class="fill-(--icon-foreground)"></path>
                        </g>
                        <g class="hidden dark:inline">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                fill="url(#_alert_typesense_dark)"></path>
                        </g>
                    </svg>
                    <div class="ml-4 flex-auto">
                        <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">Server Required</p>
                        <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                            Requires a Typesense server instance. You can use Typesense Cloud or self-host.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-12">
                <!-- API Key Setup -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">API Key Setup</h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Create a search-only API key for frontend use
                    </p>
                    <div class="mb-4">
                        <CodeBlock :code="codeExamples.generateSearchKey" language="bash" :show-copy-button="true" />
                    </div>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">

                    </p>
                    <CodeBlock :code="codeExamples.typesenseEnv" language="bash" :show-copy-button="true" />
                </div>

                <!-- Search Configuration -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Search Configuration
                    </h3>
                    <CodeBlock :code="codeExamples.searchConfig" language="javascript" :show-copy-button="true" />
                </div>

                <!-- Federated Search -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Federated Search</h3>
                    <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Search multiple collections at once with the FederatedSearch component
                    </p>
                    <CodeBlock :code="codeExamples.resultStructure" language="javascript" :show-copy-button="true" />
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- File Uploads Section -->
        <section id="file-uploads" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">File Uploads</h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel includes a modern drag-and-drop file upload interface powered by
                    <a href="https://pqina.nl/filepond/" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        FilePond
                    </a>, with image and PDF previews, upload size limits, and file type validation built in.
                </p>
            </div>

            <div class="space-y-12">
                <!-- Configuration -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Configuration</h3>
                    <CodeBlock :code="codeExamples.configExample" language="javascript" :show-copy-button="true" />
                </div>

                <!-- Script Setup -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Script Setup</h3>
                    <CodeBlock :code="codeExamples.scriptExample" language="javascript" :show-copy-button="true" />
                </div>
            </div>
        </section>
        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Real-time Notifications Section -->
        <section id="realtime-notifications" class="mb-8 sm:mb-16 scroll-mt-20">
            <div class="mb-4 sm:mb-8">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl text-zinc-900 dark:text-white">Real-time Notifications
                </h2>
                <p class="text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
                    GuacPanel supports real-time notifications using
                    <a href="https://laravel.com/docs/11.x/reverb" target="_blank"
                        class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                        Laravel Reverb
                    </a>, Laravel's first-party WebSocket server. Broadcast events from your backend and receive instant
                    updates in your Vue components - perfect for notifications, live updates, and collaborative
                    features.
                </p>

                <!-- Alert -->
                <div
                    class="my-6 flex gap-2.5 rounded-2xl border border-teal-500/20 bg-teal-50/50 p-4 leading-6 text-teal-900 dark:border-teal-500/30 dark:bg-teal-500/5 dark:text-teal-200">
                    <svg viewBox="0 0 32 32" aria-hidden="true"
                        class="mt-1 h-8 w-8 flex-none fill-teal-500 stroke-white dark:fill-teal-200/20 dark:stroke-teal-200">
                        <defs>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_reverb"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                            <radialGradient cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" id="_alert_reverb_dark"
                                gradientTransform="matrix(0 24.5001 -19.2498 0 16 5.5)">
                                <stop stop-color="#0EA5E9"></stop>
                                <stop stop-color="#22D3EE" offset=".527"></stop>
                                <stop stop-color="#818CF8" offset="1"></stop>
                            </radialGradient>
                        </defs>
                        <g class="dark:hidden">
                            <circle cx="20" cy="20" r="12" fill="url(#_alert_reverb)"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20 24.995c0-1.855 1.094-3.501 2.427-4.792C24.61 18.087 26 15.07 26 12.231 26 7.133 21.523 3 16 3S6 7.133 6 12.23c0 2.84 1.389 5.857 3.573 7.973C10.906 21.494 12 23.14 12 24.995V27a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.005Z"
                                class="fill-(--icon-background)" fill-opacity="0.5"></path>
                            <path
                                d="M25 12.23c0 2.536-1.254 5.303-3.269 7.255l1.391 1.436c2.354-2.28 3.878-5.547 3.878-8.69h-2ZM16 4c5.047 0 9 3.759 9 8.23h2C27 6.508 21.998 2 16 2v2Zm-9 8.23C7 7.76 10.953 4 16 4V2C10.002 2 5 6.507 5 12.23h2Zm3.269 7.255C8.254 17.533 7 14.766 7 12.23H5c0 3.143 1.523 6.41 3.877 8.69l1.392-1.436ZM13 27v-2.005h-2V27h2Zm1 1a1 1 0 0 1-1-1h-2a3 3 0 0 0 3 3v-2Zm4 0h-4v2h4v-2Zm1-1a1 1 0 0 1-1 1v2a3 3 0 0 0 3-3h-2Zm0-2.005V27h2v-2.005h-2ZM8.877 20.921C10.132 22.136 11 23.538 11 24.995h2c0-2.253-1.32-4.143-2.731-5.51L8.877 20.92Zm12.854-1.436C20.32 20.852 19 22.742 19 24.995h2c0-1.457.869-2.859 2.122-4.074l-1.391-1.436Z"
                                class="fill-(--icon-foreground)"></path>
                            <path
                                d="M20 26a1 1 0 1 0 0-2v2Zm-8-2a1 1 0 1 0 0 2v-2Zm2 0h-2v2h2v-2Zm1 1V13.5h-2V25h2Zm-5-11.5v1h2v-1h-2Zm3.5 4.5h5v-2h-5v2Zm8.5-3.5v-1h-2v1h2ZM20 24h-2v2h2v-2Zm-2 0h-4v2h4v-2Zm-1-10.5V25h2V13.5h-2Zm2.5-2.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2ZM18.5 18a3.5 3.5 0 0 0 3.5-3.5h-2a1.5 1.5 0 0 1-1.5 1.5v2ZM10 14.5a3.5 3.5 0 0 0 3.5 3.5v-2a1.5 1.5 0 0 1-1.5-1.5h-2Zm2.5-3.5a2.5 2.5 0 0 0-2.5 2.5h2a.5.5 0 0 1 .5-.5v-2Zm2.5 2.5a2.5 2.5 0 0 0-2.5-2.5v2a.5.5 0 0 1 .5.5h2Z"
                                class="fill-(--icon-foreground)"></path>
                        </g>
                        <g class="hidden dark:inline">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16 2C10.002 2 5 6.507 5 12.23c0 3.144 1.523 6.411 3.877 8.691.75.727 1.363 1.52 1.734 2.353.185.415.574.726 1.028.726H12a1 1 0 0 0 1-1v-4.5a.5.5 0 0 0-.5-.5A3.5 3.5 0 0 1 9 14.5V14a3 3 0 1 1 6 0v9a1 1 0 1 0 2 0v-9a3 3 0 1 1 6 0v.5a3.5 3.5 0 0 1-3.5 3.5.5.5 0 0 0-.5.5V23a1 1 0 0 0 1 1h.36c.455 0 .844-.311 1.03-.726.37-.833.982-1.626 1.732-2.353 2.354-2.28 3.878-5.547 3.878-8.69C27 6.507 21.998 2 16 2Zm5 25a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1 3 3 0 0 0 3 3h4a3 3 0 0 0 3-3Zm-8-13v1.5a.5.5 0 0 1-.5.5 1.5 1.5 0 0 1-1.5-1.5V14a1 1 0 1 1 2 0Zm6.5 2a.5.5 0 0 1-.5-.5V14a1 1 0 1 1 2 0v.5a1.5 1.5 0 0 1-1.5 1.5Z"
                                fill="url(#_alert_reverb_dark)"></path>
                        </g>
                    </svg>
                    <div class="ml-4 flex-auto">
                        <p class="not-prose font-display text-base text-teal-900 dark:text-teal-300">First-Party
                            Solution</p>
                        <p class="mt-2.5 text-sm text-teal-800 dark:text-teal-100">
                            Laravel Reverb is a blazing-fast, scalable WebSocket server built specifically for Laravel,
                            with no external dependencies required.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-12">
                <!-- Installation -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Installation</h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        Install Laravel Reverb via Composer:
                    </p>
                    <CodeBlock code="composer require laravel/reverb" language="bash" :show-copy-button="true" />
                </div>

                <!-- Enable Notifications -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Enable Notifications
                    </h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        First, enable notifications in your <code class="text-sm">.env</code> file:
                    </p>
                    <CodeBlock :code="codeExamples.reverbEnv" language="bash" :show-copy-button="true" />
                </div>

                <!-- Install Broadcasting -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Install Broadcasting
                        + Reverb</h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        Run the install command to set up broadcasting and generate Reverb credentials:
                    </p>
                    <CodeBlock :code="codeExamples.installBroadcasting" language="bash" :show-copy-button="true" />
                </div>

                <!-- Environment Configuration -->
                <div>
                    <h3 class="mb-3 text-md font-mono font-semibold text-zinc-900 dark:text-white">Generated Credentials
                    </h3>
                    <p class="mb-3 text-zinc-600 dark:text-zinc-400">
                        The install command will populate your <code class="text-sm">.env</code> file with Reverb
                        credentials:
                    </p>
                    <CodeBlock :code="codeExamples.reverbCredentials" language="bash" :show-copy-button="true" />
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="my-6 sm:my-12 border-t border-zinc-300 dark:border-zinc-600"></div>

        <!-- Next Steps -->
        <section id="next-steps" class="mb-8 scroll-mt-16">
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a href="/documentation/installation"
                    class="inline-flex items-center justify-center gap-2 border border-zinc-400 dark:border-zinc-600 bg-white px-6 py-3 font-mono text-sm font-bold uppercase tracking-wider text-[#3a3a3a] transition-all hover:bg-[#3a3a3a] hover:text-white dark:bg-[#252525] dark:text-white dark:hover:bg-white dark:hover:text-[#3a3a3a]">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Installation
                </a>

                <a href="/documentation/components"
                    class="inline-flex items-center justify-center gap-2 border border-zinc-400 dark:border-zinc-600 bg-[#3a3a3a] px-6 py-3 font-mono text-sm font-bold uppercase tracking-wider text-white transition-all hover:bg-white hover:text-[#3a3a3a] dark:bg-white dark:text-[#3a3a3a] dark:hover:bg-[#3a3a3a] dark:hover:text-white">
                    Components
                    <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </section>
    </div>

    <!-- Back to Top Button -->
    <button v-show="showBackToTop"
        class="fixed right-4 bottom-4 z-50 border border-zinc-400 dark:border-zinc-600 bg-white p-2 font-mono text-xs font-bold text-[#3a3a3a] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] sm:right-8 sm:bottom-8 dark:bg-[#252525] dark:text-white dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] dark:hover:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]"
        aria-label="Back to top" @click="scrollToTop">
        [^]
    </button>

    <ArticleNavigation :links="articleLinks" />
</template>
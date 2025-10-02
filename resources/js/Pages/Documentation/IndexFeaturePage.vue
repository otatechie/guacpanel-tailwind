<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'

defineOptions({
    layout: Public
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

    backendService: `// app/Services/DataTablePaginationService.php
public function resolvePerPageWithDefaults(Request $request, string $resourceName, ?int $filteredTotal = null): int
{
    return $this->resolvePerPage(
        $request,
        $resourceName,
        self::DEFAULT_PAGE_SIZE,      // 10
        self::ALLOWED_PAGE_SIZES,     // [10, 25, 50]
        self::ALLOW_ALL_OPTION,       // true
        self::MAX_ROWS_WHEN_ALL,      // 1000
        $filteredTotal
    );
}`,

    backendController: `// app/Http/Controllers/AdminUserController.php
public function __construct(private DataTablePaginationService $pagination) {}

public function index(Request $request)
{
    $perPage = $this->pagination->resolvePerPageWithDefaults($request, 'users');

    $users = User::query()
        ->with(['roles', 'permissions'])
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
            ];
        });

    return Inertia::render('Admin/User/IndexUserPage', [
        'users' => $users,
        'filters' => $this->pagination->buildFilters($request),
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
}`
}

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Permissions & Roles', href: '#permissions-roles' },
    { text: 'Security Middleware', href: '#middleware' },
    { text: 'Backup System', href: '#backup-system' },
    { text: 'System Health', href: '#system-health' },
    { text: 'Data Tables', href: '#data-tables' },
    { text: 'Typesense Search', href: '#typesense-search' },
    { text: 'File Uploads', href: '#file-uploads' }
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

    <div id="introduction" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-900 border p-8 md:p-12 mb-12">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <h1 class="text-3xl md:text-4xl font-bold">
                        Core Features
                    </h1>
                </div>

                <div class="max-w-2xl mx-auto mb-8">
                    <p class="text-gray-700 dark:text-gray-200 text-lg">
                        Build secure Laravel admin interfaces with GuacPanel's essential features:
                        authentication, permissions, health monitoring, backups, data tables, and search.
                        Everything you need, ready to use.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#authentication"
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    [Authentication]
                </a>
                    <a href="#data-tables"
                        class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    [Data_Tables]
                </a>
            </div>
            </div>
        </div>

        <section class="space-y-10">
            <!-- Authentication Section -->
            <section id="authentication" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Authentication
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Secure authentication system with Laravel Fortify and advanced security features
                    </p>
                </div>

                <!-- Laravel Fortify -->
                <div
                    class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono mb-8">
                    <div class="flex items-start mb-2">
                        <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                        <h4 class="text-green-700 dark:text-green-400 font-bold">Laravel Fortify</h4>
                    </div>
                    <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-mono">
                            Secure authentication with login, registration, 2FA, password reset, and email verification.
                        </p>
                    </div>
                </div>

                <!-- Passwordless Login -->
                <div
                    class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono mb-8">
                    <div class="flex items-start mb-2">
                        <span class="text-green-600 dark:text-green-500 mr-2">⌾</span>
                        <h4 class="text-green-700 dark:text-green-400 font-bold">Passwordless Login</h4>
                    </div>
                    <div class="pl-6 border-l border-green-200 dark:border-green-800 ml-2">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 font-mono">
                            Email-based magic link authentication for secure, password-free login.
                        </p>

                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                            Route Configuration
                        </h5>
                        <div class="bg-gray-800 rounded-lg mb-4">
                            <CodeBlock :code="codeExamples.routeConfig" language="php" />
                        </div>

                        <div
                            class="p-3 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                            <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                <span class="flex-shrink-0 text-xl">💡</span>
                                <span>
                                    <strong>Note:</strong>
                                    Enable passwordless login in Security Settings to use this feature.
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Magic Link Implementation -->
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono mb-8">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        Magic Link Implementation
                    </h3>

                    <h5 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">
                        Sending Magic Links
                    </h5>
                    <div class="bg-gray-800 rounded-lg mb-4">
                        <CodeBlock :code="codeExamples.magicLink" language="php" />
                    </div>

                    <h5 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">
                        Authentication Process
                    </h5>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.authProcess" language="php" />
                    </div>
                </div>

                <!-- Additional Auth Features -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-teal-600 dark:text-teal-400 mr-2">⌾</span>
                            <h4 class="text-gray-900 dark:text-gray-100 font-bold">Spatie Permissions</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                            Role-based access control with visual management
                        </p>
                        <a
                            href="#permissions-roles"
                            class="text-xs text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300">
                            Learn more →
                        </a>
                    </div>

                    <div
                        class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 font-mono">
                        <div class="flex items-start mb-2">
                            <span class="text-teal-600 dark:text-teal-400 mr-2">⌾</span>
                            <h4 class="text-gray-900 dark:text-gray-100 font-bold">Password Security</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                            Configure password expiry and enforce changes
                        </p>
                        <a
                            href="#middleware"
                            class="text-xs text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300">
                            Learn more →
                        </a>
                    </div>

                </div>
            </section>

            <!-- Permissions & Roles Section -->
            <section id="permissions-roles" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Permissions & Roles
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Fine-grained access control with Spatie Laravel-Permission
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-700 dark:text-gray-200 mb-4">
                        Built on
                        <a
                            href="https://spatie.be/docs/laravel-permission"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            Spatie's Laravel-Permission
                        </a>
                        , providing role-based access control through a clean UI.
                    </p>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-200 space-y-2 ml-4 text-sm">
                        <li>Create and assign roles visually</li>
                        <li>Set granular permissions per role</li>
                        <li>View permission inheritance</li>
                        <li>Manage user roles and permissions</li>
                    </ul>
                </div>
            </section>

            <!-- Security Middleware Section -->
            <section id="middleware" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Security Middleware
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Built-in middleware for account status, password policies, and 2FA
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">

                    <div class="space-y-6">
                        <!-- Account Disabling -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                1. Account Disabling
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Prevents disabled users from accessing the application
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.accountDisabling" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Password Expiry -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                2. Password Expiry
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Enforces password expiration (default 90 days)
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.passwordExpiry" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Force Password Change -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                3. Force Password Change
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Forces mandatory password updates when required
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.forcePassword" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Two-Factor Authentication -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                4. Two-Factor Authentication
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Enforces 2FA when required by system settings
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.twoFactor" language="php" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Backup System Section -->
            <section id="backup-system" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Backup System
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Manage backups through a clean UI powered by Spatie Laravel Backup
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-700 dark:text-gray-200 mb-4">
                        Built on
                        <a
                            href="https://spatie.be/docs/laravel-backup"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            Spatie's Laravel Backup
                        </a>
                        . Create, download, and manage backups without command line.
                    </p>

                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-200 space-y-2 ml-4 text-sm">
                        <li>View backup history and disk usage</li>
                        <li>One-click manual backups</li>
                        <li>Download, delete, or restore backups</li>
                        <li>System health indicators</li>
                    </ul>
                </div>
            </section>

            <!-- System Health Monitoring Section -->
            <section id="system-health" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        System Health Monitoring
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Monitor system health with automated checks for database, cache, disk, and more
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-700 dark:text-gray-200 mb-4">
                        Built on
                        <a
                            href="https://spatie.be/docs/laravel-health"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            Spatie Laravel Health
                        </a>
                        with real-time status monitoring and email notifications.
                    </p>

                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4 text-sm mb-6">
                        <li>Disk space usage monitoring</li>
                        <li>Database connection status</li>
                        <li>Cache system health</li>
                        <li>Queue system status</li>
                        <li>Environment verification</li>
                        <li>Debug mode detection</li>
                        <li>Application optimization checks</li>
                    </ul>

                    <div class="space-y-6">
                        <!-- Configuration -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                1. Health Checks Configuration
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.healthChecksConfig" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Controller -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                2. Controller Implementation
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.healthController" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Frontend -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                3. Frontend Integration
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.healthFrontend" language="javascript" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Data Tables Section -->
            <section id="data-tables" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Data Tables
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Powerful data tables built on TanStack Table with sorting, filtering, and export
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-700 dark:text-gray-200 mb-4">
                        Built on
                        <a
                            href="https://tanstack.com/table/latest"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            TanStack Table
                        </a>
                        with complete data management features.
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4 text-sm mb-6">
                        <li>Row selection with bulk actions</li>
                        <li>Server-side pagination and sorting</li>
                        <li>Built-in search</li>
                        <li>CSV export</li>
                        <li>Dark mode support</li>
                    </ul>

                    <div class="space-y-6">
                        <!-- Frontend Setup -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                1. Frontend Setup
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.frontendSetup" language="javascript" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Template Usage -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                2. Template Usage
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.templateUsage" language="vue" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Pagination Watcher -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                3. Pagination Watcher
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.paginationWatcher" language="javascript" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Backend Service -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                4. Backend Service
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.backendService" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Backend Controller -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                5. Backend Controller
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.backendController" language="php" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Action Buttons -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                6. Action Buttons (Optional)
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.actionButtons" language="javascript" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Bulk Delete -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                7. Bulk Delete (Optional)
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.bulkDelete" language="javascript" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Typesense Search Section -->
            <section id="typesense-search" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Typesense Search
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Lightning-fast typo-tolerant search powered by Typesense
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-700 dark:text-gray-200 mb-4">
                        Built on
                        <a
                            href="https://typesense.org"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            Typesense
                        </a>
                        and
                        <a
                            href="https://laravel.com/docs/12.x/scout#main-content"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            Laravel Scout
                        </a>
                        for real-time search.
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4 text-sm mb-6">
                        <li>Typo-tolerant search</li>
                        <li>Real-time results</li>
                        <li>Faceted search and filtering</li>
                        <li>Geo-search functionality</li>
                        <li>Multi-language support</li>
                    </ul>

                    <div class="space-y-6">
                        <!-- API Key Setup -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                1. API Key Setup
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Generate search-only API key:
                            </p>
                            <div class="bg-gray-800 rounded-lg mb-3">
                                <CodeBlock :code="codeExamples.generateSearchKey" language="bash" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Configure environment variables:
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.typesenseEnv" language="bash" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Search Configuration -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                2. Search Configuration
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.searchConfig" language="javascript" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Federated Search -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                3. Federated Search
                            </h5>
                            <p class="text-gray-700 dark:text-gray-200 text-xs mb-3">
                                Search across multiple collections simultaneously using FederatedSearch.vue
                            </p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.resultStructure" language="javascript" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- File Uploads Section -->
            <section id="file-uploads" class="scroll-mt-16 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        File Uploads
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl mx-auto">
                        Modern file upload interface with drag-and-drop and previews
                    </p>
                </div>
                <div
                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 font-mono">
                    <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
                        Built on
                        <a
                            href="https://pqina.nl/filepond/"
                            target="_blank"
                            class="border-b-2 border-blue-500 dark:border-blue-400">
                            FilePond
                        </a>
                        with drag-and-drop, image/PDF previews, size limits, and type validation.
                    </p>

                    <div class="space-y-6">
                        <!-- Configuration -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                1. Configuration
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.configExample" language="javascript" />
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Script Setup -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                2. Script Setup
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.scriptExample" language="javascript" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </section>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/documentation/installation"
                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-900 border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-300 hover:border-gray-600 dark:hover:border-gray-400 transition-all font-mono uppercase tracking-wider text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                [Installation]
            </a>

            <a href="/documentation/components"
                class="inline-flex items-center px-6 py-3 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono uppercase tracking-wider text-sm">
                [Components]
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button v-show="showBackToTop"
        class="fixed bottom-8 right-8 bg-green-500/20 border border-green-500 text-green-700 dark:text-green-200 hover:bg-green-500/30 transition-all font-mono text-sm p-3 z-50"
        aria-label="Back to top" @click="scrollToTop">
        [^]
    </button>

    <ArticleNavigation :links="articleLinks" />
</template>

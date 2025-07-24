<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'

defineOptions({
    layout: Public
})

// All code examples moved to constants
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

    tableExample: `// Import required dependencies
import { ref } from 'vue'
import Datatable from '@/Components/Datatable.vue'
import { createColumnHelper } from '@tanstack/vue-table'

// Initialize column helper
const columnHelper = createColumnHelper()

// Define table state
const loading = ref(false)
const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0
})

// Define table columns
const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => info.getValue()
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        cell: info => info.getValue()
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => (
            <span class={\`px-2 py-1 rounded-full text-xs font-medium \${
                info.getValue() === 'active'
                    ? 'bg-green-50 text-green-700'
                    : 'bg-red-50 text-red-700'
            }\`}>
                {info.getValue()}
            </span>
        )
    })
]`,

    templateExample: `<Datatable
    :data="users.data"
    :columns="columns"
    :loading="loading"
    :pagination="pagination"
    :search-fields="['name', 'email']"
    empty-message="No users found"
    empty-description="Users will appear here"
    export-file-name="users_export"
    @update:pagination="pagination = $event"
/>`,

    backendCode: `public function index(Request $request)
{
    return Inertia::render('Admin/User/IndexUserPage', [
        'users' => User::query()
            ->with(['roles', 'permissions']) 
            ->latest()                       
            ->paginate($request->input('per_page', 10)) //Paginated results
    ]);
}`,

    middlewareCode: `class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
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

// Action handlers
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

// Handle the bulk delete event
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

    googleFontsConfig: `// config/google-fonts.php
'fonts' => [
    'default' => 'https://fonts.googleapis.com/css2?family=Your+Font:wght@400;500;600;700&display=swap'
]

// resources/css/app.css
@theme {
    --font-sans: "Your Font", "sans-serif";
}`,

    generateSearchKey: `php generate-search-key.php`,

    typesenseEnv: `# .env
TYPESENSE_SEARCH_ONLY_KEY=your_generated_search_key_here
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http`,

    googleFontsUsage: `php artisan google-fonts:fetch
php artisan optimize`,

    federatedResults: `// federatedResults.value after searching users, financial_metrics, and system_notices
[
    // User results
    {
        collection_name: 'users',
        url: '/admin/user/123/edit',
        displayTitle: 'John Doe',
        displaySubtitle: 'john@example.com',
        id: 123,
        name: 'John Doe',
        email: 'john@example.com'
    },
    // Financial metric results
    {
        collection_name: 'financial_metrics',
        url: '/admin/financial-metrics/456',
        displayTitle: 'Revenue - Monthly',
        displaySubtitle: 'Amount: $1,234.56',
        id: 456,
        category: 'Revenue',
        type: 'Monthly',
        amount: 1234.56
    },
    // System notice results
    {
        collection_name: 'system_notices',
        url: '/admin/system-notice/789/edit',
        displayTitle: 'System Maintenance',
        displaySubtitle: 'Scheduled maintenance on...',
        id: 789,
        title: 'System Maintenance',
        content: 'Scheduled maintenance on...'
    }
]`,

    // Add new example for search configuration
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
}

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Permissions & Roles', href: '#permissions-roles' },
    { text: 'Security Middleware', href: '#middleware' },
    { text: 'Backup System', href: '#backup-system' },
    { text: 'Data Tables', href: '#data-tables' },
    { text: 'Typesense Search', href: '#typesense-search' },
    { text: 'Authentication Logs', href: '#activity-logs' },
    { text: 'Browser Sessions', href: '#browser-sessions' },
    { text: 'Activity Tracking', href: '#activity-tracking' },
    { text: 'File Uploads', href: '#file-uploads' },
    { text: 'Google Fonts', href: '#google-fonts' }
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

    <div id="introduction" class="max-w-7xl mx-auto lg:px-8">
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-teal-600 to-teal-700 dark:from-teal-900 dark:to-teal-800">
            <div class="relative z-10 p-8 md:p-12">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Core Features</h1>
                </div>
                <p class="text-lg text-teal-100 dark:text-teal-200 max-w-3xl mb-8">
                    Build secure Laravel admin interfaces with GuacPanel's essential features: authentication,
                    permissions, data tables, backup system, and activity tracking. Everything you need, ready to use.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#authentication"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-teal-600 hover:bg-teal-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Authentication
                    </a>
                    <a href="#data-tables"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-teal-500 text-white hover:bg-teal-400 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        Data Tables
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-teal-400/30 to-teal-500/30 blur-3xl rounded-full">
            </div>
        </div>

        <section class="space-y-10">
            <!-- Authentication Section -->
            <section id="authentication" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Authentication</h2>
                
                <!-- Introduction -->
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        GuacPanel is built on <strong>Laravel Fortify</strong>, which provides ready-to-use authentication features including login, registration, two-factor authentication, password reset, and email verification. Focus on building your app while Fortify handles the auth.
                    </p>
                </div>

                <!-- Passwordless Login -->
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Passwordless Login</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Passwordless authentication allows users to sign in securely using email-based magic links instead of traditional passwords. This approach enhances security by eliminating password-related vulnerabilities while providing a smoother user experience.
                    </p>
                    
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Route Configuration</h4>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.routeConfig" language="php" />
                    </div>
                    
                    <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-6">
                        <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                            <span class="flex-shrink-0 text-xl">💡</span>
                            <span><strong>Pro Tip:</strong> Passwordless login is currently disabled. Enable it in your Security Settings to use this feature.</span>
                        </p>
                    </div>
                </div>

                <!-- Magic Link Implementation -->
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Magic Link Implementation</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Implement magic link authentication with secure token generation and email delivery.
                    </p>
                    
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Sending Magic Links</h4>
                    <div class="bg-gray-800 rounded-lg mb-6">
                        <CodeBlock :code="codeExamples.magicLink" language="php" />
                    </div>
                    
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Authentication Process</h4>
                    <div class="bg-gray-800 rounded-lg">
                        <CodeBlock :code="codeExamples.authProcess" language="php" />
                    </div>
                </div>

                <!-- Additional Auth Features -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Spatie Permissions
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Role-based access control with visual management
                                </p>
                                <a href="#permissions-roles"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Password Security Policies
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Configure password expiry and changes
                                </p>
                                <a href="#middleware"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Authentication Logs
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Track login attempts and sessions
                                </p>
                                <a href="#activity-logs"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-gradient-to-br from-teal-500/10 to-teal-500/5 rounded-xl group-hover:from-teal-500/20 group-hover:to-teal-500/10 transition-all duration-300">
                                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400 transition-transform duration-300 group-hover:scale-110"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-teal-500 dark:group-hover:text-teal-400 transition-colors duration-300">
                                    Activity Logs
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                    Monitor user actions and system changes
                                </p>
                                <a href="#activity-tracking"
                                    class="inline-flex items-center text-sm text-teal-500 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300 transition-colors duration-300">
                                    Learn more
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Permissions & Roles Section -->
            <section id="permissions-roles" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Permissions & Roles</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Built on <a href="https://spatie.be/docs/laravel-permission" target="_blank"
                                class="border-b-2 border-blue-500 dark:border-blue-400">Spatie's
                                Laravel-Permission</a>,
                            GuacPanel offers fine-grained access control through an easy-to-use roles and
                            permissions system. Manage everything through a clean UI - for managing users' roles,
                            and permissions.
                        </p>
                        <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                            <li>Create and assign roles visually</li>
                            <li>Set granular permissions for each role</li>
                            <li>View permission inheritance at a glance</li>
                            <li>View all users and their roles and permissions</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Security Middleware Section -->
            <section id="middleware" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Security Middleware</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Protect your Laravel app with GuacPanel's built-in security middleware. Handle account
                            status, password policies, and two-factor authentication automatically on every request.
                        </p>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Key Middleware Components</h3>
                        <div class="grid md:grid-cols-1 gap-4">
                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">1.</span> Account Disabling
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Prevents disabled user accounts
                                    from accessing the application</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.accountDisabling" language="php" />
                                </div>
                                <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-8">
                                    <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">💡</span><span><strong>Pro Tip:</strong>
                                            The <code class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">disable.account</code>
                                            middleware checks if the authenticated user has been disabled. If so, it
                                            logs them out and redirects to the login page with an error
                                            message.</span>
                                    </p>
                                </div>
                            </div>
                            <hr class="border-gray-100 dark:border-gray-700 my-4">
                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">2.</span> Password Expiry
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Enforces password expiration for
                                    user accounts</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.passwordExpiry" language="php" />
                                </div>
                                <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-8">
                                    <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">💡</span><span><strong>Pro Tip:</strong>
                                            The <code class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">password.expired</code>
                                            middleware enforces password expiry (default 90 days). Customize the
                                            expiry period in the <code class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">User</code>
                                            model.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-1 gap-4 mt-8">
                            <hr class="border-gray-100 dark:border-gray-700 my-4">
                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">3.</span> Force Password Change
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Forces users to change their
                                    password after a certain period</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.forcePassword" language="php" />
                                </div>
                                <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-8">
                                    <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">💡</span><span><strong>Pro Tip:</strong>
                                            The <code class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">force.password.change</code>
                                            middleware enforces mandatory password updates when required.</span>
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <hr class="border-gray-100 dark:border-gray-700 my-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">4.</span> Two-Factor Authentication
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Enforces two-factor authentication
                                    for user accounts</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.twoFactor" language="php" />
                                </div>
                                <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-8">
                                    <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">💡</span><span><strong>Pro Tip:</strong>
                                            The <code class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">require.two.factor</code>
                                            middleware ensures users set up 2FA when it's required by system
                                            settings.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Backup System Section -->
            <section id="backup-system" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Backup System</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            GuacPanel provides a clean interface for <a href="https://spatie.be/docs/laravel-backup"
                                target="_blank" class="border-b-2 border-blue-500 dark:border-blue-400">Spatie's
                                Laravel Backup</a>.
                            Create, download, and manage backups through the UI - no command line needed. Monitor
                            backup health and delete old backups with ease.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Backup UI</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            The backup interface provides an intuitive way to manage your system backups directly
                            from the admin panel, letting you create, download, and manage backups with ease.
                        </p>

                        <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                            <li><span class="font-semibold">Backup Dashboard:</span> View backup history, disk usage
                                statistics, and system health indicators at a glance.</li>
                            <li><span class="font-semibold">One-Click Backups:</span> Create manual backups
                                instantly with a single click, without needing command line access.</li>
                            <li><span class="font-semibold">Backup Management:</span> Download, delete, or restore
                                backups through a user-friendly interface with clear status indicators.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Data Tables Section -->
            <section id="data-tables" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Data Tables</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            GuacPanel provides a powerful data table interface built on <a
                                href="https://tanstack.com/table/latest" target="_blank"
                                class="border-b-2 border-blue-500 dark:border-blue-400">TanStack Table</a>. Our
                            <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">Datatable</code>
                            component offers a complete solution for data management.
                        </p>
                        <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                            <li>Row selection with bulk actions</li>
                            <li>Server-side pagination and sorting</li>
                            <li>Built-in search with customizable fields</li>
                            <li>CSV export of selected or all rows</li>
                            <li>Responsive design with dark mode support</li>
                            <li>Customizable page sizes and loading states</li>
                        </ul>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Implementation</h3>

                        <div class="grid md:grid-cols-1 gap-4">
                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">1.</span> Table Configuration
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Configure the table with the
                                    following options:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.tableExample" language="javascript" />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">2.</span> Template Usage
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Use the following template to
                                    create a new table:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.templateExample" language="vue" />
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-1 gap-4 mt-8">
                            <div class="space-y-4">
                                <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <span class="mr-3">3.</span> Backend Integration
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Use the following code to integrate
                                    the table with the backend:</p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.backendCode" language="php" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">4.</span> Action Buttons
                            </h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Use the following code to add action
                                buttons to the table:</p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.actionButtons" language="javascript" />
                            </div>
                        </div>

                        <div class="mt-8">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                                4. Bulk Delete
                            </h5>
                            <div class="bg-gray-800 rounded-lg mb-8">
                                <CodeBlock :code="codeExamples.bulkDelete" language="javascript" />
                            </div>

                            <h5 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Detailed Guide
                            </h5>
                            <div class="space-y-6">
                                <!-- Setup Section -->
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <h6 class="font-medium text-gray-900 dark:text-white mb-2">1. Setup</h6>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                                        Add the bulk delete feature to your Datatable component:
                                    </p>
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded p-3">
                                        <code class="text-sm">:bulk-delete-route="route('your.bulk.delete.route')"</code>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        This enables row selection checkboxes and the bulk delete button
                                        automatically.
                                    </p>
                                </div>

                                <!-- Event Handling Section -->
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <h6 class="font-medium text-gray-900 dark:text-white mb-2">2. Event Handling
                                    </h6>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                                        Listen for the bulk-delete event:
                                    </p>
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded p-3">
                                        <code class="text-sm">@bulk-delete="handleBulkDelete"</code>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        The event provides selected rows data in the callback parameter.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Typesense Search Section -->
            <section id="typesense-search" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Typesense Search</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            GuacPanel integrates <a href="https://typesense.org" target="_blank"
                                class="border-b-2 border-blue-500 dark:border-blue-400">Typesense</a>, a
                            lightning-fast, open-source search engine that provides typo-tolerant search functionality. Before
                            proceeding, ensure you have set up <a
                                href="https://laravel.com/docs/12.x/scout#main-content" target="_blank"
                                class="border-b-2 border-blue-500 dark:border-blue-400">Typesense with Laravel
                                Scout</a> and your Typesense server is running.
                        </p>
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-2">Key Features</h4>
                        <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                            <li>Typo-tolerant search with configurable tolerance levels</li>
                            <li>Real-time search with instant results</li>
                            <li>Faceted search and filtering capabilities</li>
                            <li>Geo-search functionality</li>
                            <li>Multi-language support</li>
                        </ul>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Getting Started</h3>

                        <div class="grid md:grid-cols-1 gap-6">
                            <div class="dark:bg-gray-800/50">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">1. API Key Setup</h4>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    First, generate a secure search-only API key using the provided utility script:
                                </p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.generateSearchKey" language="bash" />
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mt-4 mb-4">
                                    Configure your environment variables with the generated key:
                                </p>
                                <div class="bg-gray-800 rounded-lg">
                                    <CodeBlock :code="codeExamples.typesenseEnv" language="bash" />
                                </div>
                                <div class="p-4 bg-gradient-to-br from-amber-50 to-amber-50 dark:from-amber-900/20 dark:to-amber-900/20 rounded-lg border border-amber-400 dark:border-amber-800/30 mt-4">
                                    <p class="text-sm text-amber-800 dark:text-amber-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">⚠️</span>
                                        <span><strong>Security Note:</strong> Never expose your admin API key in frontend code. Always use scoped search-only API keys. For detailed information about API key management, visit the <a
                                            href="https://typesense.org/docs/29.0/api/api-keys.html#create-an-api-key"
                                            target="_blank"
                                            class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300 underline">Typesense
                                            API Keys Documentation</a>.</span>
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">2. Basic Search Implementation</h4>
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Example Search Configuration</h5>
                                    <div class="bg-gray-800 rounded-lg">
                                        <CodeBlock :code="codeExamples.searchConfig" language="javascript" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">3. Federated Search</h4>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    GuacPanel implements federated search to search across multiple collections simultaneously.
                                    The search is handled by the <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">FederatedSearch.vue</code>
                                    component.
                                </p>

                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mt-4">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Result Structure</h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Each collection's results should follow this standard format:
                                    </p>
                                    <div class="bg-gray-800 rounded-lg">
                                        <CodeBlock :code="codeExamples.resultStructure" language="javascript" />
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mt-4">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Example Results</h5>
                                    <div class="bg-gray-800 rounded-lg">
                                        <CodeBlock :code="codeExamples.federatedResults" language="javascript" />
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mt-4">
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Adding New Collections</h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        To add a new collection to the federated search:
                                    </p>
                                    <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                        <li>Add the collection configuration to the searches array</li>
                                        <li>Create a result mapper for the collection</li>
                                        <li>Include the mapped results in the final results array</li>
                                    </ol>
                                </div>

                                <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30 mt-4">
                                    <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                        <span class="flex-shrink-0 text-xl">💡</span>
                                        <span><strong>Pro Tip:</strong> Each result maintains both its standardized display fields (displayTitle, displaySubtitle)
                                            and its original document fields, giving you flexibility in how you use the data.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Authentication Logs Section -->
            <section id="activity-logs" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Authentication Logs</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none">
                            <p>GuacPanel automatically tracks all login activity, storing user details, timestamp,
                                IP, device info, and login status. The system uses Laravel events for logging and
                                provides a dashboard view where you can filter logs by user, date, success/failure,
                                and IP address.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Browser Sessions Section -->
            <section id="browser-sessions" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Browser Session Management</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none">
                            <p>GuacPanel uses <a href="https://laravel.com/docs/session" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">Laravel Sessions</a> to
                                let users monitor and control their active sessions, showing device type, browser,
                                and last activity. Users can log out from individual sessions or all devices at
                                once.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Activity Tracking Section -->
            <section id="activity-tracking" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Activity Tracking</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none">
                            <p>GuacPanel uses <a href="https://github.com/owen-it/laravel-auditing" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">Laravel Auditing </a> to
                                track model changes, providing a clean interface to view, search, and filter all
                                user activity logs.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- File Uploads Section -->
            <section id="file-uploads" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">File Uploads</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none">
                            <p>GuacPanel uses <a href="https://pqina.nl/filepond/" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">FilePond</a> to provide
                                a modern file upload interface with drag-and-drop, image/PDF previews, size limits,
                                and type validation.</p>
                        </div>

                        <div class="mt-8">
                            <hr class="border-gray-100 dark:border-gray-700 my-4">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">1.</span> Configuration
                            </h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Configure the file upload with the
                                following options:</p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.configExample" language="javascript" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">2.</span> Script Setup
                            </h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Use the following script to setup the
                                file upload:</p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.scriptExample" language="javascript" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">3.</span> Template
                            </h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Use the following template to create a
                                new file upload:</p>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.templateExample" language="vue" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Events</h3>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4 text-sm">
                                <li><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">@processfile</code>
                                    - Emitted when a file is uploaded</li>
                                <li><code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">@removefile</code>
                                    - Emitted when a file is removed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Google Fonts Section -->
            <section id="google-fonts" class="scroll-mt-16">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Google Fonts</h2>
                <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-200/50 dark:border-gray-700/50 p-6 md:p-8 hover:border-teal-500/50 dark:hover:border-teal-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-teal-500/10">
                    <div class="prose dark:prose-invert max-w-none">
                        <p>GuacPanel uses <a href="https://github.com/spatie/laravel-google-fonts" target="_blank"
                                class="border-b-2 border-blue-500 dark:border-blue-400">Spatie's Laravel Google
                                Fonts</a> to provide self-hosted fonts with local caching for better performance.
                        </p>

                        <div class="mt-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">1.</span> Configuration
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.googleFontsConfig" language="php" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <span class="mr-3">2.</span> Usage
                            </h5>
                            <div class="bg-gray-800 rounded-lg">
                                <CodeBlock :code="codeExamples.googleFontsUsage" language="bash" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-end">
            <a href="/documentation/components"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div class="font-medium text-gray-800 dark:text-white group-hover:text-blue-500 dark:group-hover:text-teal-400">
                        Components</div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <button v-show="showBackToTop" @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-teal-600 text-white p-3 rounded-full shadow-lg hover:bg-teal-500 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
        aria-label="Back to top">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <ArticleNavigation :links="articleLinks" />
</template>

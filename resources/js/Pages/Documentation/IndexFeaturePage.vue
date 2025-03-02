<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import hljs from 'highlight.js/lib/core'
import javascript from 'highlight.js/lib/languages/javascript'
import bash from 'highlight.js/lib/languages/bash'
import 'highlight.js/styles/github-dark.css'

// Register languages
hljs.registerLanguage('javascript', javascript)
hljs.registerLanguage('bash', bash)

// Highlight.js directive
const vHighlight = {
    mounted(el) {
        hljs.highlightElement(el)
    },
    updated(el) {
        hljs.highlightElement(el)
    }
}

defineOptions({
    layout: Public
})

const routeConfigCode = ref(`// Magic Link Authentication Routes
Route::middleware(['guest', 'web'])->group(function () {
    Route::controller(MagicLinkController::class)->group(function () {
        Route::get('/magic-link/register', 'create')->name('magic.register.create');
        Route::post('/register/magic-link', 'register')->name('magic.register');
        Route::post('/login/magic-link', 'login')->name('magic.login.request');
        Route::get('/magic-link/{token}', 'authenticate')->name('magic.login');
    });
});`)

const featureToggleCode = ref(`protected function checkPasswordlessEnabled()
{
    $passwordlessEnabled = DB::table('settings')->value('passwordless_login') ?? true;
    if (!$passwordlessEnabled) {
        abort(404);
    }
}`)

const magicLinkCode = ref(`protected function sendLoginLink(User $user)
{
    $url = URL::temporarySignedRoute(
        'magic.login',
        now()->addMinutes(10),
        ['token' => $user->id]
    );

    Mail::to($user)->send(new MagicLoginLink($url));
}`)

const authProcessCode = ref(`public function authenticate(Request $request)
{
    if (!$request->hasValidSignature()) {
        return redirect()->route('login')
            ->with('error', 'Magic link expired.');
    }

    $user = User::findOrFail($request->token);
    auth()->guard('web')->login($user);
    $request->session()->regenerate();

    return redirect()->intended(config('fortify.home'));
}`)

const accountDisablingCode = ref(`public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && auth()->user()->is_disabled) {
        auth()->logout();
        session()->flash('error', 'Your account has been disabled.');
        return redirect()->route('login');
    }

    return $next($request);
}`)

const passwordExpiryCode = ref(`public function handle(Request $request, Closure $next): Response
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
}`)

const forcePasswordCode = ref(`public function handle(Request $request, Closure $next): Response
{
    if (auth()->check() && auth()->user()->force_password_change) {
        session()->flash('warning', 'You must change your password before continuing.');
        return redirect()->route('user.force.password.change');
    }

    return $next($request);
}`)

const twoFactorCode = ref(`public function handle(Request $request, Closure $next): Response
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
}`)

const tableExampleCode = ref(`// Import required dependencies
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Datatable from '@/Components/Datatable.vue'

// Initialize column helper and state
const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.users.current_page,
    per_page: Number(props.users.per_page),
    total: props.users.total
})

// Define table columns
const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => h('span', {
            'aria-label': \`User: \${info.getValue()}\`
        }, info.getValue())
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => h('span', {
            class: \`px-2 py-1 rounded-full text-xs font-medium \${
                info.getValue() === 'active'
                    ? 'bg-green-50 text-green-700'
                    : 'bg-red-50 text-red-700'
            }\`,
            role: 'status'
        }, info.getValue())
    })
]

// Handle pagination changes
watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('users.index'),
        {
            page: newPagination.current_page,
            per_page: newPagination.per_page
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        }
    )
}, { deep: true })`)

const templateExampleCode = ref(`<Datatable
    :data="users.data"
    :columns="columns"
    :loading="loading"
    :pagination="pagination"
    :search-fields="['name', 'email']"
    empty-message="No users found"
    empty-description="Users will appear here"
    export-file-name="users_export"
    @update:pagination="pagination = $event"
/>`)

const backendCode = ref(`public function index(Request $request)
{
    return Inertia::render('Admin/User/IndexUserPage', [
        'users' => User::query()
            ->with(['roles', 'permissions'])  // Eager load relationships
            ->latest()                        // Order by newest first
            ->paginate($request->input('per_page', 10))  // Get paginated results
    ]);
}`)


const middlewareCode = ref(`class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}`)

const actionButtonsCode = ref(`columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => {
        return (
            <div class="flex space-x-2">
                <button
                    onClick={() => handleEdit(row.original)}
                    class="text-blue-600 hover:text-blue-800"
                >
                    Edit
                </button>
                <button
                    onClick={() => handleDelete(row.original)}
                    class="text-red-600 hover:text-red-800"
                >
                    Delete
                </button>
            </div>
        )
    }
})`)

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Permissions & Roles', href: '#permissions-roles' },
    { text: 'Security Middleware', href: '#middleware' },
    { text: 'Backup System', href: '#backup-system' },
    { text: 'Data Tables', href: '#datatables' },
    { text: 'Activity Tracking', href: '#auditing' }
]

// Fixed the language mappings for highlight.js
const fixHighlightLanguages = () => {
    // Map language-vue to language-html/xml since highlight.js doesn't have a Vue mode
    document.querySelectorAll('pre code.language-vue').forEach((block) => {
        block.className = 'language-xml'
    })
}

// Function to apply syntax highlighting
const applyHighlighting = () => {
    nextTick(() => {
        fixHighlightLanguages()
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block)
        })
    })
}

onMounted(() => {
    document.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightElement(block)
    })
})

// Watch for changes in any code blocks and reapply highlighting
watch([
    routeConfigCode, featureToggleCode, magicLinkCode, authProcessCode,
    accountDisablingCode, passwordExpiryCode, forcePasswordCode,
    twoFactorCode, tableExampleCode, templateExampleCode,
    backendCode, middlewareCode, actionButtonsCode
], () => {
    applyHighlighting()
}, { deep: true })

// Handle edit clicks
const handleEdit = (user) => {
    router.get(route('users.edit', user.id))
}

// Handle delete clicks (you can implement this as needed)
const handleDelete = (user) => {
    // Implementation for delete functionality
    console.log('Delete user:', user)
}

// ... rest of your existing code ...
</script>

<template>

    <Head title="Features - Obomaa" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-purple-600 to-indigo-600 dark:from-purple-900 dark:to-indigo-900">
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
                <p class="text-lg text-purple-100 dark:text-purple-200 max-w-3xl mb-8">
                    Explore Obomaa's powerful built-in features including authentication, permissions, security middleware, backup systems,
                    data tables, and activity tracking. Each feature is designed to help you build secure and scalable admin interfaces.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#authentication"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-purple-600 hover:bg-purple-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Authentication
                    </a>
                    <a href="#datatables"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-purple-500 text-white hover:bg-purple-400 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        Data Tables
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-400/30 to-indigo-400/30 blur-3xl rounded-full">
            </div>
        </div>

        <div class="flex flex-col space-y-8 mb-8">
            <ArticleNavigation :links="articleLinks" />

            <!-- Main Content Sections -->
            <div class="space-y-16">
                <!-- Authentication Section -->
                <section id="authentication" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Authentication</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Introduction -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">
                                Laravel Fortify automatically scaffolds the login, two-factor login, registration,
                                password
                                reset, and email verification features for your project, allowing you to start building
                                the
                                features you care about instead of worrying about the nitty-gritty details of user
                                authentication.
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                <a href="https://laravel.com/docs/fortify" target="_blank"
                                    class="border-b-2 border-purple-500 dark:border-purple-400">Learn more about Laravel
                                    Fortify</a>
                            </p>
                        </div>

                        <!-- Passwordless Login -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Passwordless Login</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Passwordless authentication allows users to sign in securely using email-based magic
                                links
                                instead of traditional passwords. This modern approach enhances security by eliminating
                                password-related vulnerabilities while providing a smoother user experience.
                            </p>

                            <div
                                class="mt-6 p-4 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-lg border border-yellow-400 dark:border-yellow-800/30">
                                <p class="text-sm text-yellow-800 dark:text-yellow-300 flex items-start space-x-2">
                                    <span class="flex-shrink-0 text-xl">💡</span>
                                    <span><strong>Pro Tip:</strong> Passwordless login is perfect for non-technical
                                        users -
                                        no more forgotten passwords or security concerns!</span>
                                </p>
                            </div>
                        </div>

                        <!-- Implementation -->
                        <div class="space-y-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Implementation</h3>
                            <div class="grid gap-8">
                                <!-- Route Configuration -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">1.</span> Route Configuration
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        First, set up the routes for magic link authentication:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(routeConfigCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-php">{{ routeConfigCode }}</code></pre>
                                    </div>
                                </div>

                                <!-- Rest of authentication content... -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Permissions & Roles Section -->
                <section id="permissions-roles" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Permissions & Roles</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Introduction -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Obomaa provides a robust permissions and roles system built on top of Spatie's
                                Laravel-Permission package. This system allows you to control access to different parts
                                of your application with granular precision.
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                <a href="https://spatie.be/docs/laravel-permission" target="_blank"
                                    class="border-b-2 border-amber-500 dark:border-amber-400">Learn more about
                                    Laravel-Permission</a>
                            </p>
                        </div>

                        <!-- Key Concepts -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Key Concepts</h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div
                                    class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800/30">
                                    <h4 class="font-semibold text-amber-800 dark:text-amber-300 mb-2">Permissions</h4>
                                    <p class="text-amber-700 dark:text-amber-400 text-sm">
                                        Granular access controls that define what actions a user can perform. Examples:
                                        <code>create-user</code>, <code>edit-post</code>, <code>delete-comment</code>.
                                    </p>
                                </div>

                                <div
                                    class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800/30">
                                    <h4 class="font-semibold text-amber-800 dark:text-amber-300 mb-2">Roles</h4>
                                    <p class="text-amber-700 dark:text-amber-400 text-sm">
                                        Collections of permissions that can be assigned to users. Examples:
                                        <code>admin</code>, <code>editor</code>, <code>subscriber</code>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Implementation -->
                        <div class="space-y-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Implementation</h3>
                            <div class="grid gap-8">
                                <!-- Middleware Setup -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">1.</span> Role-Based Middleware
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Protect routes with role-based middleware:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(middlewareCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-php">{{ middlewareCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> The
                                        <pre
                                            class="inline bg-teal-50 dark:bg-teal-900/30 px-2 py-1 rounded text-teal-700 dark:text-teal-300 text-xs font-mono">App\Http\Middleware\RoleMiddleware</pre>
                                        checks if the authenticated user has the specified role. If not, it returns a
                                        403 Unauthorized response.</p>
                                    </div>
                                </div>

                                <!-- Route Protection -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">2.</span> Protecting Routes
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Apply the middleware to your routes:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText('Route::middleware([\'role:admin\'])->group(function () {\n    Route::get(\'/admin/dashboard\', [AdminController::class, \'dashboard\']);\n    Route::resource(\'/admin/users\', UserController::class);\n});')">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-php">Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::resource('/admin/users', UserController::class);
});</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> Only users with the 'admin' role can access
                                            the admin dashboard and user management routes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Security Middleware Section -->
                <section id="middleware" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Security Middleware</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Introduction -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Obomaa includes several middleware components that enhance security and user management.
                                These middleware classes intercept HTTP requests before they reach your controllers,
                                allowing you to implement security checks, enforce policies, and manage user sessions.
                            </p>
                        </div>

                        <!-- Key Middleware Components -->
                        <div class="space-y-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Key Middleware
                                Components</h3>

                            <!-- Account Disabling Middleware -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span class="mr-3">1.</span> Account Disabling
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Prevents disabled user accounts from accessing the application:
                                </p>

                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(accountDisablingCode)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre><code class="language-php">{{ accountDisablingCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>What this does:</strong> The <code
                                            class="bg-gray-200 dark:bg-teal-900/30 px-2 py-1 rounded text-gray-900 dark:text-teal-300 text-xs font-mono">disable.account</code>
                                        checks
                                        if the authenticated user has been disabled. If so, it logs them out and
                                        redirects to the login page with an error message.</p>
                                </div>
                            </div>

                            <!-- Password Expiry Middleware -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span class="mr-3">2.</span> Password Expiry
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Enforces password expiration policies:
                                </p>

                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(passwordExpiryCode)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre><code class="language-php">{{ passwordExpiryCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>What this does:</strong> The <code
                                            class="bg-gray-200 dark:bg-teal-900/30 px-2 py-1 rounded text-gray-900 dark:text-teal-300 text-xs font-mono">password.expired</code>
                                        checks
                                        if the authenticated user's password has expired based on the system settings.
                                        If expired, redirects to a password change page.</p>
                                </div>
                            </div>

                            <!-- Force Password Change Middleware -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span class="mr-3">3.</span> Force Password Change
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Forces users to change their password when required:
                                </p>

                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(forcePasswordCode)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre><code class="language-php">{{ forcePasswordCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>What this does:</strong> The <code
                                            class="bg-gray-200 dark:bg-teal-900/30 px-2 py-1 rounded text-gray-900 dark:text-teal-300 text-xs font-mono">force.password.change</code>
                                        checks if the user is flagged for a mandatory password change. If so, redirects
                                        to the password change page.</p>
                                </div>
                            </div>

                            <!-- Two-Factor Authentication Middleware -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span class="mr-3">4.</span> Two-Factor Authentication
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Enforces two-factor authentication requirements:
                                </p>

                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(twoFactorCode)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre><code class="language-php">{{ twoFactorCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>What this does:</strong> The
                                        <code
                                            class="bg-gray-200 dark:bg-teal-900/30 px-2 py-1 rounded text-gray-900 dark:text-teal-300 text-xs font-mono">require.two.factor</code>
                                        checks if two-factor
                                        authentication is enabled in system settings. If so and the user hasn't
                                        configured 2FA, redirects to the setup page.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Backup System Section -->
                <section id="backup-system" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Backup System</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Introduction -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Obomaa includes a powerful backup system built on <a
                                    href="https://spatie.be/docs/laravel-backup" target="_blank"
                                    class="border-b-2 border-emerald-500 dark:border-emerald-400">Spatie's Laravel
                                    Backup package</a>. This system enables you to:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                                <li>Automatically backup your database and files</li>
                                <li>Schedule regular backups</li>
                                <li>Store backups across multiple storage locations (local, S3, Dropbox, etc.)</li>
                                <li>Monitor backup health</li>
                                <li>Automatically manage and remove outdated backups</li>
                            </ul>
                        </div>

                        <!-- Backup UI Features -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Backup UI</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                The backup interface provides an intuitive way to manage your system backups directly
                                from the admin panel, letting you create, download, and manage backups with ease.
                            </p>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div
                                    class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800/30">
                                    <h4 class="font-semibold text-emerald-800 dark:text-emerald-300 mb-2">Backup
                                        Dashboard</h4>
                                    <p class="text-emerald-700 dark:text-emerald-400 text-sm">
                                        View backup history, disk usage statistics, and system health indicators at a
                                        glance.
                                    </p>
                                </div>

                                <div
                                    class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800/30">
                                    <h4 class="font-semibold text-emerald-800 dark:text-emerald-300 mb-2">One-Click
                                        Backups</h4>
                                    <p class="text-emerald-700 dark:text-emerald-400 text-sm">
                                        Create manual backups instantly with a single click, without needing command
                                        line access.
                                    </p>
                                </div>

                                <div
                                    class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800/30">
                                    <h4 class="font-semibold text-emerald-800 dark:text-emerald-300 mb-2">Backup
                                        Management</h4>
                                    <p class="text-emerald-700 dark:text-emerald-400 text-sm">
                                        Download, delete, or restore backups through a user-friendly interface with
                                        clear status indicators.
                                    </p>
                                </div>

                                <div
                                    class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800/30">
                                    <h4 class="font-semibold text-emerald-800 dark:text-emerald-300 mb-2">Health
                                        Monitoring</h4>
                                    <p class="text-emerald-700 dark:text-emerald-400 text-sm">
                                        Receive notifications about backup status and potential issues through the admin
                                        dashboard.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Data Tables Section -->
                <section id="datatables" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Tables</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Introduction -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Obomaa includes a powerful data tables component built on top of <a
                                    href="https://tanstack.com/table/v8" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">TanStack Table</a>. These
                                tables provide a rich interactive experience with features like:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                                <li>Column sorting and filtering</li>
                                <li>Pagination with server-side support</li>
                                <li>Data export (CSV, Excel, PDF)</li>
                                <li>Responsive design for all screen sizes</li>
                                <li>Customizable columns and cell rendering</li>
                                <li>Search functionality across multiple fields</li>
                            </ul>
                        </div>

                        <!-- Implementation -->
                        <div class="space-y-8">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Implementation</h3>

                            <div class="grid gap-8">
                                <!-- Table Setup -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">1.</span> Table Configuration
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Set up your columns and table state:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(tableExampleCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-javascript">{{ tableExampleCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> Creates a table configuration with two
                                            columns (Name and Status) and sets up pagination state.</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">2.</span> Adding Action Buttons
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Add action buttons to your table:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(actionButtonsCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-javascript">{{ actionButtonsCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> Adds Edit and Delete action buttons to your
                                            table. These buttons call the handleEdit and handleDelete functions when
                                            clicked.</p>
                                    </div>
                                </div>

                                <!-- Table Component -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">3.</span> Using the Datatable Component
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Implement the datatable in your template:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(templateExampleCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code v-highlight class="language-html">{{ templateExampleCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> Renders the datatable component with your
                                            configuration and binds the necessary props and events.</p>
                                    </div>
                                </div>

                                <!-- Backend Integration -->
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5
                                        class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">4.</span> Backend Integration
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Set up your backend controller to provide paginated data:
                                    </p>

                                    <div class="bg-gray-800 rounded-lg p-4 group relative">
                                        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                            @click="navigator.clipboard.writeText(backendCode)">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <pre><code class="language-php">{{ backendCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <p><strong>What this does:</strong> The controller returns paginated data to
                                            your frontend, with eager-loaded relationships for better performance.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Activity Tracking Section -->
                <section id="auditing" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Activity Tracking</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <!-- Auditing content... -->
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Model Auditing</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Obomaa integrates <a href="https://github.com/owen-it/laravel-auditing" target="_blank"
                                    class="border-b-2 border-purple-500 dark:border-purple-400">owen-it/laravel-auditing</a>
                                package
                                for comprehensive model auditing and activity tracking. While the core auditing
                                functionality is
                                handled by the package, Obomaa provides:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4">
                                <li>User-friendly interface to view audit logs</li>
                                <li>Activity timeline visualization</li>
                                <li>Filtering and search capabilities for audits</li>
                                <li>User activity tracking dashboard</li>
                            </ul>

                            <div
                                class="mt-6 p-4 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-lg border border-yellow-400 dark:border-yellow-800/30">
                                <p class="text-sm text-yellow-800 dark:text-yellow-300 flex items-start space-x-2">
                                    <span class="flex-shrink-0 text-xl">💡</span>
                                    <span><strong>Pro Tip:</strong> Implement the <code
                                            class="bg-yellow-100 dark:bg-yellow-900/40 px-1.5 py-0.5 rounded">Auditable</code>
                                        interface on critical models to track important changes automatically.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <!-- Previous Page -->
            <a href="/documentation/getting-started"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Previous</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                        Getting Started</div>
                </div>
            </a>

            <!-- Next Page -->
            <a href="/documentation/advanced-features"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 transition-colors">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                        Advanced Features</div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</template>

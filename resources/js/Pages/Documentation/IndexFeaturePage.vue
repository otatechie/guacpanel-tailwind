<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import hljs from 'highlight.js/lib/core'
import javascript from 'highlight.js/lib/languages/javascript'
import bash from 'highlight.js/lib/languages/bash'
import 'highlight.js/styles/github-dark.css'

hljs.registerLanguage('javascript', javascript)
hljs.registerLanguage('bash', bash)

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
]

// Template usage
<Datatable
    :data="users"
    :columns="columns"
    :loading="loading"
    :pagination="pagination"
    :search-fields="['name', 'email']"
    empty-message="No users found"
    empty-description="Users will appear here"
    export-file-name="users_export"
    @update:pagination="pagination = $event"
/>`)

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
            ->with(['roles', 'permissions']) 
            ->latest()                       
            ->paginate($request->input('per_page', 10)) //Paginated results
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

const actionButtonsCode = ref(`// Add to your columns array
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
}`)

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Security Middleware', href: '#middleware' },
    { text: 'Backup System', href: '#backup-system' },
    { text: 'Data Tables', href: '#data-tables' },
    { text: 'Authentication Logs', href: '#activity-logs' },
    { text: 'Browser Sessions', href: '#browser-sessions' },
    { text: 'Activity Tracking', href: '#activity-tracking' },
    { text: 'File Uploads', href: '#file-uploads' },
    { text: 'Google Fonts', href: '#google-fonts' }
]

const fixHighlightLanguages = () => {
    document.querySelectorAll('pre code.language-vue').forEach((block) => {
        block.className = 'language-xml'
    })
}

const applyHighlighting = () => {
    nextTick(() => {
        fixHighlightLanguages()
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block)
        })
    })
}

const showBackToTop = ref(false)

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 500
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
    applyHighlighting()
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})

watch([
    routeConfigCode, featureToggleCode, magicLinkCode, authProcessCode,
    accountDisablingCode, passwordExpiryCode, forcePasswordCode,
    twoFactorCode, tableExampleCode, templateExampleCode,
    backendCode, middlewareCode, actionButtonsCode
], () => {
    applyHighlighting()
}, { deep: true })

const configExample = `{
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
}`

const scriptExample = `import { defineComponent } from 'vue'
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
        console.log('File uploaded:', file)
    }
}

const handleFileRemove = (error, file) => {
    if (!error) {
        console.log('File removed:', file)
    }
}`

const templateExample = `<file-pond 
    name="document"
    label="Upload Document"
    :label-idle="'Drop files here...'"
    :allow-multiple="false"
    :max-files="1"
    :accepted-file-types="['image/jpeg', 'image/png', 'application/pdf']"
    :max-file-size="'5MB'"
    :server="serverConfig"
    :files="[]"
    :credits="null"
    :allow-pdf-preview="true"
    :pdf-preview-height="320"
    :pdf-component-extra-params="'toolbar=0'"
    class="bg-white dark:bg-gray-800 rounded-lg"
    @processfile="(error, file) => handleFileUpload(error, file)"
    @removefile="(error, file) => handleFileRemove(error, file)"
/>`

const googleFontsConfig = `// config/google-fonts.php
'fonts' => [
    'default' => 'https://fonts.googleapis.com/css2?family=Your+Font:wght@400;500;600;700&display=swap'
]

// resources/css/app.css
@theme {
    --font-sans: "Your Font", "sans-serif";
}`

const googleFontsUsage = `php artisan google-fonts:fetch
php artisan optimize`
</script>


<template>

    <Head title="Features - GuacPanel" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
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

        <div class="flex flex-col space-y-8 mb-8">
            <ArticleNavigation :links="articleLinks" />
            <div class="space-y-16">
                <section id="authentication" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Authentication</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">
                                GuacPanel is built on <a href="https://laravel.com/docs/fortify" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">Laravel Fortify</a>,
                                providing ready-to-use authentication features including login, registration, two-factor
                                auth, password reset, and email verification. Focus on building your app while Fortify
                                handles the auth.
                            </p>
                        </div>

                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Passwordless Login</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Passwordless authentication allows users to sign in securely using email-based magic
                                links
                                instead of traditional passwords. This modern approach enhances security by eliminating
                                password-related vulnerabilities while providing a smoother user experience.
                            </p>
                        </div>

                        <div class="space-y-8">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Implementation</h3>
                            <div class="grid gap-8">
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                        <pre
                                            class="text-sm"><code class="language-php">{{ routeConfigCode }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="permissions-roles" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Permissions & Roles</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Introduction</h3>
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
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Protect your Laravel app with GuacPanel's built-in security middleware. Handle account
                                status, password policies, and two-factor authentication automatically on every request.
                            </p>
                        </div>

                        <div class="space-y-8">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Key Middleware
                                Components</h3>
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                    <pre
                                        class="text-sm"><code class="language-php">{{ accountDisablingCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div
                                        class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                        <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                            <span class="flex-shrink-0 text-xl">💡</span>
                                            <span><strong>Pro Tip:</strong> The <code
                                                    class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">disable.account</code>
                                                middleware checks if the authenticated user has been disabled. If so, it
                                                logs them out and redirects to the login page with an error
                                                message.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                    <pre
                                        class="text-sm"><code class="language-php">{{ passwordExpiryCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div
                                        class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                        <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                            <span class="flex-shrink-0 text-xl">💡</span>
                                            <span><strong>Pro Tip:</strong> The <code
                                                    class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">password.expired</code>
                                                middleware enforces password expiry (default 90 days). Customize the
                                                expiry period in the User model.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                    <pre class="text-sm"><code class="language-php">{{ forcePasswordCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div
                                        class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                        <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                            <span class="flex-shrink-0 text-xl">💡</span>
                                            <span><strong>Pro Tip:</strong> The <code
                                                    class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">force.password.change</code>
                                                middleware enforces mandatory password updates when required.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                    <pre class="text-sm"><code class="language-php">{{ twoFactorCode }}</code></pre>
                                </div>

                                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div
                                        class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                        <p class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                            <span class="flex-shrink-0 text-xl">💡</span>
                                            <span><strong>Pro Tip:</strong> The <code
                                                    class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">require.two.factor</code>
                                                middleware ensures users set up 2FA when it's required by system
                                                settings.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="backup-system" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Backup System</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Introduction</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                GuacPanel provides a clean interface for <a href="https://spatie.be/docs/laravel-backup"
                                    target="_blank" class="border-b-2 border-blue-500 dark:border-blue-400">Spatie's
                                    Laravel Backup</a>.
                                Create, download, and manage backups through the UI - no command line needed. Monitor
                                backup health and delete old backups with ease.
                            </p>
                        </div>

                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Backup UI</h3>
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

                <section id="data-tables" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Tables</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Introduction</h3>
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
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Implementation</h3>

                            <div class="grid gap-8">
                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                        <pre
                                            class="text-sm"><code class="language-javascript">{{ tableExampleCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div
                                            class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                            <p
                                                class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                                <span class="flex-shrink-0 text-xl">💡</span>
                                                <span><strong>Pro Tip:</strong> Creates a table with three columns
                                                    (Name, Email, Status), server-side pagination, and styled status
                                                    indicators. The component handles search, export, and row selection
                                                    automatically.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
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
                                        <pre
                                            class="text-sm"><code class="language-javascript">{{ actionButtonsCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div
                                            class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                            <p
                                                class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                                <span class="flex-shrink-0 text-xl">💡</span>
                                                <span><strong>Pro Tip:</strong> Add action buttons using <code
                                                        class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">columnHelper.display</code>.
                                                    Buttons use GuacPanel's built-in styles and integrate with
                                                    Inertia.js for navigation and actions.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <h5 class="flex items-center text-xl font-bold text-gray-800 dark:text-white mb-4">
                                        <span class="mr-3">3.</span> Backend Integration
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
                                        <pre class="text-sm"><code class="language-php">{{ backendCode }}</code></pre>
                                    </div>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div
                                            class="p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                            <p
                                                class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2">
                                                <span class="flex-shrink-0 text-xl">💡</span>
                                                <span><strong>Pro Tip:</strong> The controller returns paginated data to
                                                    your frontend, with eager-loaded relationships for better
                                                    performance.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="activity-logs" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Authentication Logs</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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

                <section id="browser-sessions" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Browser Session Management</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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

                <section id="activity-tracking" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Activity Tracking</h2>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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

                <section id="file-uploads" class="mb-12 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">File Uploads</h2>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div class="space-y-4">
                            <div class="prose dark:prose-invert max-w-none">
                                <p>GuacPanel uses <a href="https://pqina.nl/filepond/" target="_blank"
                                        class="border-b-2 border-blue-500 dark:border-blue-400">FilePond</a> to provide
                                    a modern file upload interface with drag-and-drop, image/PDF previews, size limits,
                                    and type validation.</p>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Configuration</h3>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre
                                        class="text-sm"><code v-highlight class="language-js">{{ configExample }}</code></pre>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Script Setup</h3>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre
                                        class="text-sm"><code v-highlight class="language-js">{{ scriptExample }}</code></pre>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Template</h3>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre
                                        class="text-sm"><code v-highlight class="language-html">{{ templateExample }}</code></pre>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Events</h3>
                                <ul
                                    class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 ml-4 text-sm">
                                    <li><code
                                            class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">@processfile</code>
                                        - Emitted when a file is uploaded</li>
                                    <li><code
                                            class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">@removefile</code>
                                        - Emitted when a file is removed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="google-fonts" class="mb-12 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Google Fonts</h2>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <div class="prose dark:prose-invert max-w-none">
                            <p>GuacPanel uses <a href="https://github.com/spatie/laravel-google-fonts" target="_blank"
                                    class="border-b-2 border-blue-500 dark:border-blue-400">Spatie's Laravel Google
                                    Fonts</a> to provide self-hosted fonts with local caching for better performance.
                            </p>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Configuration</h3>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre
                                        class="text-sm"><code v-highlight class="language-php">{{ googleFontsConfig }}</code></pre>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Usage</h3>
                                <div class="bg-gray-800 rounded-lg p-4">
                                    <pre
                                        class="text-sm"><code v-highlight class="language-bash">{{ googleFontsUsage }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <a href="/documentation/installation"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Previous</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">
                        Installation</div>
                </div>
            </a>

            <a href="/documentation/components"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">
                        Components</div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
</template>

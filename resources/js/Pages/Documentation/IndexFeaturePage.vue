<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@/Components/CodeBlock.vue'

defineOptions({
    layout: Public
})

const articleLinks = [
    { text: 'Authentication', href: '#authentication' },
    { text: 'Interface & Layout', href: '#interface' },
    { text: 'Data Management', href: '#data-management' },
    { text: 'Technologies', href: '#technologies' }
]
</script>

<template>

    <Head title="Features - Obomaa" />

    <div class="max-w-7xl mx-auto">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800">
            <div class="relative z-10 p-8 md:p-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">Feature Overview</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl">
                    Explore the comprehensive set of features that Obomaa provides to help you build powerful admin
                    interfaces quickly and efficiently.
                </p>
            </div>
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 h-64 w-64 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-800/20 dark:to-indigo-800/20 blur-3xl rounded-full opacity-70">
            </div>
        </div>

        <div class="space-y-12">
            <!-- Authentication Section -->
            <section id="authentication" class="space-y-6 mb-12 scroll-mt-16">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Authentication</h2>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                    <!-- Introduction -->
                    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-8">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Introduction</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Laravel Fortify automatically scaffolds the login, two-factor login, registration, password
                            reset, and email verification features for your project, allowing you to start building the
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
                            Passwordless authentication allows users to sign in securely using email-based magic links
                            instead of traditional passwords. This modern approach enhances security by eliminating
                            password-related vulnerabilities while providing a smoother user experience.
                        </p>

                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                <span class="font-medium">💡 Pro Tip:</span> Passwordless login is perfect for
                                non-technical users - no more forgotten passwords or security concerns!
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
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">1</span>
                                    Route Configuration
                                </h5>
                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        onclick="navigator.clipboard.writeText('// Magic Link Authentication Routes\nRoute::middleware([\'guest\', \'web\'])->group(function () {\n    Route::controller(MagicLinkController::class)->group(function () {\n        Route::get(\'/magic-link/register\', \'create\')->name(\'magic.register.create\');\n        Route::post(\'/register/magic-link\', \'register\')->name(\'magic.register\');\n        Route::post(\'/login/magic-link\', \'login\')->name(\'magic.login.request\');\n        Route::get(\'/magic-link/{token}\', \'authenticate\')->name(\'magic.login\');\n    });\n});')">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre
                                        class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-gray-500">// Magic Link Authentication Routes</span>
<span class="text-purple-400">Route</span>::middleware([<span class="text-orange-400">'guest'</span>, <span class="text-orange-400">'web'</span>])->group(<span class="text-purple-400">function</span> () {
    <span class="text-purple-400">Route</span>::controller(MagicLinkController::<span class="text-purple-400">class</span>)->group(<span class="text-purple-400">function</span> () {
        <span class="text-purple-400">Route</span>::get(<span class="text-orange-400">'/magic-link/register'</span>, <span class="text-orange-400">'create'</span>)->name(<span class="text-orange-400">'magic.register.create'</span>);
        <span class="text-purple-400">Route</span>::post(<span class="text-orange-400">'/register/magic-link'</span>, <span class="text-orange-400">'register'</span>)->name(<span class="text-orange-400">'magic.register'</span>);
        <span class="text-purple-400">Route</span>::post(<span class="text-orange-400">'/login/magic-link'</span>, <span class="text-orange-400">'login'</span>)->name(<span class="text-orange-400">'magic.login.request'</span>);
        <span class="text-purple-400">Route</span>::get(<span class="text-orange-400">'/magic-link/{token}'</span>, <span class="text-orange-400">'authenticate'</span>)->name(<span class="text-orange-400">'magic.login'</span>);
    });
});</pre>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Here's what each route does:
                                    </p>
                                    <ul
                                        class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-4">
                                        <li><code
                                                class="text-purple-600 dark:text-purple-400">GET /magic-link/register</code>
                                            - Shows the registration form</li>
                                        <li><code
                                                class="text-purple-600 dark:text-purple-400">POST /register/magic-link</code>
                                            - Creates new user accounts</li>
                                        <li><code
                                                class="text-purple-600 dark:text-purple-400">POST /login/magic-link</code>
                                            - Sends the login link</li>
                                        <li><code
                                                class="text-purple-600 dark:text-purple-400">GET /magic-link/{token}</code>
                                            - Handles the actual login</li>
                                    </ul>

                                    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                            <span class="font-medium">💡 For Developers:</span> Passwordless
                                            authentication can be removed in favor of traditional password-based login,
                                            but is enabled by default for better user experience.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Toggle -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">2</span>
                                    Feature Toggle
                                </h5>
                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        onclick="navigator.clipboard.writeText('protected function checkPasswordlessEnabled()\n{\n    $passwordlessEnabled = DB::table(\'settings\')->value(\'passwordless_login\') ?? true;\n    if (!$passwordlessEnabled) {\n        abort(404);\n    }\n}')">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre
                                        class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">protected function</span> checkPasswordlessEnabled()
{
    <span class="text-blue-400">$passwordlessEnabled</span> = <span class="text-green-400">DB</span>::table(<span class="text-orange-400">'settings'</span>)->value(<span class="text-orange-400">'passwordless_login'</span>) ?? true;
    <span class="text-purple-400">if</span> (!<span class="text-blue-400">$passwordlessEnabled</span>) {
        abort(<span class="text-cyan-400">404</span>);
    }
}</pre>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Checks if passwordless login is enabled in the settings table. Defaults to
                                        enabled if no setting exists.
                                    </p>
                                </div>
                            </div>

                            <!-- Magic Link Generation -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">3</span>
                                    Magic Link Generation
                                </h5>
                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        onclick="navigator.clipboard.writeText('protected function sendLoginLink(User $user)\n{\n    $url = URL::temporarySignedRoute(\n        \'magic.login\',\n        now()->addMinutes(10),\n        [\'token\' => $user->id]\n    );\n\n    Mail::to($user)->send(new MagicLoginLink($url));\n}')">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre
                                        class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">protected function</span> sendLoginLink(User $user)
{
    <span class="text-blue-400">$url</span> = <span class="text-green-400">URL</span>::temporarySignedRoute(
        <span class="text-orange-400">'magic.login'</span>,
        now()->addMinutes(<span class="text-cyan-400">10</span>),
        [<span class="text-orange-400">'token'</span> => <span class="text-blue-400">$user</span>->id]
    );

    <span class="text-green-400">Mail</span>::to(<span class="text-blue-400">$user</span>)->send(<span class="text-purple-400">new</span> MagicLoginLink(<span class="text-blue-400">$url</span>));
}</pre>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Creates a secure, time-limited URL (10 minutes) and sends it via email to the
                                        user.
                                    </p>
                                </div>
                            </div>

                            <!-- Authentication Process -->
                            <div
                                class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">4</span>
                                    Authentication Process
                                </h5>
                                <div class="bg-gray-800 rounded-lg p-4 group relative">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        onclick="navigator.clipboard.writeText('public function authenticate(Request $request)\n{\n    if (!$request->hasValidSignature()) {\n        return redirect()->route(\'login\')\n            ->with(\'error\', \'Magic link expired.\');\n    }\n\n    $user = User::findOrFail($request->token);\n    auth()->guard(\'web\')->login($user);\n    $request->session()->regenerate();\n\n    return redirect()->intended(config(\'fortify.home\'));\n}')">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre
                                        class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">public function</span> authenticate(Request $request)
{
    <span class="text-purple-400">if</span> (!<span class="text-blue-400">$request</span>->hasValidSignature()) {
        <span class="text-purple-400">return</span> redirect()->route(<span class="text-orange-400">'login'</span>)
            ->with(<span class="text-orange-400">'error'</span>, <span class="text-orange-400">'Magic link expired.'</span>);
    }

    <span class="text-blue-400">$user</span> = User::findOrFail(<span class="text-blue-400">$request</span>->token);
    auth()->guard(<span class="text-orange-400">'web'</span>)->login(<span class="text-blue-400">$user</span>);
    <span class="text-blue-400">$request</span>->session()->regenerate();

    <span class="text-purple-400">return</span> redirect()->intended(config(<span class="text-orange-400">'fortify.home'</span>));
}</pre>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Validates the magic link, logs in the user, and redirects to the dashboard.
                                        Includes security measures like session regeneration.
                                    </p>
                                </div>
                            </div>

                            <!-- Security Middleware -->
                            <div class="space-y-8">
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Security Middleware</h3>
                                <div class="grid gap-8">
                                    <!-- Account Disabling -->
                                    <div class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">1</span>
                                            Account Disabling
                                        </h5>
                                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                                onclick="navigator.clipboard.writeText('public function handle(Request $request, Closure $next): Response\n{\n    if (auth()->check() && auth()->user()->is_disabled) {\n        auth()->logout();\n        session()->flash(\'error\', \'Your account has been disabled.\');\n        return redirect()->route(\'login\');\n    }\n\n    return $next($request);\n}')">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <pre class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">public function</span> handle(Request $request, Closure $next): Response
{
    <span class="text-purple-400">if</span> (auth()->check() && auth()->user()->is_disabled) {
        auth()->logout();
        session()->flash(<span class="text-orange-400">'error'</span>, <span class="text-orange-400">'Your account has been disabled.'</span>);
        <span class="text-purple-400">return</span> redirect()->route(<span class="text-orange-400">'login'</span>);
    }

    <span class="text-purple-400">return</span> $next($request);
}</pre>
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Checks if the authenticated user's account is disabled. If so, logs them out and redirects to the login page.
                                            </p>
                                        </div>
                                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">💡 Pro Tip:</span> Use this feature to temporarily suspend accounts instead of deleting them, preserving user data while preventing access. Add <code class="bg-yellow-100 dark:bg-yellow-900/40 px-1.5 py-0.5 rounded">check.disabled</code> middleware to your protected routes.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Password Expiry -->
                                    <div class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">2</span>
                                            Password Expiry Check
                                        </h5>
                                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                                onclick="navigator.clipboard.writeText('public function handle(Request $request, Closure $next): Response\n{\n    if (auth()->check()) {\n        $user = auth()->user();\n        $settings = Setting::first();\n\n        if ($settings && $settings->password_expiry && $user->isPasswordExpired()) {\n            session()->flash(\'warning\', \'Your password has expired. Please change it to continue.\');\n            return redirect()->route(\'user.password.expired\');\n        }\n    }\n\n    return $next($request);\n}')">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <pre class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">public function</span> handle(Request $request, Closure $next): Response
{
    <span class="text-purple-400">if</span> (auth()->check()) {
        <span class="text-blue-400">$user</span> = auth()->user();
        <span class="text-blue-400">$settings</span> = Setting::first();

        <span class="text-purple-400">if</span> (<span class="text-blue-400">$settings</span> && <span class="text-blue-400">$settings</span>->password_expiry && <span class="text-blue-400">$user</span>->isPasswordExpired()) {
            session()->flash(<span class="text-orange-400">'warning'</span>, <span class="text-orange-400">'Your password has expired. Please change it to continue.'</span>);
            <span class="text-purple-400">return</span> redirect()->route(<span class="text-orange-400">'user.password.expired'</span>);
        }
    }

    <span class="text-purple-400">return</span> $next($request);
}</pre>
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Checks if the user's password has expired based on system settings and redirects to the password change page if necessary.
                                            </p>
                                        </div>
                                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">💡 Pro Tip:</span> Configure password expiry intervals in your settings to match your organization's security policies. Protect routes with <code class="bg-yellow-100 dark:bg-yellow-900/40 px-1.5 py-0.5 rounded">password.expiry</code> middleware.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Force Password Change -->
                                    <div class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">3</span>
                                            Force Password Change
                                        </h5>
                                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                                onclick="navigator.clipboard.writeText('public function handle(Request $request, Closure $next): Response\n{\n    if (auth()->check() && auth()->user()->force_password_change) {\n        session()->flash(\'warning\', \'You must change your password before continuing.\');\n        return redirect()->route(\'user.force.password.change\');\n    }\n\n    return $next($request);\n}')">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <pre class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">public function</span> handle(Request $request, Closure $next): Response
{
    <span class="text-purple-400">if</span> (auth()->check() && auth()->user()->force_password_change) {
        session()->flash(<span class="text-orange-400">'warning'</span>, <span class="text-orange-400">'You must change your password before continuing.'</span>);
        <span class="text-purple-400">return</span> redirect()->route(<span class="text-orange-400">'user.force.password.change'</span>);
    }

    <span class="text-purple-400">return</span> $next($request);
}</pre>
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Forces users to change their password when administrators have flagged their account for a required password change.
                                            </p>
                                        </div>
                                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">💡 Pro Tip:</span> Use this when security might be compromised or when enforcing new password policies. Apply <code class="bg-yellow-100 dark:bg-yellow-900/40 px-1.5 py-0.5 rounded">force.password.change</code> middleware to secure routes.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Two Factor Authentication -->
                                    <div class="p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                        <h5 class="flex items-center text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-600 dark:text-purple-400 mr-3">4</span>
                                            Two Factor Authentication
                                        </h5>
                                        <div class="bg-gray-800 rounded-lg p-4 group relative">
                                            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                                onclick="navigator.clipboard.writeText('public function handle(Request $request, Closure $next): Response\n{\n    if (!auth()->check()) {\n        return $next($request);\n    }\n\n    $settings = Setting::first();\n    if (!$settings || !$settings->two_factor_authentication) {\n        return $next($request);\n    }\n\n    $user = auth()->user();\n    if (!$user->two_factor_secret) {\n        session()->flash(\'warning\', \'Two-factor authentication is required. Please enable it to continue.\');\n        return redirect()->route(\'user.two.factor\');\n    }\n\n    return $next($request);\n}')">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <pre class="text-yellow-400 text-sm overflow-x-auto whitespace-pre-wrap break-words max-w-full"><span class="text-purple-400">public function</span> handle(Request $request, Closure $next): Response
{
    <span class="text-purple-400">if</span> (!auth()->check()) {
        <span class="text-purple-400">return</span> $next($request);
    }

    <span class="text-blue-400">$settings</span> = Setting::first();
    <span class="text-purple-400">if</span> (!<span class="text-blue-400">$settings</span> || !<span class="text-blue-400">$settings</span>->two_factor_authentication) {
        <span class="text-purple-400">return</span> $next($request);
    }

    <span class="text-blue-400">$user</span> = auth()->user();
    <span class="text-purple-400">if</span> (!<span class="text-blue-400">$user</span>->two_factor_secret) {
        session()->flash(<span class="text-orange-400">'warning'</span>, <span class="text-orange-400">'Two-factor authentication is required. Please enable it to continue.'</span>);
        <span class="text-purple-400">return</span> redirect()->route(<span class="text-orange-400">'user.two.factor'</span>);
    }

    <span class="text-purple-400">return</span> $next($request);
}</pre>
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Enforces two-factor authentication when enabled in system settings, redirecting users to set it up if they haven't already.
                                            </p>
                                        </div>
                                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">💡 Pro Tip:</span> Consider making 2FA mandatory for admin users while keeping it optional for regular users. Use <code class="bg-yellow-100 dark:bg-yellow-900/40 px-1.5 py-0.5 rounded">require.2fa</code> middleware for sensitive routes.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <ArticleNavigation :links="articleLinks" />
</template>

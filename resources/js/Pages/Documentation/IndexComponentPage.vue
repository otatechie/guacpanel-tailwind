<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue'
import Public from '@/Layouts/Public.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import hljs from 'highlight.js'
import 'highlight.js/lib/languages/php'
import 'highlight.js/lib/languages/javascript'
import 'highlight.js/styles/night-owl.css'
import FormInput from '@/Components/FormInput.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import FormSelect from '@/Components/FormSelect.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Switch from '@/Components/Switch.vue'
import FlashMessage from '@/Components/FlashMessage.vue'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'
import Logo from '@/Components/Logo.vue'

defineOptions({
    layout: Public
})

const articleLinks = [
    {
        text: 'Form Components', href: '#form-components', children: [
            { text: 'Form Input', href: '#form-input' },
            { text: 'Form Checkbox', href: '#form-checkbox' },
            { text: 'Form Select', href: '#form-select' }
        ]
    },
    {
        text: 'Navigation Component', href: '#navigation-components', children: [
            { text: 'Navigation Sidebar', href: '#nav-sidebar' }
        ]
    },
    {
        text: 'Feedback Components', href: '#feedback-components', children: [
            { text: 'Flash Message', href: '#flash-message' },
            { text: 'Modal', href: '#modal' }
        ]
    },
    {
        text: 'Utility Components', href: '#utility-components', children: [
            { text: 'Switch', href: '#switch' },
            { text: 'Logo', href: '#logo' },
            { text: 'Page Header', href: '#page-header' }
        ]
    }
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

const demoEmail = ref('')
const demoIsChecked = ref(false)
const demoIsOpen = ref(false)
const demoIsEnabled = ref(false)
const demoSelectedCountry = ref('')
const demoCountries = ref([
    { name: 'United States', code: 'US' },
    { name: 'Canada', code: 'CA' },
    { name: 'United Kingdom', code: 'GB' }
])

const formInputCode = ref(`<FormInput
    v-model="email"
    label="Email Address"
    id="demo-email"
    type="email"
    required
    error="Please enter a valid email address"
/>`)

const formCheckboxCode = ref(`<FormCheckbox
    v-model="selectedItems"
    :value="item.id"
    :label="item.name"
    :name="remember"
    :id="remember"
/>`)

const modalCode = ref(`<Modal :show="isOpen" @close="isOpen = false" size="md">
    <template #title>Edit Profile</template>

    <div class="space-y-4">
        <!-- Modal content here -->
    </div>

    <template #footer>
        <button @click="save">Save Changes</button>
    </template>
</Modal>`)

const pageHeaderCode = ref(`<PageHeader
    title="User Management"
    description="Manage user accounts and permissions"
    :breadcrumbs="[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Users' }
    ]"
>
    <template #actions>
        <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            Add User
        </button>
    </template>
</PageHeader>`)

const switchCode = ref(`<Switch
    v-model="isEnabled"
    label="Enable notifications"
/>`)

const formSelectCode = ref(`<FormSelect
    v-model="selectedCountry"
    label="Country"
    id="demo-country"
    :options="demoCountries"
    option-label="name"
    option-value="code"
    required
/>`)

const flashMessageCode = ref(`<FlashMessage
    type="success"
    message="Your changes have been saved successfully!"
    :duration="3000"
/>`)

const flashMessageUsageCode = ref(`// In your Laravel controller
public function store(Request $request)
{
    // Process the request...
    
    // Flash a success message
    return redirect()->back()->with('success', 'Item created successfully!');
    
    // Or flash an error message
    return redirect()->back()->with('error', 'Failed to create item.');
    
    // Or flash a warning message
    return redirect()->back()->with('warning', 'Please review your changes.');
    
    // Or flash an info message
    return redirect()->back()->with('info', 'Your changes have been saved.');
}`)

const navSidebarCode = ref(`<NavSidebarDesktop />`)

const navSidebarCodeUsage = ref(`// Add items to navigation sections
const navigationSections = reactive([
    {
        items: [
            {
                name: 'Dashboard',
                href: route('home'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955..." />',
                permission: 'dashboard-view' // Optional: Permission required to see this item
            },
            { type: 'divider' }, // Visual separator between navigation items
        ]
    },
    {
        items: [
            {
                name: 'Settings',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94..." />',
                children: [
                    { name: 'Users', href: route('admin.user.index'), permission: 'users-view' },
                    // More child items...
                ]
            },
        ]
    }
])`)

const logoCode = ref(`<Logo size="3.5rem" />`)
</script>


<template>

    <Head title="Components - GuacPanel" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-teal-600 to-teal-700 dark:from-teal-900 dark:to-teal-800">
            <div class="relative z-10 p-8 md:p-12">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Component Library</h1>
                </div>
                <p class="text-lg text-teal-100 dark:text-teal-200 max-w-3xl mb-8">
                    Explore our collection of beautiful, accessible, and reusable components built with Vue 3 and
                    Tailwind CSS.
                    Each component is designed to be flexible, customizable, and easy to integrate into your projects.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#form-components"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-teal-600 hover:bg-teal-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Form Components
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
                <section id="form-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Form Components</h2>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Form Input</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A flexible input component with floating label support, error handling, and password toggle
                            functionality.
                            Provides comprehensive error handling with visual feedback and error messages.
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <FormInput v-model="demoEmail" label="Email Address" id="demo-email" type="email"
                                    required error="Please enter a valid email address" />
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(formInputCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ formInputCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Error Handling</h4>
                            <div class="prose prose-teal dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-400">
                                    The FormInput component provides comprehensive error handling through the following
                                    features:
                                </p>
                                <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2 mt-4">
                                    <li>Visual feedback with red border and error icon when errors are present</li>
                                    <li>Error message display below the input field</li>
                                    <li>ARIA attributes for accessibility (aria-invalid, aria-describedby)</li>
                                    <li>Integration with Laravel validation</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">modelValue</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String,
                                                Number</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">''</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The input
                                                value (v-model binding)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">label</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Label text
                                                for the input field</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">type</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'text'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Input type
                                                (text, email, password, number, etc.)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">error</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String, Array
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Error
                                                message(s) to display</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">id</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Input element
                                                ID (auto-generated if not provided)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">name</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Input name
                                                attribute</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">placeholder</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Input
                                                placeholder text</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">required</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Whether the
                                                input is required</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">disabled</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Whether the
                                                input is disabled</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">autocomplete
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'off'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Input
                                                autocomplete attribute</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">autofocus</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Whether the
                                                input should be focused on mount</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">readonly</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Whether the
                                                input is read-only</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">help</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Help text to
                                                display below the input</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Form Checkbox</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A customizable checkbox component that supports both single boolean values and arrays for
                            multiple selections.
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <FormCheckbox v-model="demoIsChecked" label="I agree to the terms and conditions"
                                    name="terms" id="terms" />
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(formCheckboxCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ formCheckboxCode }}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Form Select</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A searchable select component with support for custom option formatting and keyboard
                            navigation.
                        </p>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <FormSelect v-model="demoSelectedCountry" label="Country" id="demo-country"
                                    :options="demoCountries" option-label="name" option-value="code" required />
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(formSelectCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ formSelectCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">modelValue</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Any</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The selected
                                                value</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">options</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">[]</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array of
                                                options to display</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">optionLabel</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'label'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Property name
                                                for option label</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">optionValue</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'value'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Property name
                                                for option value</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </section>

                <section id="navigation-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Navigation Components</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Navigation components help users move through your application efficiently. These components
                        provide intuitive
                        interfaces for menus, sidebars, and other navigation structures.
                    </p>

                    <div id="nav-sidebar"
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Navigation Sidebar</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A versatile sidebar navigation component that supports multiple levels of navigation,
                            permissions,
                            dropdown menus, and dividers. Built with accessibility in mind and responds to the user's
                            permissions.
                        </p>

                        <div class="grid md:grid-cols-1 gap-8">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Usage</h4>
                                <div class="bg-gray-800 rounded-lg p-4 relative group">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(navSidebarCode)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre class="text-sm"><code class="language-vue">{{ navSidebarCode }}</code></pre>
                                </div>

                                <div class="bg-gray-800 rounded-lg p-4 relative group mt-6">
                                    <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                        @click="navigator.clipboard.writeText(navSidebarCodeUsage)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <pre
                                        class="text-sm"><code class="language-javascript">{{ navSidebarCodeUsage }}</code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Key Features</h4>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                <li>Support for nested navigation with dropdowns</li>
                                <li>Permission-based visibility for menu items</li>
                                <li>Visual indicators for active and parent-active states</li>
                                <li>Divider support for visual separation of menu sections</li>
                                <li>Dark mode compatible</li>
                                <li>Accessible keyboard navigation</li>
                            </ul>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Navigation Structure</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                The navigation sidebar uses a structured configuration to define menu items. The
                                structure follows these principles:
                            </p>

                            <div class="mb-6">
                                <h5 class="text-sm font-medium text-gray-800 dark:text-white mb-2">Navigation Sections
                                </h5>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <pre class="text-sm"><code class="language-javascript">const navigationSections = reactive([
  {
    items: [ /* First section items */ ]
  },
  {
    items: [ /* Second section items */ ]
  }
])</code></pre>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mt-2">
                                    Navigation is organized into sections, each containing an array of items. Sections
                                    are visually separated in the UI.
                                </p>
                            </div>

                            <div class="space-y-6">
                                <h5 class="text-sm font-medium text-gray-800 dark:text-white mb-2">Item Types</h5>

                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <h6 class="text-xs font-medium text-gray-800 dark:text-white mb-2">Regular Item</h6>
                                    <pre class="text-sm"><code class="language-javascript">{
  name: 'Dashboard',
  href: route('home'),
  icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955..." />',
  permission: 'dashboard-view' // Optional: Permission required to see this item
},
{ type: 'divider' } // Visual separator between navigation items</code></pre>
                                </div>

                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <h6 class="text-xs font-medium text-gray-800 dark:text-white mb-2">Dropdown Menu
                                    </h6>
                                    <pre class="text-sm"><code class="language-javascript">{
  name: 'Settings',
  icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94..." />',
  isOpen: false, // Initial state of dropdown (closed)
  children: [
    { 
      name: 'General', 
      href: route('admin.setting.index'), 
      permission: 'settings-general-view'  // Child permissions
    },
    { 
      name: 'Users', 
      href: route('admin.user.index'), 
      permission: 'users-view' 
    }
  ]
}</code></pre>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h5 class="text-sm font-medium text-gray-800 dark:text-white mb-2">Icons</h5>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Icons use inline SVG paths from <a href="https://heroicons.com/" target="_blank"
                                        class="text-teal-600 hover:text-teal-500 dark:text-teal-400 dark:hover:text-teal-300">Heroicons</a>.
                                    Only include the path data, not the entire SVG element.
                                </p>
                            </div>

                            <div class="mt-6">
                                <h5 class="text-sm font-medium text-gray-800 dark:text-white mb-2">Permissions</h5>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Add the <code>permission</code> property to any item to restrict visibility based on
                                    user permissions. If omitted, the item will be visible to all users.
                                </p>

                                <div
                                    class="mt-4 p-4 bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 rounded-lg border border-teal-400 dark:border-teal-800/30">
                                    <p
                                        class="text-sm text-teal-800 dark:text-teal-300 flex items-start space-x-2 leading-6">
                                        <span class="flex-shrink-0 text-xl">💡</span>
                                        <span><strong>Pro Tip:</strong> GuacPanel leverages <a
                                                href="https://spatie.be/docs/laravel-permission" target="_blank"
                                                class="border-b-2 border-blue-500 dark:border-teal-400">Spatie's
                                                Laravel-Permission</a> package for permission management. Use consistent
                                            permission naming like <code
                                                class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">settings-general-view</code>,
                                            <code
                                                class="bg-teal-100 dark:bg-teal-900/40 px-1.5 py-0.5 rounded">users-view</code>
                                            that directly map to your application's permission system.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="feedback-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Feedback Components</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Feedback components provide visual and interactive feedback to users about their actions and the
                        application's state.
                        These components are essential for creating a responsive and user-friendly interface.
                    </p>
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flash Message</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A versatile toast notification component for displaying feedback messages. Supports multiple
                            message types, customizable duration, positioning, and automatic stacking. Built with
                            accessibility in mind
                            and seamlessly integrates with Laravel's session flash messages.
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <FlashMessage type="success" message="Your changes have been saved successfully!"
                                    :duration="3000" />
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(flashMessageCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ flashMessageCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">type</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'success'
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Message type
                                                (success, error, warning, info)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">message</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">''</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The message
                                                to display</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">duration</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Number</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">10000</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Duration in
                                                milliseconds before auto-dismiss</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Laravel Integration</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                The FlashMessage component automatically handles Laravel's session flash messages. It
                                supports the following flash keys:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                <li><code>success</code> - Success messages</li>
                                <li><code>error</code> or <code>danger</code> - Error messages</li>
                                <li><code>warning</code> - Warning messages</li>
                                <li><code>info</code> - Information messages</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Modal</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A flexible modal dialog component built with Headless UI for accessibility and smooth
                            transitions.
                            Supports multiple sizes, custom headers, and footer actions.
                        </p>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <div class="space-y-4">
                                    <button @click="demoIsOpen = true"
                                        class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500">
                                        Open Modal
                                    </button>

                                    <Modal :show="demoIsOpen" @close="demoIsOpen = false" size="md">
                                        <template #title>Edit Profile</template>
                                        <div class="p-6">
                                            <p>Modal content goes here</p>
                                        </div>
                                        <template #footer>
                                            <div class="flex justify-end space-x-3">
                                                <button @click="demoIsOpen = false"
                                                    class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                                    Cancel
                                                </button>
                                                <button
                                                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500">
                                                    Save Changes
                                                </button>
                                            </div>
                                        </template>
                                    </Modal>
                                </div>
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(modalCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ modalCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">show</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Controls
                                                modal visibility
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">size</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'md'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Modal size
                                                (sm, md, lg, xl)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">closeable</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">true</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Allow closing
                                                with escape key
                                                and overlay click</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="utility-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Utility Components</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Utility components are reusable UI elements that provide common functionality across your
                        application.
                        These components are designed to be flexible, accessible, and easy to integrate into your
                        projects.
                    </p>
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Page Header</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A consistent page header component that includes a title, optional description, breadcrumbs navigation,
                            and a flexible actions slot. Perfect for creating uniform page layouts across your application.
                        </p>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <PageHeader
                                    title="User Management"
                                    description="Manage user accounts and permissions"
                                    :breadcrumbs="[
                                        { label: 'Dashboard', href: '/dashboard' },
                                        { label: 'Users' }
                                    ]"
                                >
                                    <template #actions>
                                        <button class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500">
                                            Add User
                                        </button>
                                    </template>
                                </PageHeader>
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(pageHeaderCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ pageHeaderCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">title</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Required</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The main heading for the page</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">description</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">''</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Optional description text below the title</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">breadcrumbs</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">[]</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array of breadcrumb objects with label and optional href properties</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Slots</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">actions</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Content for the actions area in the header (typically buttons or other controls)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Features</h4>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                <li>Consistent page header styling across your application</li>
                                <li>Built-in breadcrumb navigation with optional links</li>
                                <li>Flexible actions slot for page-specific controls</li>
                                <li>Dark mode support</li>
                                <li>Responsive design</li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Switch</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A toggle switch component for boolean values with support for labels and disabled states.
                            Built with accessibility in mind using Headless UI.
                        </p>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <Switch v-model="demoIsEnabled" label="Enable notifications" />
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4 relative group">
                                <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-300"
                                    @click="navigator.clipboard.writeText(switchCode)">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <pre class="text-sm"><code class="language-vue">{{ switchCode }}</code></pre>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">modelValue</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The switch
                                                value</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">label</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">null</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Label text
                                                for the switch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">disabled</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Boolean</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">false</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Whether the
                                                switch is
                                                disabled</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Logo</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A flexible logo component that displays either a custom logo image or a fallback text.
                            Supports dynamic sizing and error handling.
                        </p>
                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Default</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">size</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">'2.5rem'</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Controls the
                                                maximum height
                                                of the logo. Can be set to any valid CSS size value (e.g., '2.5rem',
                                                '40px', '3rem')
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Features</h4>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                <li>Dynamic logo sizing through props</li>
                                <li>Fallback to text if logo image fails to load</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <a href="/documentation/features"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Previous</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">
                        Features</div>
                </div>
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

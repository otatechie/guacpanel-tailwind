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

defineOptions({
    layout: Public
})
const validationCode = ref(`FormInput
    v-model="form.email"
    label="Email Address"
    :error="errors.email"
    required;

// In your component:
const form = useForm({
    email: ''
})

// Errors from Laravel will automatically be displayed
form.post(route('user.update'));`)

const articleLinks = [
    {
        text: 'Form Components', href: '#form-components', children: [
            { text: 'Form Input', href: '#form-input' },
            { text: 'Form Checkbox', href: '#form-checkbox' },
            { text: 'Form Select', href: '#form-select' }
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
            { text: 'Switch', href: '#switch' }
        ]
    }
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

// Demo data for examples
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
    title="Users"
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

</script>

<template>

    <Head title="Components - OboDash" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-purple-600 to-indigo-600 dark:from-purple-900 dark:to-indigo-900">
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
                <p class="text-lg text-purple-100 dark:text-purple-200 max-w-3xl mb-8">
                    Explore our collection of beautiful, accessible, and reusable components built with Vue 3 and
                    Tailwind CSS.
                    Each component is designed to be flexible, customizable, and easy to integrate into your projects.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#form-components"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-white text-purple-600 hover:bg-purple-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Form Components
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
                <!-- Form Components Section -->
                <section id="form-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Form Components</h2>
                    </div>

                    <!-- Form Input -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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
                            <div class="prose prose-purple dark:prose-invert max-w-none">
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

                                <div class="mt-6 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Example with
                                        Laravel Validation</h5>
                                    <pre class="text-sm"><code class="language-php">{{ validationCode }}</code></pre>
                                </div>

                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Props</h4>
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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

                    <!-- Form Checkbox -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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

                    <!-- Form Select -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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

                <!-- Feedback Components Section -->
                <section id="feedback-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Feedback Components</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Feedback components provide visual and interactive feedback to users about their actions and the application's state.
                        These components are essential for creating a responsive and user-friendly interface.
                    </p>
                    <!-- Flash Message Component -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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
                                The FlashMessage component automatically handles Laravel's session flash messages. It supports the following flash keys:
                            </p>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-2">
                                <li><code>success</code> - Success messages</li>
                                <li><code>error</code> or <code>danger</code> - Error messages</li>
                                <li><code>warning</code> - Warning messages</li>
                                <li><code>info</code> - Information messages</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Modal Component -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
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
                                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
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
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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

                <!-- Utility Components Section -->
                <section id="utility-components" class="space-y-6 scroll-mt-16">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
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
                        Utility components are reusable UI elements that provide common functionality across your application.
                        These components are designed to be flexible, accessible, and easy to integrate into your projects.
                    </p>
                    <!-- Switch Component -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
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
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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

                    <!-- Page Header Component -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Page Header</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A flexible page header component that displays a title, description, and optional breadcrumbs.
                            Includes a slot for action buttons and supports dark mode.
                        </p>

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800 dark:text-white">Example</h4>
                                <PageHeader
                                    title="Users"
                                    description="Manage user accounts and permissions"
                                    :breadcrumbs="[
                                        { label: 'Dashboard', href: '/dashboard' },
                                        { label: 'Users' }
                                    ]"
                                >
                                    <template #actions>
                                        <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                            Add
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
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
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
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">title</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Required</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">The main title of the page</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">description</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">String</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">''</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Optional description text</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">breadcrumbs</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">[]</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Array of breadcrumb items with label and optional href</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Slots</h4>
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Description</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">actions</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Slot for action buttons or controls</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
            <a href="/documentation/features"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Previous</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                        Features</div>
                </div>
            </a>
        </div>
    </div>

    <button v-show="showBackToTop" @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-purple-600 text-white p-3 rounded-full shadow-lg hover:bg-purple-500 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
        aria-label="Back to top">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</template>

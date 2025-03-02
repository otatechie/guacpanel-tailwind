<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, nextTick, watch } from 'vue'
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
    { text: 'Form Components', href: '#form-components', children: [
        { text: 'Form Input', href: '#form-input' },
        { text: 'Form Checkbox', href: '#form-checkbox' },
        { text: 'Form Select', href: '#form-select' }
    ]},
    { text: 'Feedback Components', href: '#feedback-components', children: [
        { text: 'Flash Message', href: '#flash-message' },
        { text: 'Modal', href: '#modal' }
    ]},
    { text: 'Utility Components', href: '#utility-components', children: [
        { text: 'Switch', href: '#switch' }
    ]}
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
    // Initialize highlight.js
    applyHighlighting()
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
    title="User Management"
    description="Manage user accounts and permissions"
    :breadcrumbs="[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Users' }
    ]"
>
    <template #actions>
        <button>Add User</button>
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


</script>

<template>

    <Head title="Components - Obomaa" />

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
                    <a href="#component-api"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-purple-500 text-white hover:bg-purple-400 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        Component API
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
                                <pre><code class="language-vue">{{ formInputCode }}</code></pre>
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
                                    <pre><code class="language-php">{{ validationCode }}</code></pre>
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
                                <pre><code class="language-vue">{{ formCheckboxCode }}</code></pre>
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
                                <pre><code class="language-vue">{{ formSelectCode }}</code></pre>
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
                    <!-- Flash Message Component -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flash Message</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            A versatile toast notification component for displaying feedback messages. Supports multiple
                            message types,
                            customizable duration, positioning, and automatic stacking. Built with accessibility in mind
                            and seamlessly
                            integrates with Laravel's session flash messages.
                        </p>



                        <div class="mt-8">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-4">Message Types</h4>
                            <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Key</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Usage</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Style</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Success</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">success</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Successful
                                                operations and confirmations</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Green with checkmark icon
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Error</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">error</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Failed
                                                operations and error states</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Red with X icon
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Warning</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">warning</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Important
                                                notices and cautions</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Yellow with exclamation icon
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Info</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">info</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">General
                                                information and updates</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    Blue with info icon
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                <pre><code class="language-vue">{{ modalCode }}</code></pre>
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
                                <pre><code class="language-vue">{{ switchCode }}</code></pre>
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
                        Getting Started
                    </div>
                </div>
            </a>

            <!-- Next Page -->
            <a href="/documentation/component-api"
                class="group flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 transition-colors">
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Next</div>
                    <div
                        class="font-medium text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                        Component API
                    </div>
                </div>
                <svg class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import Public from '@js/Layouts/Public.vue'
import ArticleNavigation from '@js/Shared/Public/ArticleNavigation.vue'
import CodeBlock from '@js/Components/Common/CodeBlock.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import Modal from '@js/Components/Notifications/Modal.vue'

defineOptions({
  layout: Public,
})

const codeExamples = {
  formInput: `<FormInput
    v-model="email"
    label="Email Address"
    id="demo-email"
    type="email"
    required
    error="Please enter a valid email address"
/>`,

  formCheckbox: `<FormCheckbox
    v-model="selectedItems"
    :value="item.id"
    :label="item.name"
    :name="remember"
    :id="remember"
/>`,

  formSelect: `<FormSelect
    v-model="selectedCountry"
    label="Country"
    id="demo-country"
    :options="demoCountries"
    option-label="name"
    option-value="code"
    required
/>`,

  modal: `<Modal :show="isOpen" @close="isOpen = false" size="md">
    <template #title>Edit Profile</template>

    <div class="space-y-4">
        <!-- Modal content here -->
    </div>

    <template #footer>
        <button @click="save">Save Changes</button>
    </template>
</Modal>`,

  tab: `<Tabs v-model="activeTab" :tabs="tabs">
    <div v-if="activeTab === 0">
        <h3>Overview</h3>
        <p>This is the overview content.</p>
    </div>
    
    <div v-if="activeTab === 1">
        <h3>Settings</h3>
        <p>This is the settings content.</p>
    </div>
    
    <div v-if="activeTab === 2">
        <h3>Billing</h3>
        <p>This is the billing content.</p>
    </div>
</Tabs>

<!-- Script setup -->
const activeTab = ref(0)
const tabs = [
    { name: 'Overview', icon: 'chart-bar', color: 'text-blue-500' },
    { name: 'Settings', icon: 'document-text', color: 'text-green-500' },
    { name: 'Billing', icon: 'document-duplicate', color: 'text-purple-500' }
]`,

  pageHeader: `<PageHeader
    title="User Management"
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
</PageHeader>`,

  navSidebar: `<NavSidebarDesktop />`,

  navSidebarUsage: `// Add items to navigation sections
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
                    { name: 'Users', href: route('admin.user.index'), permission: 'view-users' },
                    // More child items...
                ]
            },
        ]
    }
])`,

  logo: `<Logo size="3.5rem" />`,

  tabs: `<Tabs v-model="activeTab" :tabs="tabs">
    <div v-if="activeTab === 0">
        <h3>Overview</h3>
        <p>This is the overview content.</p>
    </div>
    
    <div v-if="activeTab === 1">
        <h3>Settings</h3>
        <p>This is the settings content.</p>
    </div>
    
    <div v-if="activeTab === 2">
        <h3>Billing</h3>
        <p>This is the billing content.</p>
    </div>
</Tabs>

<!-- Script setup -->
const activeTab = ref(0)
const tabs = [
    { name: 'Overview', icon: 'chart-bar', color: 'text-blue-500' },
    { name: 'Settings', icon: 'document-text', color: 'text-green-500' },
    { name: 'Billing', icon: 'document-duplicate', color: 'text-purple-500' }
]`,

  areaChart: `<ApexAreaChart
    :chart-data="chartData"
    title="Monthly Revenue"
    height="300px"
/>`,

  barChart: `<ApexBarChart
    :chart-data="chartData"
    title="Revenue vs Expenses"
    height="300px"
/>`,

  lineChart: `<ApexLineChart
    :chart-data="chartData"
    title="Growth Trend"
    height="300px"
/>`,

  donutChart: `<ApexDonutChart
    :chart-data="chartData"
    title="Revenue Distribution"
    height="300px"
/>`,

  statWidget: `<StatWidget
    title="Total Users"
    value="1,234"
    description="Active this month"
    :icon="userIconSVG"
    trend="up"
    color="blue" />`,

  metricWidget: `<MetricWidget
    title="Total Revenue"
    :value="84621"
    trend="up"
    :change="12.5"
    :svg="revenueIconSVG"
    color="emerald" />`,

  chartWidget: `<ChartWidget
    title="Revenue"
    value="$45,231"
    :change="12.5"
    :data="[30, 40, 35, 50, 70]"
    color="emerald" />`,
}

const articleLinks = [
  {
    text: 'Form Components',
    href: '#form-components',
    children: [
      { text: 'Form Input', href: '#form-input' },
      { text: 'Form Checkbox', href: '#form-checkbox' },
      { text: 'Form Select', href: '#form-select' },
    ],
  },
  {
    text: 'Data Visualization',
    href: '#data-visualization',
    children: [
      { text: 'Area Chart', href: '#area-chart' },
      { text: 'Bar Chart', href: '#bar-chart' },
      { text: 'Line Chart', href: '#line-chart' },
      { text: 'Donut Chart', href: '#donut-chart' },
    ],
  },
  {
    text: 'Navigation Component',
    href: '#navigation-components',
    children: [{ text: 'Navigation Sidebar', href: '#nav-sidebar' }],
  },
  {
    text: 'Feedback Components',
    href: '#feedback-components',
    children: [
      { text: 'Modal', href: '#modal' },
      { text: 'Tab', href: '#tab' },
    ],
  },
  {
    text: 'Utility Components',
    href: '#utility-components',
    children: [
      { text: 'Logo', href: '#logo' },
      { text: 'Page Header', href: '#page-header' },
      { text: 'Tabs', href: '#tabs' },
    ],
  },
  {
    text: 'Dashboard Widgets',
    href: '#dashboard-widgets',
    children: [
      { text: 'Stat Widget', href: '#stat-widget' },
      { text: 'Metric Widget', href: '#metric-widget' },
      { text: 'Chart Widget', href: '#chart-widget' },
    ],
  },
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

// Demo state
const demoEmail = ref('')
const demoIsOpen = ref(false)
const demoSelectedCountry = ref('')
const demoCountries = ref([
  { name: 'United States', code: 'US' },
  { name: 'Canada', code: 'CA' },
  { name: 'United Kingdom', code: 'GB' },
  { name: 'Australia', code: 'AU' },
  { name: 'Germany', code: 'DE' },
  { name: 'France', code: 'FR' },
])

const demoChartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Revenue',
      data: [4400, 5500, 4900, 6500, 7000, 8000],
    },
    {
      label: 'Expenses',
      data: [3500, 4100, 3600, 4800, 5200, 6000],
    },
  ],
})

const chartProps = [
  {
    name: 'chartData',
    type: 'Object',
    default: 'Required',
    description: 'Data object containing labels and datasets for the chart',
  },
  {
    name: 'height',
    type: 'String',
    default: '400px',
    description: 'Height of the chart container',
  },
  {
    name: 'title',
    type: 'String',
    default: "''",
    description: 'Title displayed above the chart',
  },
]
</script>

<template>
  <Head title="Components - GuacPanel" />

  <div id="introduction" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="mb-12 border bg-white p-8 md:p-12 dark:bg-gray-900">
      <div class="text-center">
        <div class="mb-4 flex items-center justify-center">
          <h1 class="text-3xl font-bold md:text-4xl">Component Library</h1>
        </div>

        <div class="mx-auto mb-8 max-w-2xl">
          <p class="mb-4 text-lg text-gray-700 dark:text-gray-200">
            Explore our collection of beautiful, accessible, and reusable components built with Vue
            3 and Tailwind CSS. Each component is designed to be flexible, customizable, and easy to
            integrate into your projects.
          </p>
        </div>

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
          <a
            href="#form-components"
            class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            [Form_Components]
          </a>
          <a
            href="/documentation/features"
            class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
            <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
              <path
                fill-rule="evenodd"
                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                clip-rule="evenodd" />
            </svg>
            [Features]
          </a>
        </div>
      </div>
    </div>

    <section class="space-y-10">
      <!-- Form Components Section -->
      <section id="form-components" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Form Components</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Essential form components for building user interfaces with flexible inputs and
            validation
          </p>
        </div>
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Essential form components for building user interfaces with flexible inputs, checkboxes,
            and select elements with validation support.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>FormInput - Flexible input with floating labels and error handling</li>
            <li>FormSelect - Select dropdown with search functionality</li>
          </ul>

          <div class="space-y-6">
            <!-- Form Input -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Form Input</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Flexible input component with floating labels, error handling, and password toggle
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.formInput" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Form Select -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Form Select</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Select dropdown component with search functionality and flexible options
              </p>

              <!-- Live Demo -->
              <div
                class="mb-4 rounded-lg border border-green-200 bg-gradient-to-br from-green-50 to-emerald-50 p-4 dark:border-green-900/30 dark:from-green-950/20 dark:to-emerald-950/20">
                <p
                  class="mb-3 flex items-center text-xs font-semibold text-green-900 dark:text-green-300">
                  <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  LIVE DEMO
                </p>
                <FormSelect
                  v-model="demoSelectedCountry"
                  label="Country"
                  id="demo-country"
                  :options="demoCountries"
                  option-label="name"
                  option-value="code"
                  required />
                <p class="mt-3 font-mono text-xs text-green-800 dark:text-green-300">
                  Selected: {{ demoSelectedCountry || '(none)' }}
                </p>
              </div>

              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.formSelect" language="vue" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Data Visualization Section -->
      <section id="data-visualization" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Data Visualization</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Chart components for displaying data in visual formats with interactive features
          </p>
        </div>
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Essential chart components for creating informative and interactive data visualizations
            with customizable styling and multiple chart types.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>Area Chart - Versatile area chart with multiple datasets</li>
            <li>Bar Chart - Customizable bar chart for comparisons</li>
            <li>Line Chart - Line chart for trend visualization</li>
            <li>Donut Chart - Donut chart for data distribution</li>
          </ul>

          <div class="space-y-6">
            <!-- Area Chart -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Area Chart</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Versatile area chart component that supports multiple datasets and customizable
                styling
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.areaChart" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Bar Chart -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Bar Chart</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Customizable bar chart component for data comparisons with multiple datasets
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.barChart" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Line Chart -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Line Chart</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Line chart component for trend visualization with customizable styling
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.lineChart" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Donut Chart -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Donut Chart</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Donut chart component for data distribution visualization with customizable styling
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.donutChart" language="vue" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Navigation Components Section -->
      <section id="navigation-components" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
            Navigation Components
          </h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Navigation components for efficient user movement through your application
          </p>
        </div>
        <div
          class="border border-gray-200 bg-white p-6 font-mono dark:border-gray-700 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Essential navigation components for creating intuitive interfaces with menus, sidebars,
            and navigation structures. These components are designed for accessibility,
            responsiveness, and seamless integration with your permission system.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>Navigation Sidebar - Accessible sidebar with nested menus and permissions</li>
            <li>Article Navigation - Table of contents sidebar for documentation</li>
            <li>Nav Profile - User profile dropdown with quick actions</li>
          </ul>

          <div class="space-y-6">
            <!-- Navigation Sidebar -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                Navigation Sidebar
              </h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Accessible sidebar navigation with nested menus, permissions, and dark mode support.
                Automatically handles mobile responsiveness and collapsible sections.
              </p>

              <div
                class="mb-4 rounded-lg border border-teal-400 bg-gradient-to-br from-teal-50 to-teal-50 p-3 dark:border-teal-800/30 dark:from-teal-900/20 dark:to-teal-900/20">
                <p class="flex items-start space-x-2 text-sm text-teal-800 dark:text-teal-300">
                  <span class="flex-shrink-0 text-xl">💡</span>
                  <span>
                    <strong>Key Features:</strong>
                    <ul class="mt-1 ml-1 list-inside list-disc text-xs">
                      <li>Permission-based visibility of menu items</li>
                      <li>Collapsible nested sections</li>
                      <li>Mobile-responsive with drawer navigation</li>
                      <li>Auto active state detection</li>
                      <li>Dark mode support</li>
                    </ul>
                  </span>
                </p>
              </div>

              <div class="mb-4 rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.navSidebar" language="vue" />
              </div>

              <details class="mt-4">
                <summary
                  class="mb-2 cursor-pointer list-none text-sm font-semibold text-gray-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                  Configuration Example (Click to expand)
                </summary>
                <div class="mt-3">
                  <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                    Define navigation items with nested children, icons, and permission-based
                    visibility:
                  </p>
                  <div class="rounded-lg bg-gray-800">
                    <CodeBlock :code="codeExamples.navSidebarUsage" language="javascript" />
                  </div>
                </div>
              </details>
            </div>
          </div>
        </div>
      </section>

      <!-- Feedback Components Section -->
      <section id="feedback-components" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Feedback Components</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Visual and interactive feedback components for user actions and application state
          </p>
        </div>
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Essential feedback components for creating responsive and user-friendly interfaces with
            visual notifications and interactive dialogs.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>Modal - Flexible dialog components with accessibility support</li>
            <li>Tab - Tabbed interface component for organizing content</li>
          </ul>

          <div class="space-y-6">
            <!-- Modal -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Modal</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Flexible dialog component with accessibility support and multiple sizes
              </p>

              <!-- Live Demo -->
              <div
                class="mb-4 rounded-lg border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-4 dark:border-amber-900/30 dark:from-amber-950/20 dark:to-orange-950/20">
                <p
                  class="mb-3 flex items-center text-xs font-semibold text-amber-900 dark:text-amber-300">
                  <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  LIVE DEMO
                </p>
                <button
                  @click="demoIsOpen = true"
                  class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                  Open Modal
                </button>

                <Modal :show="demoIsOpen" @close="demoIsOpen = false" size="md">
                  <template #title>Example Modal</template>
                  <div class="space-y-4">
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                      This is a live demo of the Modal component. You can close it by clicking the X
                      button, pressing Escape, or clicking outside the modal.
                    </p>
                    <FormInput v-model="demoEmail" label="Email" id="modal-email" type="email" />
                  </div>
                  <template #footer>
                    <div class="flex justify-end gap-8">
                      <button
                        @click="demoIsOpen = false"
                        class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Cancel
                      </button>
                      <button
                        @click="demoIsOpen = false"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                        Save
                      </button>
                    </div>
                  </template>
                </Modal>
              </div>

              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.modal" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Tab -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Tab</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Tabbed interface component for organizing content with keyboard navigation and
                accessibility support
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.tab" language="vue" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Utility Components Section -->
      <section id="utility-components" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Utility Components</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Reusable UI elements that provide common functionality across your application
          </p>
        </div>
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Essential utility components for building consistent user interfaces with reusable
            elements and common functionality.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>PageHeader - Consistent page headers with breadcrumbs and actions</li>
            <li>Logo - Flexible logo component with fallback support</li>
            <li>Tabs - Tabbed interface component for organizing content</li>
          </ul>

          <div class="space-y-6">
            <!-- Page Header -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Page Header</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                A consistent page header component with title, breadcrumbs, and flexible actions
                slot
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.pageHeader" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Logo -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Logo</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Flexible logo component with dynamic sizing and fallback support
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.logo" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Tabs -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Tabs</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Tabbed interface component with responsive design and icon support
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.tabs" language="vue" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Dashboard Widgets Section -->
      <section id="dashboard-widgets" class="mb-12 scroll-mt-16">
        <div class="mb-8 text-center">
          <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Dashboard Widgets</h2>
          <p class="mx-auto max-w-2xl text-gray-700 dark:text-gray-200">
            Pre-built dashboard widgets for displaying metrics, stats, and charts
          </p>
        </div>
        <div
          class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
          <p class="mb-4 text-gray-700 dark:text-gray-200">
            Dashboard widgets for visualizing key metrics and statistics with built-in trend
            indicators and customizable themes.
          </p>

          <ul
            class="mb-6 ml-4 list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            <li>StatWidget - Clean stat cards with icons and trends</li>
            <li>MetricWidget - Bold brutalist metric displays</li>
            <li>ChartWidget - Sparkline chart visualizations</li>
            <li>ProgressWidget, RetroStatWidget, StockWidget available</li>
          </ul>

          <div class="space-y-6">
            <!-- Stat Widget -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Stat Widget</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Clean stat card with icon, value, and optional trend indicator
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.statWidget" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Metric Widget -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                Metric Widget
              </h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Bold brutalist metric card with geometric accents and trend indicators
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.metricWidget" language="vue" />
              </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700" />

            <!-- Chart Widget -->
            <div>
              <h5 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Chart Widget</h5>
              <p class="mb-3 text-xs text-gray-700 dark:text-gray-200">
                Sparkline chart widget with dynamic SVG visualization
              </p>
              <div class="rounded-lg bg-gray-800">
                <CodeBlock :code="codeExamples.chartWidget" language="vue" />
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>
  </div>

  <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex flex-col justify-center gap-4 sm:flex-row">
      <a
        href="/documentation/features"
        class="inline-flex items-center border border-gray-400 bg-white px-6 py-3 font-mono text-sm tracking-wider text-gray-800 uppercase transition-all hover:border-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7" />
        </svg>
        [Features]
      </a>

      <a
        href="/documentation"
        class="inline-flex items-center border border-green-500 bg-green-500/20 px-6 py-3 font-mono text-sm tracking-wider text-green-700 uppercase transition-all hover:bg-green-500/30 dark:text-green-200">
        [Documentation]
        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>
    </div>
  </div>

  <!-- Back to Top Button -->
  <button
    v-show="showBackToTop"
    class="fixed right-8 bottom-8 z-50 border border-green-500 bg-green-500/20 p-3 font-mono text-sm text-green-700 transition-all hover:bg-green-500/30 dark:text-green-200"
    aria-label="Back to top"
    @click="scrollToTop">
    [^]
  </button>

  <ArticleNavigation :links="articleLinks" />
</template>

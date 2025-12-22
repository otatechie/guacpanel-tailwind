## Form Components

- `FormInput.vue`
  - Purpose: Text/password/date/etc. input with floating label, validation, optional password toggle.
  - Props: `modelValue`, `label` (required), `type` (`text|password|date|time|datetime-local`), `placeholder`, `required`, `disabled`, `error`, `help`, `id`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormInput v-model="form.email" label="Email" type="email" :error="errors.email" />
    ```

- `FormTextarea.vue`
  - Purpose: Textarea with floating label and validation.
  - Props: `modelValue`, `label` (required), `placeholder`, `rows`, `required`, `disabled`, `error`, `help`, `id`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormTextarea v-model="form.bio" label="Bio" rows="4" :error="errors.bio" />
    ```

- `FormSelect.vue`
  - Purpose: Custom searchable single-select (combobox-like) with keyboard support and clear button.
  - Props: `modelValue`, `label` (required), `options` (array of objects), `optionLabel` (default `label`), `optionValue` (default `value`), `placeholder`, `loading`, `required`, `disabled`, `error`, `id`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormSelect
      v-model="form.country"
      label="Country"
      :options="countries"
      optionLabel="name"
      optionValue="code"
      :loading="isLoading"
      :error="errors.country"
    />
    ```

- `FormCheckbox.vue`
  - Purpose: Single checkbox with label, help, and error.
  - Props: `modelValue` (Boolean), `label` (required), `required`, `disabled`, `error`, `help`, `id`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormCheckbox v-model="form.terms" label="I agree to terms" :error="errors.terms" />
    ```

- `FormCheckboxGroup.vue`
  - Purpose: Grid of checkbox cards for multi-select.
  - Props: `modelValue` (Array), `options` (required), `optionLabel` (`name`), `optionValue` (`id`), `optionDescription` (`description`), `label`, `help`, `columns` (`auto` or number), `disabled`, `error`, `name`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormCheckboxGroup
      v-model="form.tags"
      label="Tags"
      :options="tags"
      optionLabel="name"
      optionValue="id"
      optionDescription="description"
      columns="3"
      :error="errors.tags"
    />
    ```

- `FormRadioGroup.vue`
  - Purpose: Radio group with label/description per option.
  - Props: `modelValue` (String|Number|Boolean), `options` (required: `{ label, value, description? }`), `label`, `name`, `required`, `error`.
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <FormRadioGroup
      v-model="form.plan"
      label="Plan"
      :options="[
        { label: 'Free', value: 'free' },
        { label: 'Pro', value: 'pro', description: 'Best for teams' }
      ]"
      :error="errors.plan"
    />
    ```

## Widgets Structure (recommended)

- Place reusable UI widgets under `resources/js/Components/Widgets/`.
- Document each widget similarly: purpose, props, emits, usage snippet.

## General Components

- `Modal.vue` (`resources/js/Components/Modal.vue`)
  - Purpose: Accessible modal with focus trap, ESC/overlay close, size variants.
  - Props: `show` (Boolean), `size` (`sm|md|lg|xl|2xl|3xl|4xl|5xl|full`, default `md`), `closeOnClickOutside` (Boolean, default `true`).
  - Emits: `close`.
  - Slots: `title`, default, `footer`.
  - Usage:
    ```vue
    <Modal :show="open" size="md" @close="open = false">
      <template #title>Confirm</template>
      <p>Are you sure?</p>
      <template #footer>
        <button @click="open = false">Cancel</button>
      </template>
    </Modal>
    ```

- `Tabs.vue` (`resources/js/Components/Tabs.vue`)
  - Purpose: Responsive tabs (desktop tabs + mobile select).
  - Props: `v-model` (Number index), `tabs` (Array of strings or objects `{ name, icon?, color? }`).
  - Emits: `update:modelValue`.
  - Usage:
    ```vue
    <Tabs v-model="active" :tabs="[{ name: 'Profile' }, { name: 'Security', icon: 'document-text' }]">
      <div v-if="active === 0">Profile content</div>
      <div v-else>Security content</div>
    </Tabs>
    ```

- `PageHeader.vue` (`resources/js/Components/PageHeader.vue`)
  - Purpose: Page title/description with breadcrumbs and actions slot.
  - Props: `title` (required), `description`, `breadcrumbs` (Array `{ label, href? }`).
  - Slots: `actions`.
  - Usage:
    ```vue
    <PageHeader
      title="Users"
      description="Manage all users"
      :breadcrumbs="[{ label: 'Dashboard', href: route('dashboard') }, { label: 'Users' }]">
      <template #actions>
        <button class="btn-primary btn-sm">Add User</button>
      </template>
    </PageHeader>
    ```

- `FeatureCard.vue` (`resources/js/Components/FeatureCard.vue`)
  - Purpose: Highlight feature with icon, title, description, optional link.
  - Props: `title`, `description`, `icon` (path d), `link`, `color` (`teal|indigo|emerald`).
  - Slots: `icon` (optional).
  - Usage:
    ```vue
    <FeatureCard
      title="API Ready"
      description="Robust endpoints with auth"
      icon="M5 12h14"
      color="teal"
    />
    ```

- `CodeBlock.vue` (`resources/js/Components/CodeBlock.vue`)
  - Purpose: Syntax-highlighted code with optional copy button.
  - Props: `code` (String|Object), `language` (`javascript|php|html|css|bash|typescript|python|json|vue|jsx|tsx|auto`), `showCopyButton` (Boolean, default `true`).
  - Usage:
    ```vue
    <CodeBlock :code="snippet" language="vue" />
    ```

- `ArticleNavigation.vue` (`resources/js/Components/ArticleNavigation.vue`)
  - Purpose: Sticky article outline with scroll spy.
  - Props: `links` (Array `{ id, title }`).
  - Usage:
    ```vue
    <ArticleNavigation :links="[{ id:'intro', title:'Introduction' }, { id:'setup', title:'Setup' }]" />
    ```

- `ColorThemeSwitcher.vue` (`resources/js/Components/ColorThemeSwitcher.vue`)
  - Purpose: Theme color picker dropdown (stores choice in localStorage, applies via `applyThemeColor`).
  - Props: none (uses theme config from `utils/themeInit`).
  - Usage:
    ```vue
    <ColorThemeSwitcher />
    ```

- `Datatable.vue` (`resources/js/Components/Datatable.vue`)
  - Purpose: Feature-rich table (sorting, search, pagination client/server, bulk delete modal, export, row expansion).
  - Props (key): `data` (Array), `columns` (tanstack column defs), `title`, `enableSearch`, `enableExport`, `searchFields`, `emptyMessage`, `emptyDescription`, `exportFileName`, `pageSizeOptions`, `defaultPageSize`, `loading`, `error`, `bulkDeleteRoute`, `pagination` (object for server mode), `filters`, `formatExportData`, `routeName`, `routeParams`.
  - Emits: `update:pagination`, `bulk-delete`, `navigate`.
  - Slots: `row` via column renderers; built-in bulk delete modal.
  - Usage (client pagination):
    ```vue
    <Datatable
      :data="rows"
      :columns="columns"
      :search-fields="['name','email']"
      empty-message="No records"
      empty-description="Data will appear here"
    />
    ```
  - Usage (server pagination):
    ```vue
    <Datatable
      :data="rows"
      :columns="columns"
      :pagination="pagination"
      :loading="loading"
      :search-fields="['name']"
      @update:pagination="pagination = $event"
    />
    ```

- `NavProfile.vue` (`resources/js/Components/Nav/NavProfile.vue`)
  - Purpose: User profile dropdown (avatar) with role chip, quick links, and sign-out.
  - Behavior: Toggles on click; closes on outside click/ESC; highlights active route; permission-gated menu items.
  - Props: none (reads user/permissions from Inertia `page.props.auth.user`).
  - Usage:
    ```vue
    <NavProfile />
    ```

- `NavSidebarDesktop.vue` (`resources/js/Components/Nav/NavSidebarDesktop.vue`)
  - Purpose: Desktop sidebar navigation with sections, permission gating, parent/child routes, and expand/collapse behavior.
  - Behavior: Highlights current route; parent expands when its child is active; clicking parent while on its route toggles collapse; respects permissions for parents/children.
  - Props: none (uses Inertia page props for auth/permissions).
  - Usage:
    ```vue
    <!-- Uses internal navigationSections; example of shape if you customize -->
    <NavSidebarDesktop />
    <!-- navigationSections item example (in component or via future injection):
    [
      {
        items: [
          { name: 'Dashboard', route: 'dashboard', icon: '<path ... />' },
          { type: 'divider' },
        ],
      },
      {
        items: [
          {
            name: 'System Settings',
            route: 'admin.setting.index',
            icon: '<path ... />',
            permission: 'manage-settings',
            children: [
              { name: 'User Management', route: 'admin.user.index', permission: 'manage-users' },
              { name: 'Access Control', route: 'admin.permission.role.index' },
            ],
          },
          { type: 'divider' },
        ],
      },
    ]
    -->
    ```

## Widgets

- `ChartWidget.vue`
  - Purpose: Sparkline-style mini chart with percentage change.
  - Props: `title`, `value`, `change` (Number), `data` (Number[]), `color` (`blue|green|red|yellow|purple|emerald`).
  - Usage:
    ```vue
    <ChartWidget title="Revenue" value="$45,231" :change="12.5" :data="[30,40,35,50,70]" color="emerald" />
    ```

- `MetricWidget.vue`
  - Purpose: Bold metric card with brutalist styling and trend indicator.
  - Props: `title`, `value`, `trend` (`up|down|null`), `change` (Number), `svg`, `viewBox`, `color` (`blue|green|red|yellow|purple|primary`).
  - Usage:
    ```vue
    <MetricWidget title="Total Revenue" :value="84621" trend="up" :change="12.5" :svg="revenueIcon" color="emerald" />
    ```

- `ProgressWidget.vue`
  - Purpose: Progress bar card with percentage and description.
  - Props: `title`, `value` (Number), `max` (Number, default 100), `description`, `color` (`blue|green|red|yellow|purple|indigo`), `showPercentage` (Boolean).
  - Usage:
    ```vue
    <ProgressWidget title="Storage Used" :value="75" :max="100" description="75GB of 100GB used" color="blue" />
    ```

- `StatWidget.vue`
  - Purpose: Stat card with icon and optional trend label.
  - Props: `title`, `value`, `description`, `icon` (svg string), `trend` (`up|down|neutral`), `color` (`blue|green|red|yellow|purple`).
  - Usage:
    ```vue
    <StatWidget title="Total Users" value="1,234" description="Active this month" :icon="userIcon" trend="up" color="blue" />
    ```

- `StockWidget.vue`
  - Purpose: Stock price card with logo, price, and change indicator.
  - Props: `stock` ({ symbol, name, price, change, currency }), `size` (`sm|md|lg`), `src` (logo), `alt`.
  - Usage:
    ```vue
    <StockWidget :stock="{ symbol:'AAPL', name:'Apple Inc', price:'173.25', change:0.86, currency:'$' }" src="/apple.svg" alt="Apple" size="lg" />
    ```

## Charts

- `Charts/ApexAreaChart.vue`
  - Purpose: ApexCharts area chart with gradient fill and dark-mode support.
  - Props: `chartData` ({ labels: string[], datasets: [{ label, data[] }]}), `height` (default `400px`), `title`.
  - Usage:
    ```vue
    <ApexAreaChart :chartData="areaData" title="Revenue" height="320px" />
    ```

- `Charts/ApexLineChart.vue`
  - Purpose: ApexCharts line chart with smooth stroke and dark-mode support.
  - Props: same shape as area chart (`chartData`, `height`, `title`).
  - Usage:
    ```vue
    <ApexLineChart :chartData="lineData" title="Signups" height="320px" />
    ```

- `Charts/ApexBarChart.vue`
  - Purpose: ApexCharts bar chart for categorical comparisons.
  - Props: `chartData` ({ labels, datasets: [{ label, data[] }]}), `height`, `title`.
  - Usage:
    ```vue
    <ApexBarChart :chartData="barData" title="Sales by Region" height="320px" />
    ```

- `Charts/ApexDonutChart.vue`
  - Purpose: ApexCharts donut chart with totals and dark-mode support.
  - Props: `chartData` ({ labels, datasets: [{ data[] }]}), `height`, `title`.
  - Usage:
    ```vue
    <ApexDonutChart :chartData="donutData" title="Revenue Split" height="320px" />
    ```


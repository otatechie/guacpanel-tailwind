# UI component contract

Pages and layouts import wrappers from `@/Components/*` — **never** `@/Components/ui/*`,
`reka-ui`, `class-variance-authority`, or `@/lib/utils`. ESLint enforces this
(`no-restricted-imports` in `eslint.config.js`).

The wrapper layer is what makes shadcn removable: reimplementing these components
against this document, with plain Tailwind, must be possible without touching a
single page. Keep it that way:

- Wrappers own the prop vocabulary below. shadcn/cva terms never leak into pages.
- Wrappers are dumb — props in, events out. Behavior lives in `resources/js/composables/`.
- Design tokens live in `resources/css/app.css` (`:root` / `.dark`); retheme there,
  never in `Components/ui/` files.
- `Components/ui/` is generated code (`npx shadcn-vue@latest add <name>`), TypeScript,
  excluded from ESLint/Prettier. Don't hand-edit it; regenerate instead.
- After every `shadcn-vue add`, check `git diff resources/css/app.css` — the CLI
  re-injects a Google Fonts `@import` and config blocks it thinks are missing.
- Overlays use the z-scale tokens in `resources/css/partials/theme.css`
  (`--z-banner` … `--z-progress`).
- Every wrapper has contract tests in `resources/js/Components/__tests__/`.
  Run them with `npm test`. They assert this document, not shadcn internals —
  they must keep passing if `ui/` is ever swapped out.

## Button — `@/Components/Button.vue`

| Prop | Values | Default | Notes |
|---|---|---|---|
| `variant` | `primary` `secondary` `danger` `ghost` `outline` `link` | `primary` | replaces `btn-primary` etc. |
| `size` | `xs` `sm` `md` `lg` `icon` `icon-sm` `icon-xs` | `md` | replaces `btn-sm` etc. |
| `type` | native button types | `button` | pass `submit` explicitly for form submits |
| `as` | element name or component | `button` | e.g. Inertia's `Link` |

Slots: default. Events/attrs (`@click`, `disabled`, `class`, aria-*) fall through.

```vue
<Button variant="danger" size="sm" @click="destroy">Delete</Button>
<Button type="submit" class="w-full">Save changes</Button>
<Button :as="Link" :href="route('admin.user.index')" variant="secondary">Back</Button>
```

`danger` renders a **solid** red fill (matching the old `.btn-danger`), overriding
shadcn's soft `destructive` tint. That override lives in the wrapper, not in `ui/`.

## Badge — `@/Components/Badge.vue`

| Prop | Values | Default |
|---|---|---|
| `variant` | `neutral` `primary` `info` `success` `warning` `danger` | `neutral` |

Semantic colours shadcn has no equivalent for; defined in the wrapper.
`RoleBadge` and `NotificationTypeBadge` build on it and keep their own APIs.

## Form controls — `@/Components/Forms/*`

Public APIs are unchanged from before the migration; only the internals moved onto
shadcn primitives. All of them take `modelValue` and emit `update:modelValue`.

| Component | Props | Notes |
|---|---|---|
| `FormInput` | `modelValue` `label`* `id` `type` `required` `error` `placeholder` `disabled` `help` | `type="password"` adds a show/hide toggle |
| `FormTextarea` | same as above plus `rows` | |
| `FormCheckbox` | `modelValue` `label`* `id` `required` `error` `disabled` `help` | always emits a strict boolean, never `indeterminate` |
| `Switch` | `modelValue`* `disabled` `label` | `label` becomes the aria-label |

`label` is used to derive the input `id` when `id` is not given
(`"Email address"` → `email-address`). `error` sets `aria-invalid` and renders a
`role="alert"`; `help` renders only when there is no error.

## Modal — `@/Components/Notifications/Modal.vue`

| Prop | Values | Default |
|---|---|---|
| `show` | boolean | — |
| `size` | `sm` `md` `lg` `xl` `2xl` `3xl` | `md` |
| `closeOnClickOutside` | boolean | `true` |

Emits `close`. Slots: `title`, default, `footer`.

API is unchanged, but focus trapping, escape handling, scroll lock, `role="dialog"`
and `aria-labelledby` now come from reka-ui rather than being hand-rolled.

## Deliberately app-level (not shadcn)

These have working, app-specific behavior whose APIs are worth more than
conformity. They use the design tokens but are not shadcn wrappers:

| Component | Why |
|---|---|
| `Forms/FormSelect` | searchable combobox: filtering, keyboard nav, clear, flip positioning. shadcn `Select` is a plain select — swapping it would be a behavior migration, not a restyle. |
| `Common/Datatable` | TanStack Table v8 + `useServerPagination`. Restyle its chrome; never rewrite it. |
| `Common/Tabs` | index-based `modelValue: Number`. shadcn Tabs is string-keyed — converting breaks every caller for marginal gain. |
| `Skeleton/Skeleton` | composite line/card/table variants with a custom shimmer. |
| `Notifications/Alert` | semantic icon set + dismissible behavior. |
| `CommandPalette`, `Typesense/Search`, `Forms/FilePondUploader` | wrap third-party or bespoke behavior. |

## Removing shadcn

1. Delete `resources/js/Components/ui/`, `resources/js/lib/utils.ts`, `components.json`.
2. Reimplement the ~6 wrappers above against this document with plain Tailwind,
   using the tokens already in `app.css`.
3. `npm uninstall reka-ui class-variance-authority clsx tailwind-merge tw-animate-css @lucide/vue`
4. Drop the `no-restricted-imports` block from `eslint.config.js`.

No page or layout file needs to change. `npm test` tells you when you're done.

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

## Toasts — `@/Components/Toaster.vue` + `@/composables/useToast`

Mount `<Toaster />` once per layout; call `useToast()` anywhere to raise one.

| Toaster prop | Default | Notes |
|---|---|---|
| `offset` | `72px` | clears the fixed header; auth pages pass `16px` |

`useToast()` returns `success` / `error` / `warning` / `info` / `dismiss`, plus
`show(message, type, options)`. `danger` is accepted as an alias for `error`,
because Laravel flash keys and `Alert` both use that word. An empty message is
ignored rather than shown as a blank toast.

```js
const toast = useToast()
toast.success('Profile updated')
toast.show(flash.error, 'danger')
```

Stacking, per-toast timers, pause-on-hover and live-region semantics belong to
the toast library — don't hand-roll them, and don't add a `window` global.

## Popover — `@/Components/Popover.vue`

| Prop | Values | Default | Notes |
|---|---|---|---|
| `align` | `start` `center` `end` | `end` | trigger edge the panel lines up with |
| `width` | any width utility | `w-72` | responsive utilities work: `w-[calc(100vw-1.5rem)] sm:w-80` |
| `open` | boolean | _(unset)_ | optional `v-model:open`; leave unbound to let the panel manage itself |

Slots: `trigger` (the control that opens it) and default (the panel body).
The wrapper strips shadcn's padding and flex gap so each section owns its spacing.

Bind `v-model:open` only when something inside has to close the panel — a link
that navigates without unmounting the trigger, or an action that dismisses it.
Escape, outside-click, focus return and positioning are handled for you; don't
re-add document listeners for them.

```vue
<Popover v-model:open="isOpen" width="w-80">
    <template #trigger><button aria-label="Notifications">…</button></template>
    <div>…rows…</div>
</Popover>
```

Panel content sits in normal tab order, so forms and controls inside behave
as they would anywhere else.

## Dropdown menu — `@/Components/DropdownMenu.vue`

| Prop | Values | Default | Notes |
|---|---|---|---|
| `align` | `start` `center` `end` | `end` | trigger edge the panel lines up with |
| `width` | any width utility | `w-56` | replaces shadcn's size-to-the-trigger default |
| `open` | boolean | _(unset)_ | optional `v-model:open`, same rules as `Popover` |
| `modal` | boolean | `false` | modal menus prevent Tab and make the page inert |

Slots: `trigger` and default, as `Popover`. The wrapper strips shadcn's panel
padding so each section owns its spacing.

Reach for this over `Popover` when the panel is a menu hanging off a top-bar
control — it carries menu semantics and keyboard behaviour the popover doesn't.
The default is non-modal because these panels hold their own controls (a dismiss
button per row, a "Read all" action) and those must stay in normal tab order;
pass `modal` only for a panel that is purely a list of commands.

```vue
<DropdownMenu v-model:open="isOpen" width="w-80">
    <template #trigger><button aria-label="Notifications">…</button></template>
    <div>…rows…</div>
</DropdownMenu>
```

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
| `FormCheckbox` | `modelValue` `label`* `id` `required` `error` `disabled` `help` `indeterminate` | `indeterminate` renders the mixed state for a box governing others; it still emits a strict boolean, never `indeterminate` |
| `Switch` | `modelValue`* `disabled` `label` `describedBy` | `label` becomes the aria-label, `describedBy` the aria-describedby; renders an `On`/`Off` word beside the track |

`label` is used to derive the input `id` when `id` is not given
(`"Email address"` → `email-address`). `error` sets `aria-invalid` and renders a
`role="alert"`; `help` renders only when there is no error.

**Field height is `h-8` (32px)**, matching `Button size="sm"` — the size that sits
beside these controls everywhere. shadcn's `Input` ships `h-9`, so `FormInput`
overrides it in the wrapper; `ui/input/Input.vue` is regenerated and must not be
edited. Non-shadcn controls (`FormSelect`, the `Datatable` toolbar) get the same
height from the `.form-input` class in `resources/css/partials/forms.css`. A new
control that pairs with a button belongs on one of those two, not on a fresh value.

## Modal — `@/Components/Notifications/Modal.vue`

| Prop | Values | Default |
|---|---|---|
| `show` | boolean | — |
| `size` | `sm` `md` `lg` `xl` `2xl` `3xl` | `md` |
| `closeOnClickOutside` | boolean | `true` |
| `description` | string — optional one-line subtitle | `''` |

Emits `close`. Slots: `title`, default, `footer`.

The body slot is the dialog's accessible description (`aria-describedby`), so
`description` is a subtitle, not a requirement — a modal whose substance lives in
the body just omits it.

API is unchanged, but focus trapping, escape handling, scroll lock, `role="dialog"`
and `aria-labelledby` now come from reka-ui rather than being hand-rolled.

## Stacking

shadcn portals its overlays, dialogs, sheets, dropdowns, popovers and tooltips at
`z-50` and above, and the wrappers cannot reach the overlay to change it. So app
chrome stays **below 50**:

| Layer | z |
|---|---|
| Mobile sidebar backdrop | 20 |
| Sidebar, mobile notification | 30 |
| Header | 40 |
| System notification banner, impersonation banner | 45 |
| Everything shadcn portals | 50+ |

A fixed element that lands on 50 or above will cover a dialog — the header was at
`z-55` and ate the top of every modal and sheet, including their close buttons.

## Sheet — `@/Components/Notifications/Sheet.vue`

| Prop | Values | Default |
|---|---|---|
| `show` | boolean | — |
| `side` | `right` `left` | `right` |
| `size` | `sm` `md` `lg` `xl` | `md` |
| `closeOnClickOutside` | boolean | `true` |
| `description` | string — optional one-line subtitle | `''` |

Emits `close`. Slots: `title`, default, `footer` — the same shape as `Modal`, so
the two are learnable together, and the body is likewise the accessible
description.

Reach for `Sheet` over `Modal` when the content is a form or a long list: a panel
gives vertical room and leaves the record you came from on screen. Keep `Modal`
for confirmations and anything short enough to read at a glance.

Both wrappers pass `showCloseButton: false` to the shadcn primitive and render
their own close inside the header row. shadcn positions its close `absolute top-4
right-4`, which knows nothing about the header's padding and lands off-centre
against the title.

## Deliberately app-level (not shadcn)

These have working, app-specific behavior whose APIs are worth more than
conformity. They use the design tokens but are not shadcn wrappers:

| Component | Why |
|---|---|
| `Forms/FormSelect` | searchable combobox: filtering, keyboard nav, clear, flip positioning. shadcn `Select` is a plain select — swapping it would be a behavior migration, not a restyle. |
| `Common/Datatable` | TanStack Table v8 + `useServerPagination`. Restyle its chrome; never rewrite it. |
| `Common/RowActions` | `actions` (`[{ label, icon, variant, onSelect }]`) and `label`. Wraps shadcn `DropdownMenu` as the `⋯` trigger for a table row. Renders nothing when `actions` is empty, so the column keeps one position on every row. |
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

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

## Button — `@/Components/Button.vue`

| Prop | Values | Default | Notes |
|---|---|---|---|
| `variant` | `primary` `secondary` `danger` `ghost` `outline` `link` | `primary` | replaces `btn-primary` etc. |
| `size` | `xs` `sm` `md` `lg` `icon` | `md` | replaces `btn-xs` etc. |
| `type` | native button types | `button` | explicit `submit` for form submits |

Slots: default. Events/attrs (`@click`, `disabled`, `class`, aria-*) fall through
to the underlying `<button>`.

```vue
<Button variant="danger" size="sm" @click="destroy">Delete</Button>
<Button type="submit" class="w-full">Save changes</Button>
```

Migration map: `btn btn-primary` → `<Button>`, `btn-secondary` → `variant="secondary"`,
`btn-danger` → `variant="danger"`, `btn-ghost` → `variant="ghost"`,
`btn-sm` → `size="sm"`, `btn-md`/no size → default.

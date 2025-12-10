import { createApp, h } from 'vue'
import { createInertiaApp, Link, router } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import NProgress from 'nprogress'
import '@css/app.css'
import '@js/utils/darkMode.js'
import { initializeTheme } from '@js/utils/themeInit'
import InstantSearch from 'vue-instantsearch/vue3/es'
import Default from '@js/Layouts/Default.vue'
import Auth from '@js/Layouts/Auth.vue'
import Public from '@js/Layouts/Public.vue'

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

NProgress.configure({ showSpinner: false })
router.on('start', () => NProgress.start())
router.on('finish', () => NProgress.done())
router.on('error', () => NProgress.done())

const appName = import.meta.env.VITE_APP_NAME ?? 'GuacPanel'

createInertiaApp({
  progress: {
    delay: 50,
    color: '#ffa500',
    includeCSS: true,
  },
  title: title => `${title} - ${appName}`,
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue', { eager: true })
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app
      .use(plugin)
      .use(ZiggyVue)
      .use(InstantSearch)
      .component('Link', Link)
      .component('Default', Default)
      .component('Auth', Auth)
      .component('Public', Public)
      .mount(el)

    return app
  },
})

initializeTheme()

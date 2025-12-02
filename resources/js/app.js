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

initializeTheme()

NProgress.configure({ showSpinner: false })
router.on('start', () => NProgress.start())
router.on('finish', () => NProgress.done())
router.on('error', () => NProgress.done())

const appName = import.meta.env.VITE_APP_NAME ?? 'GuacPanel'
const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })

createInertiaApp({
  progress: {
    delay: 250,
    color: '#ffa500',
    includeCSS: true,
    showSpinner: false,
  },

  title: title => `${title} - ${appName}`,

  resolve: name => pages[`./Pages/${name}.vue`],

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

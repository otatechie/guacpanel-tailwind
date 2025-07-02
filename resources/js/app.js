import { createApp, h } from "vue";
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy-js";
import NProgress from "nprogress";
import "nprogress/nprogress.css";
import "../css/app.css";
import { initializeTheme } from "./utils/themeInit";
import InstantSearch from "vue-instantsearch/vue3/es";

// Initialize theme before Vue app starts
initializeTheme();

// Layouts
import Default from "./Layouts/Default.vue";
import Auth from "./Layouts/Auth.vue";
import Public from "./Layouts/Public.vue";

// NProgress configuration
router.on("start", () => NProgress.start());
router.on("finish", () => NProgress.done());
router.on("error", () => NProgress.done());

createInertiaApp({
    progress: {
        delay: 250,
        color: "#ffa500",
        includeCSS: true,
        showSpinner: true,
    },

    title: (title) => `${title} - GuacPanel`,

    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin).use(ZiggyVue);
        app.use(InstantSearch);

        const globalComponents = {
            Link,
            Default,
            Auth,
            Public,
        };

        Object.entries(globalComponents).forEach(([name, component]) => {
            app.component(name, component);
        });

        app.mount(el);

        return app;
    },
});

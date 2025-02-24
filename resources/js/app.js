import { createApp, h } from "vue";
import { createPinia } from 'pinia';
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy-js";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

// Layouts
import Default from "./Layouts/Default.vue";
import Auth from "./Layouts/Auth.vue";

// NProgress configuration
router.on("start", () => NProgress.start());
router.on("finish", () => NProgress.done());
router.on("error", () => NProgress.done());

const pinia = createPinia();

createInertiaApp({
    progress: {
        delay: 250,
        color: "#ffa500",
        includeCSS: true,
        showSpinner: true,
    },

    title: (title) => `${title} - Paxfund`,

    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(pinia)
            .use(ZiggyVue);

        const globalComponents = {
            Link,
            Default,
            Auth
        };

        Object.entries(globalComponents).forEach(([name, component]) => {
            app.component(name, component);
        });

        app.mount(el);

        return app;
    },
});

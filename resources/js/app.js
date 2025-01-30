import { createApp, h } from "vue";
import { createPinia } from 'pinia';
import { createInertiaApp, Link, router } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy-js";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

// Layouts
import Default from "./Layouts/Default.vue";
import BaseLayout from "./Layouts/BaseLayout.vue";
import Auth from "./Layouts/Auth.vue";

// Media Library
import "@spatie/media-library-pro-styles";
import { MediaLibraryAttachment } from 'media-library-pro-vue3-attachment';
import { MediaLibraryCollection } from 'media-library-pro-vue3-collection';

// DataTables imports
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-dt';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import 'datatables.net-buttons';
import 'datatables.net-autofill-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-colreorder-dt';
import 'datatables.net-fixedheader-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-select-dt';

// Initialize DataTables
DataTable.use(DataTablesCore);
window.JSZip = jszip;
window.pdfMake = pdfmake;

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
            BaseLayout,
            Auth,
            MediaLibraryAttachment,
            MediaLibraryCollection,
            DataTable,
        };

        Object.entries(globalComponents).forEach(([name, component]) => {
            app.component(name, component);
        });

        app.mount(el);

        return app;
    },
});

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './router'; 
import '../css/app.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');

        if (!pages[`./Pages/${name}.vue`]) {
            throw new Error(`Page not found: ${name}`);
        }

        return pages[`./Pages/${name}.vue`]().then(module => module.default);
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(routes) 
            .mount(el);
    },
});

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createI18n } from 'vue-i18n';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ar from './locales/ar.json';
import en from './locales/en.json';

import '../css/app.css';

const i18n = createI18n({
    legacy: false,
    locale: document.documentElement.lang || 'ar',
    fallbackLocale: 'en',
    messages: { ar, en },
});

createInertiaApp({
    title: (title) => title ? `${title} — Doctorato` : 'Doctorato — نظام إدارة العيادات الطبية المتكامل',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: { color: '#C4A265', showSpinner: true },
});

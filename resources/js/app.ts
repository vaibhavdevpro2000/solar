import '../css/app.css';
import $ from 'jquery';
window.$ = window.jQuery = $; // Now TypeScript knows these exist

import { router } from '@inertiajs/vue3'

router.on('finish', () => {
    // Re-initialize jQuery plugins here if they disappear after navigation
    $('.select2').select2(); 
})

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Optional: Test if it's working
$(document).ready(() => {
    console.log('jQuery is loaded and ready with Inertia!');
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
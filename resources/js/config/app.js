import './bootstrap';
import '../../css/app.css';
import '../../scss/app.scss';
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.js";

// Importa o CSS do SweetAlert2
import 'sweetalert2/dist/sweetalert2.min.js';
import 'sweetalert2/dist/sweetalert2.all.min.js';
import 'sweetalert2/dist/sweetalert2.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../../vendor/tightenco/ziggy/dist/vue.m';
import ComponentsMap from '../componentsJs/components.js';
import { createPinia } from 'pinia';

import { registerSW } from 'virtual:pwa-register'; // <-- aqui entra o PWA!

const appName = (import.meta.env.VITE_APP_NAME || 'Tocare Music').replace(/[_-]/g, ' ');

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`../../PagesVuejs/${name}.vue`, import.meta.glob('../../PagesVuejs/**/*.vue')),

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(createPinia());

        // Configuração de eventos passivos
        app.config.globalProperties.$eventOptions = {
            passive: true
        };

        // chamada dos componentes
        Object.entries(ComponentsMap).forEach(([name, component]) => {
            app.component(name, component);
        });

        app.mount(el);
    },
    progress: {
        color: '#FDA543',
    },
});

// Adiciona meta tags para mobile
const meta = document.createElement('meta');
meta.name = 'viewport';
meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
document.head.appendChild(meta);

// Substitui o registro manual de SW pelo recomendado do vite-plugin-pwa
const updateSW = registerSW({
  onNeedRefresh() {
    // Aqui você pode exibir um popup ou um toast para o usuário
    console.log('Nova versão disponível. Atualize o app!');
  },
  onOfflineReady() {
    console.log('App pronto para funcionar offline!');
  }
});

const preloadSongs = async () => {
  if (!navigator.onLine) {
    return;
  }

  try {
    const response = await fetch('/offline/songs');
    if (!response.ok) {
      return;
    }

    const data = await response.json();
    const songs = data?.songs ?? [];

    await Promise.all(
      songs.map((song) => fetch(`/offline/songs/${song.id}`))
    );
  } catch (error) {
    console.warn('Falha ao pré-carregar músicas para offline.', error);
  }
};

preloadSongs();

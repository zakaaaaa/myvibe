import { createApp } from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import "bootstrap/dist/js/bootstrap.bundle.min.js"
import firebasePlugin from './plugins/firebase'

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { App as CapacitorApp } from '@capacitor/app';
import { Capacitor } from '@capacitor/core';

import vue3GoogleLogin from 'vue3-google-login'

library.add(fas, far);

createApp(App).component('fa', FontAwesomeIcon).use(vue3GoogleLogin, {
    clientId: '12107287082-c6tcv5n3o3l2ve4pnjk14qkh9350ecpt.apps.googleusercontent.com'
}).use(firebasePlugin).use(store).use(router).mount('#myVibeApps');

// Disable text selection on native app
if (Capacitor.isNativePlatform()) {
    document.body.classList.add('native-app');
}

CapacitorApp.addListener('appUrlOpen', (event) => {
    const url = new URL(event.url);
    const path = url.pathname;
    if (path) {
        router.push(path).catch((err) => {
            console.error('Router error:', err);
        });
    }
});
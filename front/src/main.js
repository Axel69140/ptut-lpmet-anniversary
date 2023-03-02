import { createApp } from 'vue';
import App from './App.vue';
import { router } from './routes';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import store from './store';
import jquery from 'jquery'

createApp(App).use(router).use(store).use(jquery).mount('#app');

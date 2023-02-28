import { createApp } from 'vue';
import App from './App.vue';
import { router } from './routes';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import $ from 'jquery'

createApp(App).use(router).mount('#app');

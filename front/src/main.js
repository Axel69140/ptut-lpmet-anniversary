import { createApp } from 'vue';
import App from './App.vue';
import { router } from './routes';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import store from './store';
import jquery from 'jquery'

//createApp(App).use(router).use(store).use(jquery).mount('#app');
const app = createApp(App);

app.use(router).use(store).use(jquery);

app.component('EasyDataTable', Vue3EasyDataTable);

app.mount('#app');
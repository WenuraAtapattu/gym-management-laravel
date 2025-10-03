import './bootstrap';
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

// Import Bootstrap JS
import 'bootstrap';

const app = createApp({});
app.component('example-component', ExampleComponent);
app.mount('#app');

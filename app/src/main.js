import Vue from "vue";
import App from "./App.vue";
import { BootstrapVue } from 'bootstrap-vue'
import Store from './Store'

// Import css
import 'bootstrap-vue/dist/bootstrap-vue.css'
import './sass/main.scss'

// Turn off production tip in console
Vue.config.productionTip = false;

// Register extensions
Vue.use(BootstrapVue)
Vue.prototype.$store = Store

// Render Vue
new Vue({
  render: h => h(App)
}).$mount("#fg-listings-app");

import Vue from "vue"
import VueResource from "vue-resource"
import VueMoment from "vue-moment"
import Moment from "moment"
import App from "./App.vue"
import vuetify from "./plugins/vuetify";
import "@mdi/font/css/materialdesignicons.css"
import "typeface-open-sans"
import i18n from "./plugins/i18n"
import "./plugins/registerServiceWorker"

Vue.use(VueResource);
Vue.use(VueMoment, {moment: Moment});

Vue.config.productionTip = false;
Vue.http.options.root = process.env.VUE_APP_API_ROOT;

new Vue({
    i18n,
    vuetify,
    render: h => h(App)
}).$mount("#app");

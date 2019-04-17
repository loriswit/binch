import "@babel/polyfill"
import Vue from "vue"
import VueResource from "vue-resource"
import VueMoment from "vue-moment"
import App from "./App.vue"

import "./plugins/vuetify"
import i18n from "./plugins/i18n"
import "./plugins/registerServiceWorker"

Vue.use(VueResource);
Vue.use(VueMoment, {
    moment: require("moment")
});

Vue.config.productionTip = false;
Vue.http.options.root = process.env.VUE_APP_API_ROOT;

new Vue({
    i18n,
    render: h => h(App)
}).$mount("#app");

<template>
  <v-app>
    <Header/>

    <v-main>
      <v-fade-transition>
        <Loading v-if="loading"/>
      </v-fade-transition>

      <Auth/>

      <v-slide-y-transition>
        <v-alert v-if="error" type="error" dense tile>
          {{ $t("error." + error.msg, [error.value]) }}
        </v-alert>
      </v-slide-y-transition>

      <v-slide-y-transition mode="out-in">
        <router-view class="main-component"/>
      </v-slide-y-transition>
    </v-main>
  </v-app>
</template>

<script>
import Header from "@/components/Header"
import Loading from "@/components/Loading"
import Auth from "@/components/Auth";

import {mapGetters, mapMutations, mapState} from "vuex"

export default {
    name: "App",
    components: {
        Auth,
        Header,
        Loading,
    },
    computed: {
        ...mapState(["locale", "error", "title"]),
        ...mapGetters(["loading"])
    },
    methods: {
        ...mapMutations(["clearError"])
    },
    watch: {
        "$route": "clearError",
        title(value) {
            document.title = process.env.VUE_APP_TITLE;
            if (value)
                document.title = value + " - " + document.title;
        }
    },
    created() {
        if (Object.keys(this.$i18n.messages).includes(this.locale)) {
            this.$i18n.locale = this.locale;
            this.$moment.locale(this.locale);
        }
    }
}
</script>

<style scoped>
#app {
  font-size: 14px;
  font-family: "Open Sans", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.main-component {
  position: absolute;
  width: 100%;
  min-height: 100%;
}
</style>

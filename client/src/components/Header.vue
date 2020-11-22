<template>
  <header>
    <v-navigation-drawer persistent v-model="showDrawer" clipped fixed app width="300">
      <v-list>
        <template v-if="isGroupPage">
          <v-subheader>
            <span class="list-heading">{{ group.name }}</span>
          </v-subheader>
          <v-list-item v-for="(item, i) in groupItems" :key="'group-' + i"
                       :to="'/group/' + $route.params.id + item.page">
            <v-list-item-action>
              <v-icon>mdi-{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t("header.group." + item.name) }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
        </template>

        <template v-if="recent.length !== 0">
          <v-subheader>{{ $t("header.recent.title") }}</v-subheader>
          <v-list-item v-for="(group, i) in recent" :key="'recent-' + i"
                       :to="'/group/' + group.id">
            <v-list-item-action>
              <v-icon>mdi-account-group</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ group.name }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
        </template>

        <v-subheader>{{ $t("header.navigation.title") }}</v-subheader>
        <v-list-item v-for="(item, i) in mainItems" :key="'main-' + i"
                     :to="item.page">
          <v-list-item-action>
            <v-icon>mdi-{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ $t("header.navigation." + item.name) }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>

        <v-subheader>{{ $t("header.language.title") }}</v-subheader>
        <v-list-item v-for="(locale, i) in locales" :key="'locale-' + i"
                     @click="setLocale(locale.id)">
          <v-list-item-action>
            <v-icon>mdi-translate</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ locale.name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar color="primary" clipped-left flat dark>
      <v-app-bar-nav-icon @click.stop="showDrawer = !showDrawer" :disabled="loading"></v-app-bar-nav-icon>
      <v-toolbar-title>
        {{ isGroupPage ? group.name : appTitle }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <span v-if="showDev" class="badge dev">dev</span>
      <span class="badge beta">beta</span>

      <v-btn v-if="$route.params.id && refresh"
             @click="refresh"
             :disabled="loading"
             icon>
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
      <img v-else src="@/assets/images/logo.svg" alt="Logo" class="logo">
    </v-app-bar>
  </header>
</template>

<script>
import {mapGetters, mapState} from "vuex";

export default {
    name: "Header",
    data: () => ({
        appTitle: process.env.VUE_APP_TITLE,
        showDrawer: false,
        mainItems: [{
            icon: "home",
            name: "home",
            page: "/home"
        }, {
            icon: "account-multiple",
            name: "goto",
            page: "/join"
        }, {
            icon: "account-multiple-plus",
            name: "new",
            page: "/new"
        }],
        groupItems: [{
            icon: "history",
            name: "rounds",
            page: "/rounds"
        }, {
            icon: "pencil",
            name: "edit",
            page: "/edit"
        }],
        locales: [{
            name: "English",
            id: "en"
        }, {
            name: "Français",
            id: "fr"
        }],
        showDev: process.env.NODE_ENV === "development"
    }),
    computed: {
        ...mapState(["group", "recent", "refresh"]),
        ...mapGetters(["loading"]),

        isGroupPage() {
            return this.$route.params.id && this.group;
        }
    },
    methods: {
        setLocale(locale) {
            this.showDrawer = false;
            this.$i18n.locale = locale;
            this.$moment.locale(locale);
            localStorage.setItem("locale", locale);
        }
    },
    watch: {
        loading() {
            this.showDrawer = false;
        }
    },
}
</script>

<style scoped>
.logo {
  width: 28px;
  margin-left: 6px;
  margin-right: 2px;
}

.badge {
  font-weight: bold;
  font-size: 0.8em;
  text-transform: uppercase;
  background-color: indianred;
  padding: 3px 5px;
  margin-right: 10px;
  border-radius: 5px;
  vertical-align: top;
}

.beta {
  background-color: indianred;
}

.dev {
  background-color: cornflowerblue;
}

.list-heading {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

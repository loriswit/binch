<template>
  <header>
    <v-navigation-drawer persistent v-model="showDrawer" clipped fixed app width="300">
      <v-list>
        <template v-if="group">
          <v-subheader>
            <span class="list-heading">{{ group.name }}</span>
          </v-subheader>
          <v-list-item v-for="(item, i) in groupItems" :key="'group-' + i"
                       @click="onClick(item.page)">
            <v-list-item-action>
              <v-icon>mdi-{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t("header.group." + item.name) }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
        </template>

        <template v-if="recentItems.length !== 0">
          <v-subheader>{{ $t("header.recent.title") }}</v-subheader>
          <v-list-item v-for="(group, i) in recentItems" :key="'recent-' + i"
                       @click="$emit('group', group.id)">
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
                     @click="onClick(item.page)">
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
      <v-app-bar-nav-icon @click.stop="showDrawer = !showDrawer" :disabled="updating"></v-app-bar-nav-icon>
      <v-toolbar-title>
        {{ group ? group.name : "BinchApp" }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <span v-if="showDev" class="badge dev">dev</span>
      <span class="badge beta">beta</span>
      <v-scale-transition>
        <v-btn v-if="group" :disabled="updating" icon>
          <v-icon @click="$emit('update')">mdi-refresh</v-icon>
        </v-btn>
      </v-scale-transition>
    </v-app-bar>
  </header>
</template>

<script>
export default {
    name: "Header",
    data() {
        return {
            showDrawer: false,
            mainItems: [{
                icon: "home",
                name: "home",
                page: "Home"
            }, {
                icon: "account-multiple",
                name: "goto",
                page: "Goto"
            }, {
                icon: "account-multiple-plus",
                name: "new",
                page: "New"
            }],
            groupItems: [{
                icon: "history",
                name: "rounds",
                page: "Rounds"
            }, {
                icon: "pencil",
                name: "edit",
                page: "Edit"
            }],
            locales: [{
                name: "English",
                id: "en"
            }, {
                name: "Français",
                id: "fr"
            }],
            showDev: process.env.NODE_ENV === "development"
        }
    },
    props: {
        group: Object,
        groupID: String,
        updating: Boolean,
        recentGroups: Array
    },
    computed: {
        recentItems() {
            if (!this.recentGroups)
                return [];

            if (!this.group)
                return this.recentGroups;

            return this.recentGroups.filter(e => e.id !== this.groupID);
        }
    },
    methods: {
        onClick(page) {
            this.showDrawer = false;
            this.$emit("page", page);
        },
        setLocale(locale) {
            this.showDrawer = false;
            this.$i18n.locale = locale;
            this.$moment.locale(locale);
            localStorage.setItem("locale", locale);
        }
    },
    watch: {
        updating() {
            this.showDrawer = false;
        }
    }
}
</script>

<style scoped>
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
  background-color: mediumseagreen;
}

.list-heading {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

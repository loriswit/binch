<template>
  <header>
    <v-navigation-drawer persistent v-model="showDrawer" clipped fixed app>
      <v-list>
        <template v-if="group">
          <v-subheader>
            <span class="list-heading">{{ group.name }}</span>
          </v-subheader>
          <v-list-tile v-for="(item, i) in groupItems" :key="'group-' + i"
                       @click="onClick(item.page)">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ $t("header.group." + item.name) }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
          <v-divider></v-divider>
        </template>

        <v-subheader>{{ $t("header.recent.title") }}</v-subheader>
        <v-list-tile v-for="(group, i) in recentGroups" :key="'recent-' + i"
                     v-if="group.id !== groupID"
                     @click="$emit('group', group.id)">
          <v-list-tile-action>
            <v-icon>group</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ group.name }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-divider></v-divider>

        <v-subheader>{{ $t("header.navigation.title") }}</v-subheader>
        <v-list-tile v-for="(item, i) in mainItems" :key="'main-' + i"
                     @click="onClick(item.page)">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ $t("header.navigation." + item.name) }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        <v-divider></v-divider>

        <v-subheader>{{ $t("header.language.title") }}</v-subheader>
        <v-list-tile v-for="(locale, i) in locales" :key="'locale-' + i"
                     @click="setLocale(locale.id)">
          <v-list-tile-action>
            <v-icon>language</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ locale.name }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>

    <v-toolbar color="primary" clipped-left flat dark app>
      <v-toolbar-side-icon @click.stop="showDrawer = !showDrawer" :disabled="updating"></v-toolbar-side-icon>
      <v-toolbar-title>
        {{ group ? group.name : "BinchApp" }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <span v-if="showDev" class="badge dev">dev</span>
      <span class="badge beta">beta</span>
      <v-scale-transition>
        <v-btn v-if="group" :disabled="updating" icon>
          <v-icon @click="$emit('update')">refresh</v-icon>
        </v-btn>
      </v-scale-transition>
    </v-toolbar>
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
                icon: "group",
                name: "goto",
                page: "Goto"
            }, {
                icon: "group_add",
                name: "new",
                page: "New"
            }],
            groupItems: [{
                icon: "history",
                name: "rounds",
                page: "Rounds"
            }, {
                icon: "edit",
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

<template>
  <header>
    <v-navigation-drawer persistent v-model="showDrawer" clipped fixed app>
      <v-list>
        <template v-if="group">
          <v-subheader>
            <span class="list-heading">{{ group.name }}</span>
          </v-subheader>
          <v-list-tile v-for="(item, i) in groupItems" :key="'group-' + i" @click="onClick(item.page)">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>{{ item.title }}</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
          <v-divider></v-divider>
        </template>

        <v-subheader>Navigation</v-subheader>
        <v-list-tile v-for="(item, i) in mainItems" :key="'main-' + i" @click="onClick(item.page)">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
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
                title: "Home",
                page: "Home"
            }, {
                icon: "group",
                title: "Go to group",
                page: "Goto"
            }, {
                icon: "group_add",
                title: "New group",
                page: "New"
            }],
            groupItems: [{
                icon: "edit",
                title: "Edit group",
                page: "Edit"
            }, {
                icon: "history",
                title: "Rounds history",
                page: "Rounds"
            }],
            showDev: process.env.NODE_ENV === "development"
        }
    },
    props: {
        group: Object,
        updating: Boolean
    },
    methods: {
        onClick(page) {
            this.showDrawer = false;
            this.$emit("page", page);
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

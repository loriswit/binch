<template>
  <v-app>
    <Header :group="group"
            :groupID="groupID"
            :updating="updating"
            :recentGroups="recentGroups"
            @page="setPage"
            @group="setGroup"
            @update="updateGroup"/>

    <v-content>
      <v-fade-transition>
        <Loading v-if="updating"/>
      </v-fade-transition>

      <Auth :groupID="groupID" :active="needAuth" @close="needAuth = false"/>

      <v-slide-y-transition>
        <Error v-if="error" :error="error"/>
      </v-slide-y-transition>

      <v-slide-y-transition mode="out-in">
        <component class="main-component"
                   v-if="page"
                   :is="page"
                   :group="group"
                   :groupID="groupID"
                   :payer="payer"
                   @page="setPage"
                   @group="setGroup"
                   @update="updateGroup"
                   @edit="editGroup"
                   @payer="setPayer"
                   @pay="addRound"
                   @delete="deleteRounds"/>
      </v-slide-y-transition>
    </v-content>
  </v-app>
</template>

<script>
import Header from "./components/Header"
import Home from "./components/pages/Home"
import Group from "./components/pages/Group"
import Error from "./components/Error"
import Loading from "./components/Loading"
import Goto from "./components/pages/Goto"
import Edit from "./components/pages/Edit"
import New from "@/components/pages/New";
import Auth from "@/components/Auth";

import Storage from "@/storage"
import Pay from "@/components/pages/Pay";
import Rounds from "@/components/pages/Rounds";

export default {
    name: "App",
    components: {
        Rounds,
        Pay,
        Auth,
        New,
        Home,
        Group,
        Error,
        Header,
        Loading,
        Goto,
        Edit
    },
    data() {
        return {
            page: null,
            groupID: null,
            group: null,
            payer: null,
            updating: false,
            needAuth: false,
            error: null,
            recentGroups: null
        }
    },
    methods: {
        setPage(page) {
            this.error = null;
            this.page = page || "Home";
            localStorage.setItem("page", this.page);
        },
        updateGroup() {
            if (!this.groupID) {
                this.group = null;
                return;
            }

            this.updating = true;

            this.$http.get("group/" + this.groupID).then(response => {
                this.group = response.body;
                this.updating = false;
                this.error = null;

                if (this.recentGroups)
                    this.recentGroups = this.recentGroups.filter(e => e.id !== this.groupID);
                else
                    this.recentGroups = [];

                this.recentGroups.unshift({
                    id: this.groupID,
                    name: this.group.name
                });

                localStorage.setItem("recent", JSON.stringify(this.recentGroups));

                this.setPage("Group");
            }, response => {
                if (response.status === 404) {
                    this.updating = false;
                    scrollTo(0, 0);

                    if (this.page === "Group")
                        this.setPage("Home");

                    this.error = "The group '" + this.groupID + "' does not exist";
                    this.groupID = null;
                } else
                    this.onRequestFailed(response);
            });
        },
        editGroup(data) {
            this.updating = true;

            const newGroup = this.group === null;

            const options = {
                method: newGroup ? "POST" : "PATCH",
                url: newGroup ? "groups" : "group/" + this.groupID,
                body: data
            };

            if (!newGroup)
                this.addAuthHeader(options);

            this.$http(options).then(response => {
                if (newGroup)
                    this.setGroup(data.path);

                this.group = response.body;
                if (this.group.token)
                    Storage.putToken(this.groupID, this.group.token);

                this.updating = false;
                this.error = null;
                this.setPage("Group");
            }, response => this.onRequestFailed(response));
        },
        setGroup(id) {
            if (id) {
                this.groupID = id.toLowerCase();
                history.pushState("", "", "/" + this.groupID);
                localStorage.setItem("group", this.groupID);
            } else {
                this.groupID = null;
                history.pushState("", "", "/");
                localStorage.removeItem("group");
            }

            this.updateGroup();
        },
        setPayer(member) {
            this.payer = member;
            if (member)
                this.setPage("Pay");
        },
        addRound(data) {
            this.updating = true;

            const options = {
                method: "POST",
                url: "group/" + this.groupID + "/rounds",
                body: data
            };

            this.addAuthHeader(options);

            this.$http(options).then(() => {
                this.updating = false;
                this.error = null;
                this.updateGroup();
            }, response => this.onRequestFailed(response));
        },
        deleteRounds(rounds) {
            this.updating = true;

            for (const round of rounds) {
                const options = {
                    method: "PATCH",
                    url: "group/" + this.groupID + "/round/" + round.date,
                    body: {deleted: round.deleted}
                };

                this.addAuthHeader(options);

                this.$http(options).then(() => {
                    this.updating = false;
                    this.error = null;
                    this.updateGroup();
                }, response => this.onRequestFailed(response));
            }
        },
        onRequestFailed(response) {
            this.updating = false;
            scrollTo(0, 0);

            if (response.status === 0)
                this.error = navigator.onLine ? "Connexion to server failed." : "Not connected to internet.";
            else if (response.status === 401 || response.status === 403)
                this.needAuth = true;
            else
                this.error = "Request failed: " + (response.body.message || response.body.error);
        },
        addAuthHeader(options) {
            const token = Storage.getToken(this.groupID);
            if (token)
                options.headers = {
                    Authorization: "Bearer " + token
                };
        }
    },
    computed: {
        title() {
            return this.group ? this.group.name : null;
        }
    },
    created() {
        const recent = localStorage.getItem("recent");
        if (recent)
            this.recentGroups = JSON.parse(recent);

        const locale = localStorage.getItem("locale") || navigator.language;
        if (Object.keys(this.$i18n.messages).includes(locale)) {
            this.$i18n.locale = locale;
            this.$moment.locale(locale);
        }

        const path = location.pathname.split("/")[1];
        this.setPage(path ? "Group" : localStorage.getItem("page"));
        this.setGroup(path || localStorage.getItem("group"));
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

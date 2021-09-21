<template>
  <div id="members" v-if="group">
    <h1>{{ $t("members.title") }}</h1>
    <v-container>
      <v-row v-for="(_, i) in Math.ceil(members.length / 2)" :key="'row-' + i" justify="center">
        <v-col v-for="j in 2" :key="'col-' + j" cols="5">
          <router-link v-if="members.length >= i * 2 + j"
                       :to="'members/' + members[i * 2 + j - 1]" class="member">
            <v-icon>mdi-account</v-icon>
            <span>{{ members[i * 2 + j - 1] }}</span>
          </router-link>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import {mapActions, mapMutations, mapState} from "vuex";

export default {
    name: "Members",
    computed: {
        ...mapState(["group", "refresh"]),
        members() {
            return this.group.members.map(e => e.name);
        },
        member() {
            return this.$route.params.member;
        }
    },
    methods: {
        ...mapActions(["fetchGroup"]),
        ...mapMutations(["onRefresh"]),
    },
    created() {
        this.onRefresh(() => {
            this.fetchGroup(this.$route.params.id);
        });

        this.refresh();
    }
}
</script>

<style scoped>
h1 {
  text-align: center;
  font-weight: normal;
  font-size: 2em;
  margin: 20px 20px 0;
}

.member {
  display: flex;
  align-items: center;
  /*color: black;*/
  border-bottom: 1px whitesmoke solid;
  padding: 15px 6px;
  margin: 5px;
  text-decoration: none;
  font-size: 1.2em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.member:hover {
  background: whitesmoke;
}

.member .v-icon {
  font-size: 1em;
  margin-right: 8px;
}

.col {
  padding: 0;
  margin-left: 10px;
  margin-right: 0;
}

.col:last-child {
  margin-right: 10px;
}
</style>

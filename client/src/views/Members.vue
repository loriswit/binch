<template>
  <div id="members" v-if="group">
    <h1>{{ $t("members.title") }}</h1>
    <v-container>
      <v-row v-for="(_, i) in members.length / 2" :key="'row-' + i" justify="center">
        <v-col v-for="j in 2" :key="'col-' + j" cols="5">
          <v-card :to="'members/' + members[i * 2 + j - 1]"
                  class="member" color="primary" outlined>
            {{ members[i * 2 + j - 1] }}
          </v-card>
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
  color: white;
  padding: 10px;
  text-align: center;
  font-size: 1.2em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.col {
  margin-left: 15px;
  margin-right: 0;
}

.col:last-child {
  margin-right: 15px;
}
</style>

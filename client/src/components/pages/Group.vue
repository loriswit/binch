<template>
  <div id="group" v-if="group">
    <h1>{{ $t("group.title") }}</h1>
    <v-list v-if="members.active.length" :two-line="valueType === 'delta'">
      <v-list-item class="member"
                   v-for="(member, i) in members.active" :key="i"
                   @click="$emit('payer', member.name)">
        <v-list-item-content class="tile-content">
          <v-list-item-title class="name">
            {{ member.name }}
          </v-list-item-title>
          <v-list-item-subtitle v-if="valueType === 'delta'">
            <span class="paid">+{{ member.paid.toFixed(2) }}</span>
            <span class="consumed">-{{ member.consumed.toFixed(2) }}</span>
          </v-list-item-subtitle>
        </v-list-item-content>


        <v-list-item-action class="percent" v-if="valueType === 'ratio'">
          {{ member.percent.toFixed(1) }} %
        </v-list-item-action>
        <v-list-item-action class="percent" v-else>
          {{ (member.delta >= 0 ? "+ " : "- ") + Math.abs(member.delta).toFixed(2) }}
        </v-list-item-action>

        <v-list-item-action>
          <v-icon>mdi-chevron-right</v-icon>
        </v-list-item-action>
      </v-list-item>
    </v-list>

    <template v-if="members.new.length">
      <h2>{{ $t("group.new") }}</h2>
      <v-list>
        <v-list-item class="member"
                     v-for="(member, i) in members.new" :key="i"
                     @click="$emit('payer', member)">
          <v-list-item-content>
            <v-list-item-title>{{ member }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-icon>mdi-chevron-right</v-icon>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </template>

    <v-bottom-navigation background-color="primary" grow dark fixed v-model="valueType">
      <v-btn depressed value="ratio">
        <span>{{ $t("group.ratio") }}</span>
        <v-icon>mdi-percent</v-icon>
      </v-btn>
      <v-btn depressed value="delta">
        <span>{{ $t("group.balance") }}</span>
        <v-icon>mdi-scale-balance</v-icon>
      </v-btn>
    </v-bottom-navigation>
  </div>
</template>

<script>
export default {
    name: "Group",
    data() {
        return {
            valueType: "ratio"
        }
    },
    props: {
        group: Object
    },
    computed: {
        members() {
            const members = {
                active: [],
                new: []
            };

            if (!this.group || !this.group.members)
                return members;

            for (const member of this.group.members) {
                const percent = member.paid / member.consumed * 100;
                if (isNaN(percent)) {
                    members.new.push(member.name);
                    continue;
                }

                const delta = (member.paid - member.consumed) / 100;

                members.active.push({
                    name: member.name,
                    percent: percent,
                    paid: member.paid / 100,
                    consumed: member.consumed / 100,
                    delta: delta
                });
            }

            if (this.valueType === "ratio")
                members.active.sort(function (a, b) {
                    return a.percent - b.percent;
                });

            else
                members.active.sort(function (a, b) {
                    return a.delta - b.delta;
                });

            return members;
        }
    },
    methods: {},
    created() {
        setInterval(() => {
            this.$emit("update");
        }, 60000);
    },
    beforeCreate() {
        scrollTo(0, 0);
    }
}
</script>

<style scoped>
#group {
  margin-bottom: 80px;
}

h1 {
  font-weight: normal;
  font-size: 2em;
  margin: 20px;
}

h2 {
  font-weight: normal;
  font-size: 1.5em;
  margin: 20px;
}

.v-list {
  padding: 0;
  border-top: 1px solid whitesmoke;
}

.member {
  border-bottom: 1px solid whitesmoke;
}

.tile-content {
  justify-content: space-evenly;
}

.member:nth-child(1) .name,
.member:nth-child(1) .percent {
  color: red;
  font-size: 1.8em;
}

.member:nth-child(2) .name,
.member:nth-child(2) .percent {
  color: brown;
  font-size: 1.6em;
}

.member:nth-child(3) .name,
.member:nth-child(3) .percent {
  color: darkred;
  font-size: 1.4em;
}

.percent {
  font-weight: bold;
  font-size: 1.15em;
}

.paid {
  color: mediumseagreen;
}

.consumed {
  color: indianred;
  margin-left: 10px;
}
</style>

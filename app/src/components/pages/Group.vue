<template>
  <div v-if="group">
    <h1>Who pays the round?</h1>
    <v-list v-if="members.active.length">
      <v-list-tile class="member"
                   v-for="(member, i) in members.active" :key="i"
                   @click="$emit('payer', member.name)">
        <v-list-tile-content>
          <v-list-tile-title class="name">
            {{ member.name }}
          </v-list-tile-title>
        </v-list-tile-content>
        <v-list-tile-action class="percent">
          {{ member.percent.toFixed(1) }} %
        </v-list-tile-action>
        <v-list-tile-action>
          <v-icon>keyboard_arrow_right</v-icon>
        </v-list-tile-action>
      </v-list-tile>
    </v-list>

    <template v-if="members.new.length">
      <h2>New members</h2>
      <v-list>
        <v-list-tile v-for="(member, i) in members.new" :key="i"
                     @click="$emit('payer', member)">
          <v-list-tile-content>
            <v-list-tile-title>{{ member }}</v-list-tile-title>
          </v-list-tile-content>
          <v-list-tile-action>
            <v-icon>keyboard_arrow_right</v-icon>
          </v-list-tile-action>
        </v-list-tile>
      </v-list>
    </template>
  </div>
</template>

<script>
export default {
    name: "Group",
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

                members.active.push({
                    name: member.name,
                    percent: percent
                });
            }
            members.active.sort(function (a, b) {
                return a.percent - b.percent;
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
}

.member {
  border-bottom: 1px solid whitesmoke;
}

.member:nth-child(1) .name,
.member:nth-child(1) .percent {
  color: red;
  font-size: 1.6em;
}

.member:nth-child(2) .name,
.member:nth-child(2) .percent {
  color: brown;
  font-size: 1.4em;
}

.member:nth-child(3) .name,
.member:nth-child(3) .percent {
  color: darkred;
  font-size: 1.2em;
}

.percent {
  font-weight: bold;
}
</style>

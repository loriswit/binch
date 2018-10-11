<template>
  <v-form @submit.prevent="$emit('delete', rounds)">
    <h1>Rounds history</h1>
    <v-list>
      <div v-for="(round, i) in rounds" :key="i">

        <v-divider v-if="i === 0 || !sameDay(round.date, rounds[i - 1].date)"></v-divider>
        <v-subheader v-if="i === 0 || !sameDay(round.date, rounds[i - 1].date)">
          {{ formatDate(round.date) }}
        </v-subheader>

        <v-list-tile class="round"
                     :class="{ deleted: round.deleted }">

          <v-list-tile-content>
            <v-list-tile-title class="round-title">
              {{ round.payer }} payed
              {{ drinksCount(round) }} drinks at
              {{ (parseFloat(round.price) / 100).toFixed(2) }}
            </v-list-tile-title>
            <v-list-tile-sub-title>
              <v-icon class="time-icon">access_time</v-icon>
              <span>{{ formatTime(round.date) }}</span>
            </v-list-tile-sub-title>

          </v-list-tile-content>
          <v-list-tile-action>
            <v-btn icon @click="round.deleted = !round.deleted">
              <v-icon :color="round.deleted ? 'red' : 'primary'">
                {{ round.deleted ? "restore_from_trash" : "delete" }}
              </v-icon>
            </v-btn>
          </v-list-tile-action>

        </v-list-tile>
      </div>
    </v-list>

    <Buttons text="Save"
             valid
             @cancel="$emit('page', 'Group')"/>
  </v-form>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons";
import VListTileAction from "vuetify/src/components/VList/VListTileAction";

export default {
    name: "Rounds",
    components: {VListTileAction, Buttons},
    data() {
        return {
            rounds: []
        }
    },
    props: {
        groupID: String
    },
    methods: {
        drinksCount(round) {
            return Object.values(round.consumers).reduce((a, b) => a + b);
        },
        sameDay(date1, date2) {
            const a = new Date(date1);
            const b = new Date(date2);
            return a.getFullYear() === b.getFullYear() &&
                a.getMonth() === b.getMonth() &&
                a.getDate() === b.getDate();
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        },
        formatTime(date) {
            return new Date(date).toLocaleTimeString("en-US", {
                hour12: false,
                hour: "numeric",
                minute: "numeric"
            });
        }
    },
    created() {
        this.$http.get("group/" + this.groupID + "/rounds").then(response => {
            this.rounds = response.body.slice().reverse();
        });
    }
}
</script>

<style scoped>
h1 {
  font-weight: normal;
  font-size: 2em;
  margin: 20px;
}

.round {
  padding-bottom: 10px;
}

.v-subheader {
  justify-content: center;
}

.time-icon {
  font-size: 0.9em;
  line-height: 1.5;
  margin-right: 4px;
}

button .v-icon {
  font-size: 2.5em;
}

.deleted {
  background-color: whitesmoke;
}

.deleted .round-title {
  color: grey;
  text-decoration: line-through;
}

</style>

<template>
  <div>
    <v-dialog v-if="selectedRound" v-model="dialog" width="500">
      <v-card>
        <v-card-title class="headline" primary-title>
          Round payed by {{ selectedRound.payer }}
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-tile v-for="(amount, name) in selectedRound.consumers" :key="name">
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ name }}
                </v-list-tile-title>
              </v-list-tile-content>
              <v-list-tile-action class="amount">
                {{ amount }} &times; {{ (parseFloat(selectedRound.price) / 100).toFixed(2) }}
              </v-list-tile-action>
            </v-list-tile>

            <v-divider></v-divider>

            <v-list-tile>
              <v-list-tile-content>
                <v-list-tile-title class="total">
                  Total
                </v-list-tile-title>
              </v-list-tile-content>
              <v-list-tile-action class="amount">
                {{ selectedRound.drinksCount }}
                &times; {{ (parseFloat(selectedRound.price) / 100).toFixed(2) }}
                = {{ (parseFloat(selectedRound.totalPrice) / 100).toFixed(2) }}
              </v-list-tile-action>
            </v-list-tile>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click="dialog = false">
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-form @submit.prevent="$emit('delete', rounds)">
      <h1>Rounds history</h1>
      <v-list>
        <div v-for="(round, i) in rounds" :key="i">

          <v-divider v-if="i === 0 || !sameDay(round.date, rounds[i - 1].date)"></v-divider>
          <v-subheader v-if="i === 0 || !sameDay(round.date, rounds[i - 1].date)">
            {{ round.date | moment("dddd, MMMM Do YYYY") }}
          </v-subheader>

          <v-list-tile class="round"
                       :class="{ deleted: round.deleted }">

            <v-list-tile-content>
              <v-list-tile-title class="round-title">
                <span class="round-payer">{{ round.payer }}</span>
                <v-icon class="arrow-icon">arrow_right</v-icon>
                <span class="round-drinks">
                  {{ drinksCount(round) }} &times; {{ (parseFloat(round.price) / 100).toFixed(2) }}
                </span>
              </v-list-tile-title>
              <v-list-tile-sub-title>
                <v-icon class="time-icon">access_time</v-icon>
                <span>{{ round.date | moment("H:mm") }}</span>
              </v-list-tile-sub-title>
            </v-list-tile-content>

            <v-list-tile-action>
              <v-btn icon @click="showConsumers(round)">
                <v-icon color="primary">info</v-icon>
              </v-btn>
            </v-list-tile-action>

            <v-list-tile-action>
              <v-btn icon @click="round.deleted = !round.deleted">
                <v-icon :color="round.deleted ? 'red' : 'primary'">
                  {{ round.deleted ? "restore_from_trash" : "delete" }}
                </v-icon>
              </v-btn>
            </v-list-tile-action>

          </v-list-tile>
        </div>
        <v-divider></v-divider>
      </v-list>

      <Buttons text="Save"
               valid
               @cancel="$emit('page', 'Group')"/>
    </v-form>
  </div>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons"
import moment from "moment"

export default {
    name: "Rounds",
    components: {Buttons},
    data() {
        return {
            rounds: [],
            selectedRound: null,
            dialog: false
        }
    },
    props: {
        groupID: String
    },
    methods: {
        showConsumers(round) {
            this.selectedRound = round;
            this.selectedRound.drinksCount = this.drinksCount(round);
            this.selectedRound.totalPrice = this.selectedRound.drinksCount * round.price;
            this.dialog = true;
        },
        drinksCount(round) {
            return Object.values(round.consumers).reduce((a, b) => a + b);
        },
        sameDay(date1, date2) {
            return moment(date1).isSame(date2, "day");
        },
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

.v-list {
  padding: 0;
}

.total {
  font-weight: bold;
}

.amount {
  text-align: right;
  font-weight: bold;
}

.round {
  padding-bottom: 10px;
}

.v-subheader {
  justify-content: center;
}

.arrow-icon {
  color: lightgrey;
  line-height: 0.9;
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

.deleted .round-title .round-payer,
.deleted .round-title .round-drinks {
  color: grey;
  text-decoration: line-through;
}

</style>

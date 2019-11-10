<template>
  <div>
    <v-dialog v-if="selectedRound" v-model="dialog" width="500">
      <v-card>
        <v-card-title class="headline" primary-title>
          {{ $t("rounds.dialog.title") }} {{ selectedRound.payer }}
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item v-for="(amount, name) in selectedRound.consumers" :key="name">
              <v-list-item-content>
                <v-list-item-title>
                  {{ name }}
                </v-list-item-title>
              </v-list-item-content>
              <v-list-item-action class="amount">
                {{ amount }} &times; {{ (parseFloat(selectedRound.price) / 100).toFixed(2) }}
              </v-list-item-action>
            </v-list-item>

            <v-divider></v-divider>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title class="total">
                  {{ $t("rounds.dialog.total") }}
                </v-list-item-title>
              </v-list-item-content>
              <v-list-item-action class="amount">
                {{ selectedRound.drinksCount }}
                &times; {{ (parseFloat(selectedRound.price) / 100).toFixed(2) }}
                = {{ (parseFloat(selectedRound.totalPrice) / 100).toFixed(2) }}
              </v-list-item-action>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="dialog = false">
            {{ $t("button.close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-form @submit.prevent="submit">
      <h1>{{ $t("rounds.title") }}</h1>
      <v-list>
        <div v-for="(round, i) in reversedRounds" :key="i">

          <v-divider v-if="i === 0 || !sameDay(round.date, reversedRounds[i - 1].date)"></v-divider>
          <v-subheader v-if="i === 0 || !sameDay(round.date, reversedRounds[i - 1].date)">
            {{ round.date | moment("dddd, LL") }}
          </v-subheader>

          <v-list-item :class="{ deleted: round.deleted }">

            <v-list-item-content class="round">
              <v-list-item-title class="round-title">
                <span class="round-payer">{{ round.payer }}</span>
                <v-icon class="arrow-icon">mdi-chevron-right</v-icon>
                <span class="round-drinks">
                  {{ drinksCount(round) }} &times; {{ (parseFloat(round.price) / 100).toFixed(2) }}
                </span>
              </v-list-item-title>
              <v-list-item-subtitle>
                <v-icon class="time-icon">mdi-clock-outline</v-icon>
                <span>{{ round.date | moment("H:mm") }}</span>
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn icon @click="showConsumers(round)">
                <v-icon large color="primary">mdi-information</v-icon>
              </v-btn>
            </v-list-item-action>

            <v-list-item-action>
              <v-btn icon @click="swapDeleted(rounds.length - i - 1)">
                <v-icon large :color="round.deleted ? 'red' : 'primary'">
                  mdi-delete{{ round.deleted ? "-restore" : "" }}
                </v-icon>
              </v-btn>
            </v-list-item-action>

          </v-list-item>
        </div>
        <v-divider></v-divider>
      </v-list>

      <Buttons :text="$t('button.save')" :valid="changedRounds.some(x => x)"/>
    </v-form>
  </div>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons"
import moment from "moment"
import {mapActions, mapState, mapMutations} from "vuex";

export default {
    name: "Rounds",
    components: {Buttons},
    data: () => ({
        changedRounds: [],
        selectedRound: null,
        dialog: false
    }),
    computed: {
        ...mapState(["rounds", "refresh"]),

        reversedRounds() {
            return this.rounds.slice().reverse();
        }
    },
    methods: {
        ...mapActions(["fetchRounds", "deleteRound", "fetchGroup"]),
        ...mapMutations(["onRefresh"]),

        async submit() {
            let requests = [];
            for (const round of this.rounds.filter((_, i) => this.changedRounds[i]))
                requests.push(this.deleteRound({
                    id: this.$route.params.id,
                    date: round.date,
                    deleted: round.deleted
                }));

            await Promise.all(requests);
            this.$router.go(-1);
        },

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
        swapDeleted(index) {
            this.rounds[index].deleted = !this.rounds[index].deleted;
            this.changedRounds[index] = !this.changedRounds[index];
        }
    },
    created() {
        this.onRefresh(() => {
            this.fetchGroup(this.$route.params.id);
            this.fetchRounds(this.$route.params.id);
        });

        this.refresh();
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
  padding: 0;
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

.deleted {
  background-color: whitesmoke;
}

.deleted .round-title .round-payer,
.deleted .round-title .round-drinks {
  color: grey;
  text-decoration: line-through;
}

</style>

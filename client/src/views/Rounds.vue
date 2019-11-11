<template>
  <div>
    <v-dialog v-model="dialog" width="500">
      <v-card v-if="selectedRound">
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
      <header>
        <h1>{{ $t("rounds.title") }}</h1>

        <v-menu
          v-model="filterMenu"
          :close-on-content-click="false"
          transition="slide-x-transition"
          offset-x
          left
          nudge-left="20"
          nudge-width="200"
        >
          <template v-slot:activator="{ on }">
            <v-btn color="primary" depressed fab small v-on="on">
              <v-icon>mdi-magnify</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-text-field
              ref="input"
              v-model="filter"
              :placeholder="$t('rounds.filter')"
              hide-details
              dense
              outlined
              clearable
            />
          </v-card>
        </v-menu>
      </header>

      <v-list v-infinite-scroll="loadMore">
        <div v-for="(round, i) in visibleRounds" :key="i">

          <v-divider v-if="i === 0 || !sameDay(round.date, visibleRounds[i - 1].date)"/>
          <v-subheader v-if="i === 0 || !sameDay(round.date, visibleRounds[i - 1].date)">
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
              <v-btn icon @click="swapDeleted(i)">
                <v-icon large :color="round.deleted ? 'red' : 'primary'">
                  mdi-delete{{ round.deleted ? "-restore" : "" }}
                </v-icon>
              </v-btn>
            </v-list-item-action>

          </v-list-item>
        </div>
        <v-divider></v-divider>
      </v-list>

      <h2 v-if="!loading && !filteredRounds.length">{{ $t("rounds.none") }}</h2>

      <div class="progress" v-if="visibleRounds.length < filteredRounds.length">
        <v-progress-circular color="primary" indeterminate/>
      </div>

      <Buttons :text="$t('button.save')" :valid="changedRounds.some(x => x)"/>
    </v-form>
  </div>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons"
import {mapActions, mapState, mapMutations, mapGetters} from "vuex";

export default {
    name: "Rounds",
    components: {Buttons},
    data: () => ({
        changedRounds: [],
        selectedRound: null,
        dialog: false,
        amountDisplayed: 0,

        filter: null,
        filterMenu: false
    }),
    computed: {
        ...mapState(["rounds", "refresh"]),
        ...mapGetters(["loading"]),

        filteredRounds() {
            if (!this.filter)
                return this.rounds;

            return this.rounds.filter(round =>
                this.normalize(this.filter).split(/\b\s+/).every(word =>
                    this.normalize(round.payer).includes(word) ||
                    this.normalize(this.$moment(round.date).format("dddd LL")).includes(word)
                ));
        },
        visibleRounds() {
            return this.filteredRounds.slice(0, this.amountDisplayed);
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
            return this.$moment(date1).isSame(date2, "day");
        },
        swapDeleted(index) {
            this.rounds[index].deleted = !this.rounds[index].deleted;
            this.changedRounds[index] = !this.changedRounds[index];
        },
        loadMore() {
            this.amountDisplayed += 10;
            if (this.filteredRounds.length > 0 && this.filteredRounds.length < this.amountDisplayed)
                this.amountDisplayed = this.filteredRounds.length;
        },
        normalize(str) {
            return str.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        },
    },
    watch: {
        filterMenu(value) {
            if (value)
                setTimeout(() => this.$refs.input.focus(), 100);
        },
        filter() {
            this.amountDisplayed = 0;
            this.loadMore();
        }
    },
    created() {
        this.onRefresh(() => {
            this.fetchRounds(this.$route.params.id);
            this.fetchGroup(this.$route.params.id);
        });

        this.refresh();
    }
}
</script>

<style scoped>
.v-form header {
  margin: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

h1 {
  margin: 0;
}

.headline {
  word-break: normal;
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

.progress {
  display: block;
  margin: 20px;
  text-align: center;
}

</style>

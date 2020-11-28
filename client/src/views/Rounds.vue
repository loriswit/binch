<template>
  <div>
    <round-dialog v-model="dialog" :round="selectedRound"/>

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
              :placeholder="$t('rounds.filter.label')"
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

          <v-list-item :class="{ deleted: round.deleted, large: round.description }">

            <v-list-item-content class="round">
              <v-list-item-title class="round-title">
                <span class="round-payer">{{ round.payer }}</span>
                <v-icon class="arrow-icon">mdi-chevron-right</v-icon>
                <span class="round-drinks">
                  {{ drinksCount(round) }} &times; {{ (parseFloat(round.price) / 100).toFixed(2) }}
                </span>
              </v-list-item-title>

              <v-list-item-subtitle class="subtitle" v-if="round.description">
                <v-icon class="subtitle-icon">mdi-card-text-outline</v-icon>
                <span>{{ round.description }}</span>
              </v-list-item-subtitle>

              <v-list-item-subtitle class="subtitle">
                <v-icon class="subtitle-icon">mdi-clock-outline</v-icon>
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

      <scroll-progress v-if="visibleRounds.length < filteredRounds.length"/>

      <Buttons :text="$t('button.save')" :valid="changedRounds.some(x => x)"/>
    </v-form>
  </div>
</template>

<script>
import "@/assets/css/form.css"
import Buttons from "@/components/Buttons"
import {mapActions, mapState, mapMutations, mapGetters} from "vuex";
import RoundDialog from "@/components/RoundDialog";
import ScrollProgress from "@/components/ScrollProgress";

export default {
    name: "Rounds",
    components: {ScrollProgress, RoundDialog, Buttons},
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
                    (round.description && this.normalize(round.description).includes(word)) ||
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

.v-form h1 {
  margin: 0;
}

.v-form .v-list {
  padding: 0;
  border-top: none;
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

.subtitle {
  display: flex;
  align-items: center;
}

.subtitle > span {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.subtitle-icon {
  font-size: 0.9em;
  line-height: 1.5;
  margin-right: 4px;
}

.deleted {
  background-color: whitesmoke;
}

.large {
  min-height: 80px;
}

.deleted .round-title .round-payer,
.deleted .round-title .round-drinks {
  color: grey;
  text-decoration: line-through;
}

</style>
